<?php
get_header();

$slides = get_posts([
    'post_type'      => 'csi_slide',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
]);
?>

<!-- ══════════ HERO SLIDER ══════════ -->
<div class="csi-hero" id="csi-hero">

    <?php foreach ($slides as $i => $slide):
        $title    = str_replace('\n', '<br>', esc_html($slide->post_title));
        $subtitle = esc_html(get_post_meta($slide->ID, '_csi_subtitle', true));
        $cta_text = esc_html(get_post_meta($slide->ID, '_csi_cta_text', true));
        $cta_url  = esc_url(get_post_meta($slide->ID, '_csi_cta_url', true) ?: get_permalink(wc_get_page_id('shop')));
        $color    = esc_attr(get_post_meta($slide->ID, '_csi_title_color', true) ?: 'salmon');
        $img_url  = get_the_post_thumbnail_url($slide->ID, 'full');
        $bg_style = $img_url
            ? 'background-image:linear-gradient(to right,rgba(17,17,17,.85) 40%,rgba(17,17,17,.3) 100%),url(' . esc_url($img_url) . ');background-size:cover;background-position:center;'
            : '';
    ?>
    <div class="csi-hero__slide<?php echo $i === 0 ? ' is-active' : ''; ?>"
         data-index="<?php echo $i; ?>"
         <?php if ($bg_style): ?>style="<?php echo $bg_style; ?>"<?php endif; ?>>
        <div class="csi-hero__content">
            <div class="csi-hero__title csi-hero__title--<?php echo $color; ?>"><?php echo $title; ?></div>
            <?php if ($subtitle): ?><div class="csi-hero__sub"><?php echo $subtitle; ?></div><?php endif; ?>
            <?php if ($cta_text): ?>
            <a href="<?php echo $cta_url; ?>" class="csi-hero__cta csi-hero__cta--<?php echo $color; ?>">
                <?php echo $cta_text; ?> <span>→</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Arrows -->
    <button class="csi-hero__arrow csi-hero__arrow--prev" id="csi-prev" aria-label="Anterior">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <button class="csi-hero__arrow csi-hero__arrow--next" id="csi-next" aria-label="Siguiente">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>

    <!-- Dots (generados por JS) -->
    <div class="csi-hero__dots" id="csi-hero-dots"></div>
</div>

