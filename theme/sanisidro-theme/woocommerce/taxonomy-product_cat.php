<?php
/**
 * Campo San Isidro — Product Category Archive
 */
get_header();
$term     = get_queried_object();
$cat_name = $term->name;
$cat_desc = $term->description;
$products = wc_get_products([
    'status'   => 'publish',
    'limit'    => -1,
    'category' => [$term->slug],
    'orderby'  => 'date',
    'order'    => 'DESC',
]);
$all_cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0]);
?>

<!-- ══════════ CATEGORY HERO ══════════ -->
<div class="csi-cat-hero">
    <div class="csi-container">
        <div class="csi-breadcrumb csi-breadcrumb--inline">
            <a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a>
            <span class="csi-breadcrumb__sep">›</span>
            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Productos</a>
            <span class="csi-breadcrumb__sep">›</span>
            <span><?php echo esc_html($cat_name); ?></span>
        </div>
        <h1 class="csi-cat-hero__title"><?php echo esc_html($cat_name); ?></h1>
        <?php if ($cat_desc): ?>
        <p class="csi-cat-hero__desc"><?php echo esc_html($cat_desc); ?></p>
        <?php endif; ?>
        <div class="csi-cat-hero__count"><?php echo count($products); ?> productos</div>
    </div>
</div>

<!-- ══════════ FILTER PILLS ══════════ -->
<div class="csi-cat-filter-bar">
    <div class="csi-container">
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="csi-tab">Todos</a>
        <?php foreach ($all_cats as $cat):
            if ($cat->name === 'Uncategorized') continue;
        ?>
        <a href="<?php echo esc_url(get_term_link($cat)); ?>"
           class="csi-tab <?php echo $cat->slug === $term->slug ? 'is-active' : ''; ?>">
            <?php echo esc_html($cat->name); ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- ══════════ PRODUCTS GRID ══════════ -->
<section class="csi-section">
    <div class="csi-container">
        <?php if (!empty($products)): ?>
        <div class="csi-archive-info" id="csi-archive-info"></div>
        <div class="csi-archive-grid" id="csi-archive-grid">
            <?php foreach ($products as $i => $product):
                $terms  = get_the_terms($product->get_id(), 'product_cat');
                $img_id = $product->get_image_id();
            ?>
            <div class="csi-product-card">
                <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" style="display:block;text-decoration:none;">
                    <div class="csi-product-img csi-product-img--gradient-<?php echo ($i % 4) + 1; ?>">
                        <?php if ($img_id): echo wp_get_attachment_image($img_id, 'medium', false, ['loading'=>'lazy']); else: ?><span style="font-size:48px;">🥩</span><?php endif; ?>
                        <div class="csi-product-img__fade"></div>
                    </div>
                </a>
                <div class="csi-product-body">
                    <div class="csi-product-cat"><?php echo esc_html($terms ? $terms[0]->name : ''); ?></div>
                    <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" style="text-decoration:none;"><div class="csi-product-name"><?php echo esc_html($product->get_name()); ?></div></a>
                    <div class="csi-product-bottom">
                        <div class="csi-product-price"><span>$ </span><?php echo number_format((float)$product->get_price(), 0, ',', '.'); ?></div>
                        <?php if ($product->is_purchasable() && $product->is_in_stock()): ?>
                        <a href="<?php echo esc_url('?add-to-cart=' . $product->get_id()); ?>" class="csi-add-btn ajax_add_to_cart" data-product_id="<?php echo esc_attr($product->get_id()); ?>" rel="nofollow"><span class="csi-add-btn__icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span><span class="csi-add-btn__text">Agregar</span></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="csi-archive-pagination" id="csi-archive-pagination"></div>
        <?php else: ?>
        <p style="color:var(--text-muted);text-align:center;padding:60px 0;">No hay productos en esta categoría.</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
