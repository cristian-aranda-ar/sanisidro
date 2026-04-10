<?php
/**
 * Campo San Isidro — functions.php
 */

// ═══════════════════════════════════════
// CPT: SLIDES
// ═══════════════════════════════════════
add_action('init', function() {
    register_post_type('csi_slide', [
        'labels' => [
            'name'               => 'Slides',
            'singular_name'      => 'Slide',
            'add_new'            => 'Agregar slide',
            'add_new_item'       => 'Agregar nuevo slide',
            'edit_item'          => 'Editar slide',
            'view_item'          => 'Ver slide',
            'all_items'          => 'Todos los slides',
            'search_items'       => 'Buscar slides',
            'not_found'          => 'No se encontraron slides.',
            'menu_name'          => 'Slider',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-images-alt2',
        'supports'     => ['title', 'thumbnail'],
        'menu_position' => 5,
    ]);
});

// Meta box con los campos del slide
add_action('add_meta_boxes', function() {
    add_meta_box(
        'csi_slide_fields',
        'Configuración del slide',
        'csi_slide_meta_box',
        'csi_slide',
        'normal',
        'high'
    );
});

function csi_slide_meta_box($post) {
    wp_nonce_field('csi_slide_save', 'csi_slide_nonce');
    $subtitle    = get_post_meta($post->ID, '_csi_subtitle', true);
    $cta_text    = get_post_meta($post->ID, '_csi_cta_text', true);
    $cta_url     = get_post_meta($post->ID, '_csi_cta_url', true);
    $title_color = get_post_meta($post->ID, '_csi_title_color', true) ?: 'salmon';
    ?>
    <table class="form-table" style="width:100%">
        <tr>
            <th style="width:160px;padding:12px 0"><label for="csi_subtitle"><strong>Subtítulo</strong></label></th>
            <td><input type="text" id="csi_subtitle" name="csi_subtitle" value="<?php echo esc_attr($subtitle); ?>" style="width:100%" placeholder="Ej: Elaboración artesanal desde 1978" /></td>
        </tr>
        <tr>
            <th style="padding:12px 0"><label for="csi_cta_text"><strong>Texto del botón</strong></label></th>
            <td><input type="text" id="csi_cta_text" name="csi_cta_text" value="<?php echo esc_attr($cta_text); ?>" style="width:100%" placeholder="Ej: Ver Productos" /></td>
        </tr>
        <tr>
            <th style="padding:12px 0"><label for="csi_cta_url"><strong>URL del botón</strong></label></th>
            <td><input type="url" id="csi_cta_url" name="csi_cta_url" value="<?php echo esc_attr($cta_url); ?>" style="width:100%" placeholder="https://..." /></td>
        </tr>
        <tr>
            <th style="padding:12px 0"><label><strong>Color del título</strong></label></th>
            <td style="display:flex;gap:20px;align-items:center;padding-top:6px">
                <?php
                $colors = [
                    'salmon'  => ['label' => 'Salmón',   'hex' => '#f08060'],
                    'beige'   => ['label' => 'Beige',    'hex' => '#d6cfc7'],
                    'red'     => ['label' => 'Rojo',     'hex' => '#e8453a'],
                    'white'   => ['label' => 'Blanco',   'hex' => '#f0ebe5'],
                ];
                foreach ($colors as $val => $c): ?>
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                    <input type="radio" name="csi_title_color" value="<?php echo esc_attr($val); ?>" <?php checked($title_color, $val); ?> />
                    <span style="display:inline-block;width:18px;height:18px;border-radius:50%;background:<?php echo esc_attr($c['hex']); ?>;border:2px solid rgba(0,0,0,.15)"></span>
                    <?php echo esc_html($c['label']); ?>
                </label>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>
    <p style="color:#888;margin-top:8px;font-size:12px">
        💡 La imagen de fondo se define en <strong>Imagen destacada</strong> (panel derecho).<br>
        El título del slide se define en el campo <strong>Título</strong> de arriba.<br>
        Podés usar <code>\n</code> en el título para saltar de línea.
    </p>
    <?php
}

add_action('save_post_csi_slide', function($post_id) {
    if (!isset($_POST['csi_slide_nonce']) || !wp_verify_nonce($_POST['csi_slide_nonce'], 'csi_slide_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = [
        '_csi_subtitle'    => 'csi_subtitle',
        '_csi_cta_text'    => 'csi_cta_text',
        '_csi_cta_url'     => 'csi_cta_url',
        '_csi_title_color' => 'csi_title_color',
    ];
    foreach ($fields as $meta_key => $post_key) {
        if (isset($_POST[$post_key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$post_key]));
        }
    }
    // URL con sanitize especial
    if (isset($_POST['csi_cta_url'])) {
        update_post_meta($post_id, '_csi_cta_url', esc_url_raw($_POST['csi_cta_url']));
    }
});

// Columnas en el listado de slides
add_filter('manage_csi_slide_posts_columns', function($cols) {
    return [
        'cb'          => $cols['cb'],
        'title'       => 'Título',
        'thumbnail'   => 'Imagen',
        'subtitle'    => 'Subtítulo',
        'cta'         => 'Botón',
        'date'        => 'Orden / Fecha',
    ];
});
add_action('manage_csi_slide_posts_custom_column', function($col, $post_id) {
    if ($col === 'thumbnail') {
        echo get_the_post_thumbnail($post_id, [80, 45]);
    }
    if ($col === 'subtitle') {
        echo esc_html(get_post_meta($post_id, '_csi_subtitle', true));
    }
    if ($col === 'cta') {
        echo esc_html(get_post_meta($post_id, '_csi_cta_text', true));
    }
}, 10, 2);

// WooCommerce support
add_action('after_setup_theme', function() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption']);

    register_nav_menus([
        'primary' => 'Menú Principal',
    ]);
});