<!-- ══════════ PRODUCTOS — TODOS ══════════ -->
<section class="csi-section" id="productos">
    <div class="csi-container">

        <!-- Header: título izquierda + tabs derecha -->
        <div class="csi-products-header">
            <div class="csi-products-header__left">
                <h2 class="csi-section-title">Nuestros Productos</h2>
            </div>
            <div class="csi-products-header__tabs" id="csi-cat-pills">
                <button class="csi-tab is-active" data-cat="todos">Todos</button>
                <?php
                $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0]);
                foreach ($cats as $cat) {
                    if ($cat->name !== 'Uncategorized') {
                        echo '<button class="csi-tab" data-cat="' . esc_attr($cat->slug) . '">' . esc_html($cat->name) . '</button>';
                    }
                }
                ?>
            </div>
        </div>

        <!-- Carrusel -->
        <div class="csi-carousel-wrap">
            <button class="csi-carousel__btn" id="csi-products-prev" aria-label="Anterior" disabled>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
            </button>

            <div class="csi-carousel__viewport">
                <div class="csi-carousel__track" id="csi-product-grid">
                    <?php
                    $all_products = csi_get_all_products(60);
                    foreach ($all_products as $i => $product) {
                        $terms     = get_the_terms($product->get_id(), 'product_cat');
                        $cat_slugs = $terms ? implode(' ', array_map(fn($t) => $t->slug, $terms)) : '';
                        $img_id    = $product->get_image_id();
                        ?>
                        <div class="csi-product-card" data-cats="<?php echo esc_attr($cat_slugs); ?>">
                            <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" style="display:block;text-decoration:none;">
                                <div class="csi-product-img csi-product-img--gradient-<?php echo ($i % 4) + 1; ?>">
                                    <?php if ($img_id): echo wp_get_attachment_image($img_id, 'medium', false, ['loading'=>'lazy']); else: ?><span style="font-size:48px;">🥩</span><?php endif; ?>
                                    <div class="csi-product-img__fade"></div>
                                </div>
                            </a>
                            <div class="csi-product-body">
                                <div class="csi-product-cat"><?php echo esc_html($terms ? $terms[0]->name : ''); ?></div>
                                <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" style="text-decoration:none;">
                                    <div class="csi-product-name"><?php echo esc_html($product->get_name()); ?></div>
                                </a>
                                <div class="csi-product-bottom">
                                    <div class="csi-product-price"><span>$ </span><?php echo number_format((float)$product->get_price(), 0, ',', '.'); ?></div>
                                    <?php if ($product->is_purchasable() && $product->is_in_stock()): ?>
                                        <a href="<?php echo esc_url('?add-to-cart=' . $product->get_id()); ?>" class="csi-add-btn ajax_add_to_cart" data-product_id="<?php echo esc_attr($product->get_id()); ?>" rel="nofollow"><span class="csi-add-btn__icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span><span class="csi-add-btn__text">Agregar</span></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <button class="csi-carousel__btn" id="csi-products-next" aria-label="Siguiente">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>

        <!-- Paginador -->
        <div class="csi-carousel-footer">
            <div class="csi-carousel-dots" id="csi-products-dots"></div>
        </div>

    </div>
</section>

<!-- ══════════ FEATURE: HISTORIA ══════════ -->
<section class="csi-feature" id="historia">
    <div class="csi-feature__visual csi-feature__visual--historia csi-historia-slider">
        <div class="csi-hs-img csi-hs-img--1" style="background-image:url('<?php echo get_template_directory_uri(); ?>/assets/galeria-mesa-de-trabajo-1-copia-mesa-de-trabajo-1-copia.jpg')"></div>
        <div class="csi-hs-img csi-hs-img--2" style="background-image:url('<?php echo get_template_directory_uri(); ?>/assets/galeria-7.jpg')"></div>
    </div>
    <div class="csi-feature__content">
        <div class="csi-feature__label">Garupá, Misiones — Desde 1994</div>
        <h2 class="csi-feature__title">Más de 30 años elaborando embutidos en el corazón de Misiones</h2>
        <p class="csi-feature__text">
            Campo San Isidro nació bajo el ala del Frigorífico El Abasto, fundado por Arturo Panozzo,
            para satisfacer la demanda de embutidos de calidad en la región. Desde nuestros primeros
            chorizos hasta la expansión a fiambres de pasta fina y salamines, cada producto refleja
            el compromiso con la tradición artesanal y la materia prima local de Misiones.
        </p>
        <a href="#" class="csi-feature__btn">Conocé Nuestra Historia →</a>
    </div>
</section>

<!-- ══════════ LÍNEA PARRILLERA ══════════ -->
<section class="csi-section" id="parrillera">
    <div class="csi-container">
        <div class="csi-section-header">
            <div class="csi-section-label">Lo más elegido</div>
            <h2 class="csi-section-title">Línea Parrillera</h2>
            <div class="csi-section-line"></div>
        </div>
        <div class="csi-carousel-wrap">
            <button class="csi-carousel__btn" id="csi-parrillera-prev" aria-label="Anterior" disabled>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <div class="csi-carousel__viewport">
                <div class="csi-carousel__track" id="csi-parrillera-grid">
                    <?php
                    $parrilla = csi_get_products_by_cat('embutidos', 20);
                    foreach ($parrilla as $i => $product):
                        $terms  = get_the_terms($product->get_id(), 'product_cat');
                        $img_id = $product->get_image_id();
                    ?>
                    <div class="csi-product-card">
                        <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" style="display:block;text-decoration:none;">
                            <div class="csi-product-img csi-product-img--gradient-<?php echo ($i % 4) + 1; ?>">
                                <?php if ($img_id): echo wp_get_attachment_image($img_id, 'medium', false, ['loading'=>'lazy']); else: ?><span style="font-size:48px;">🔥</span><?php endif; ?>
                                <div class="csi-product-img__fade"></div>
                            </div>
                        </a>
                        <div class="csi-product-body">
                            <div class="csi-product-cat"><?php echo esc_html($terms ? $terms[0]->name : 'Embutidos'); ?></div>
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
            </div>
            <button class="csi-carousel__btn" id="csi-parrillera-next" aria-label="Siguiente">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
        <div class="csi-carousel-footer">
            <div class="csi-carousel-dots" id="csi-parrillera-dots"></div>
        </div>
    </div>
</section>

<!-- ══════════ INFO BANNER ══════════ -->
<div class="csi-info-banner" id="info-banner">
    <div class="csi-container">
        <div class="csi-info-grid" id="csi-info-grid">
            <div class="csi-info-item">
                <div class="csi-info-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <div class="csi-info-texts">
                    <div class="csi-info-title">Envío Refrigerado</div>
                    <div class="csi-info-desc">A todo el país con cadena de frío</div>
                </div>
            </div>
            <div class="csi-info-item">
                <div class="csi-info-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="csi-info-texts">
                    <div class="csi-info-title">Pago Seguro</div>
                    <div class="csi-info-desc">Todas las tarjetas y transferencia</div>
                </div>
            </div>
            <div class="csi-info-item">
                <div class="csi-info-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                </div>
                <div class="csi-info-texts">
                    <div class="csi-info-title">Calidad Premium</div>
                    <div class="csi-info-desc">Elaboración artesanal desde 1994</div>
                </div>
            </div>
            <div class="csi-info-item">
                <div class="csi-info-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                </div>
                <div class="csi-info-texts">
                    <div class="csi-info-title">Atención Directa</div>
                    <div class="csi-info-desc">WhatsApp y teléfono de Lun a Sáb</div>
                </div>
            </div>
        </div>
        <div class="csi-info-dots" id="csi-info-dots"></div>
    </div>
</div>

<!-- ══════════ FIAMBRES & SALAMINES ══════════ -->
<section class="csi-section" id="fiambres">
    <div class="csi-container">
        <div class="csi-section-header">
            <div class="csi-section-label">Selección Gourmet</div>
            <h2 class="csi-section-title">Fiambres &amp; Salamines</h2>
            <div class="csi-section-line"></div>
        </div>
        <div class="csi-carousel-wrap">
            <button class="csi-carousel__btn" id="csi-fiambres-prev" aria-label="Anterior" disabled>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <div class="csi-carousel__viewport">
                <div class="csi-carousel__track" id="csi-fiambres-grid">
                    <?php
                    $fiambres = array_merge(
                        csi_get_products_by_cat('fiambres', 20),
                        csi_get_products_by_cat('salames', 20)
                    );
                    foreach ($fiambres as $i => $product):
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
                            <div class="csi-product-cat"><?php echo esc_html($terms ? $terms[0]->name : 'Fiambres'); ?></div>
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
            </div>
            <button class="csi-carousel__btn" id="csi-fiambres-next" aria-label="Siguiente">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
        <div class="csi-carousel-footer">
            <div class="csi-carousel-dots" id="csi-fiambres-dots"></div>
        </div>
    </div>
</section>

<!-- ══════════ TESTIMONIALS ══════════ -->
<section class="csi-section csi-section--dark" id="testimonios">
    <div class="csi-container">
        <div class="csi-section-header">
            <div class="csi-section-label">Testimonios</div>
            <h2 class="csi-section-title">Lo que dicen nuestros clientes</h2>
            <div class="csi-section-line"></div>
        </div>
        <div class="csi-testimonial-grid">
            <?php
            $testimonials = [
                ['text' => 'Los chorizos parrilleros son espectaculares. Se nota la calidad y la elaboración artesanal. Desde que los descubrí no compro otra marca.', 'author' => 'Marcelo R.', 'role' => 'Posadas, Misiones'],
                ['text' => 'Los fiambres feteados al vacío llegan perfectos. El jamón cocido es increíble, tiene ese sabor casero que no encontrás en ningún lado.', 'author' => 'Carolina S.', 'role' => 'Buenos Aires'],
                ['text' => 'La rosca polaca es una delicia. Me transporta a la mesa de mis abuelos. El envío refrigerado funciona perfecto, todo llega fresco.', 'author' => 'Daniel K.', 'role' => 'Oberá, Misiones'],
            ];
            foreach ($testimonials as $t):
            ?>
            <div class="csi-testimonial-card">
                <div class="csi-testimonial-stars">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#f08060" stroke="#f08060" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <?php endfor; ?>
                </div>
                <p class="csi-testimonial-text">"<?php echo esc_html($t['text']); ?>"</p>
                <div class="csi-testimonial-author"><?php echo esc_html($t['author']); ?></div>
                <div class="csi-testimonial-role"><?php echo esc_html($t['role']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<?php get_footer(); ?>