// Enqueue Google Fonts + main stylesheet + JS
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'csi-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600;700&display=swap',
        [], null
    );
    wp_enqueue_style('csi-style', get_stylesheet_uri(), ['csi-fonts'], '1.0.0');
    wp_enqueue_script('csi-main', get_template_directory_uri() . '/assets/main.js', [], '1.0.0', true);
});

// Remove default WooCommerce styles
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

// WooCommerce: remove breadcrumbs and default wrappers on shop
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

// Custom WooCommerce loop item template hook
function csi_get_product_category_label($product) {
    $terms = get_the_terms($product->get_id(), 'product_cat');
    if ($terms && !is_wp_error($terms)) {
        return esc_html($terms[0]->name);
    }
    return '';
}

function csi_get_gradient_class($index) {
    $classes = ['gradient-1','gradient-2','gradient-3','gradient-4'];
    return $classes[$index % 4];
}

// Helper: render a product card
function csi_product_card($product, $index = 0) {
    $id       = $product->get_id();
    $name     = $product->get_name();
    $price    = $product->get_price();
    $cat      = csi_get_product_category_label($product);
    $img_id   = $product->get_image_id();
    $grad     = csi_get_gradient_class($index);
    $url      = get_permalink($id);
    $cart_url = wc_get_cart_url();
    $nonce    = wp_create_nonce('add-to-cart');
    ?>
    <div class="csi-product-card" data-category="<?php echo esc_attr($cat); ?>">
        <a href="<?php echo esc_url($url); ?>" style="display:block;text-decoration:none;">
            <div class="csi-product-img csi-product-img--<?php echo $grad; ?>">
                <?php if ($img_id): ?>
                    <?php echo wp_get_attachment_image($img_id, 'medium', false, ['loading' => 'lazy']); ?>
                <?php else: ?>
                    <span style="font-size:64px;">🥩</span>
                <?php endif; ?>
                <div class="csi-product-img__fade"></div>
            </div>
        </a>
        <div class="csi-product-body">
            <div class="csi-product-cat"><?php echo esc_html($cat); ?></div>
            <a href="<?php echo esc_url($url); ?>" style="text-decoration:none;">
                <div class="csi-product-name"><?php echo esc_html($name); ?></div>
            </a>
            <div class="csi-product-bottom">
                <div class="csi-product-price">
                    <span>$ </span><?php echo number_format((float)$price, 0, ',', '.'); ?>
                </div>
                <?php if ($product->is_purchasable() && $product->is_in_stock()): ?>
                    <a href="<?php echo esc_url('?add-to-cart=' . $id); ?>"
                       class="csi-add-btn ajax_add_to_cart"
                       data-product_id="<?php echo esc_attr($id); ?>"
                       rel="nofollow">
                        Agregar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

// Helper: get products by category slug
function csi_get_products_by_cat($cat_slug, $limit = 12) {
    return wc_get_products([
        'status'   => 'publish',
        'limit'    => $limit,
        'category' => [$cat_slug],
        'orderby'  => 'date',
        'order'    => 'DESC',
    ]);
}

// Helper: get all products
function csi_get_all_products($limit = 20) {
    return wc_get_products([
        'status'  => 'publish',
        'limit'   => $limit,
        'orderby' => 'date',
        'order'   => 'DESC',
    ]);
}

// WooCommerce cart count for header
function csi_cart_count() {
    if (function_exists('WC')) {
        return WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    }
    return 0;
}

// Add WooCommerce cart fragments for AJAX update
add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    ob_start();
    $count = WC()->cart->get_cart_contents_count();
    echo '<span class="csi-cart-badge" id="csi-cart-count">' . esc_html($count) . '</span>';
    $fragments['#csi-cart-count'] = ob_get_clean();
    return $fragments;
});
