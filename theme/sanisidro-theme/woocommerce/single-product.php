<?php
/**
 * Campo San Isidro — Single Product
 */
get_header();
while (have_posts()) : the_post();
    global $product;
    $product    = wc_get_product(get_the_ID());
    $img_id     = $product->get_image_id();
    $gallery    = $product->get_gallery_image_ids();
    $price      = $product->get_price();
    $desc       = $product->get_description();
    $short_desc = $product->get_short_description();
    $terms      = get_the_terms($product->get_id(), 'product_cat');
    $cat_name   = $terms && !is_wp_error($terms) ? $terms[0]->name : '';
    $cat_link   = $terms ? get_term_link($terms[0]) : '#';
    $in_stock   = $product->is_in_stock();

    // Related products
    $related_ids = wc_get_related_products($product->get_id(), 5);
    $related     = array_map('wc_get_product', $related_ids);
?>

<!-- ══════════ BREADCRUMB ══════════ -->
<div class="csi-breadcrumb">
    <div class="csi-container">
        <a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a>
        <span class="csi-breadcrumb__sep">›</span>
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Productos</a>
        <?php if ($cat_name): ?>
        <span class="csi-breadcrumb__sep">›</span>
        <a href="<?php echo esc_url($cat_link); ?>"><?php echo esc_html($cat_name); ?></a>
        <?php endif; ?>
        <span class="csi-breadcrumb__sep">›</span>
        <span><?php echo esc_html(get_the_title()); ?></span>
    </div>
</div>

<!-- ══════════ PRODUCT MAIN ══════════ -->
<section class="csi-product-single">
    <div class="csi-container">
        <div class="csi-product-single__grid">

            <!-- Imagen -->
            <div class="csi-product-single__gallery">
                <div class="csi-product-single__main-img">
                    <?php if ($img_id): ?>
                        <?php echo wp_get_attachment_image($img_id, 'large', false, ['class' => 'csi-psg-img']); ?>
                    <?php else: ?>
                        <div class="csi-psg-placeholder">🥩</div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($gallery)): ?>
                <div class="csi-product-single__thumbs">
                    <?php
                    $all_imgs = array_merge([$img_id], $gallery);
                    foreach (array_slice($all_imgs, 0, 4) as $gid):
                    ?>
                    <div class="csi-psg-thumb">
                        <?php echo wp_get_attachment_image($gid, 'thumbnail'); ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Info -->
            <div class="csi-product-single__info">
                <?php if ($cat_name): ?>
                <div class="csi-product-single__cat"><?php echo esc_html($cat_name); ?></div>
                <?php endif; ?>

                <h1 class="csi-product-single__title"><?php the_title(); ?></h1>

                <?php if ($short_desc): ?>
                <div class="csi-product-single__short-desc"><?php echo wp_kses_post($short_desc); ?></div>
                <?php endif; ?>

                <div class="csi-product-single__price">
                    <span class="csi-psp-label">Precio</span>
                    <span class="csi-psp-amount">$ <?php echo number_format((float)$price, 0, ',', '.'); ?></span>
                </div>

                <!-- Stock -->
                <div class="csi-product-single__stock <?php echo $in_stock ? 'is-stock' : 'is-nostock'; ?>">
                    <span class="csi-pss-dot"></span>
                    <?php echo $in_stock ? 'En stock' : 'Sin stock'; ?>
                </div>

                <!-- Qty + Add to cart -->
                <?php if ($product->is_purchasable() && $in_stock): ?>
                <form class="csi-product-single__form" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype="multipart/form-data">
                    <div class="csi-product-single__actions">
                        <div class="csi-qty-wrap">
                            <button type="button" class="csi-qty-btn" data-action="minus">−</button>
                            <input type="number" class="csi-qty-input" name="quantity" value="1" min="1" max="99" />
                            <button type="button" class="csi-qty-btn" data-action="plus">+</button>
                        </div>
                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="csi-product-single__btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
                            Agregar al carrito
                        </button>
                    </div>
                </form>
                <?php endif; ?>

                <!-- Badges -->
                <div class="csi-product-single__badges">
                    <div class="csi-psb-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        Envío refrigerado
                    </div>
                    <div class="csi-psb-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Elaboración artesanal
                    </div>
                    <div class="csi-psb-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Sin conservantes artificiales
                    </div>
                </div>
            </div>
        </div>

        <!-- Descripción completa -->
        <?php if ($desc): ?>
        <div class="csi-product-single__desc">
            <h2 class="csi-psd-title">Descripción</h2>
            <div class="csi-psd-body"><?php echo wp_kses_post($desc); ?></div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ══════════ RELATED PRODUCTS ══════════ -->
<?php if (!empty($related)): ?>
<section class="csi-section csi-section--dark" id="relacionados">
    <div class="csi-container">
        <div class="csi-section-header">
            <div class="csi-section-label">También te puede interesar</div>
            <h2 class="csi-section-title">Productos relacionados</h2>
            <div class="csi-section-line"></div>
        </div>
        <div class="csi-carousel-wrap">
            <button class="csi-carousel__btn" id="csi-related-prev" aria-label="Anterior" disabled>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <div class="csi-carousel__viewport">
                <div class="csi-carousel__track" id="csi-related-grid">
                    <?php foreach ($related as $i => $rp):
                        if (!$rp) continue;
                        $rterms  = get_the_terms($rp->get_id(), 'product_cat');
                        $rimg_id = $rp->get_image_id();
                    ?>
                    <div class="csi-product-card">
                        <a href="<?php echo esc_url(get_permalink($rp->get_id())); ?>" style="display:block;text-decoration:none;">
                            <div class="csi-product-img csi-product-img--gradient-<?php echo ($i % 4) + 1; ?>">
                                <?php if ($rimg_id): echo wp_get_attachment_image($rimg_id, 'medium', false, ['loading'=>'lazy']); else: ?><span style="font-size:48px;">🥩</span><?php endif; ?>
                                <div class="csi-product-img__fade"></div>
                            </div>
                        </a>
                        <div class="csi-product-body">
                            <div class="csi-product-cat"><?php echo esc_html($rterms ? $rterms[0]->name : ''); ?></div>
                            <a href="<?php echo esc_url(get_permalink($rp->get_id())); ?>" style="text-decoration:none;"><div class="csi-product-name"><?php echo esc_html($rp->get_name()); ?></div></a>
                            <div class="csi-product-bottom">
                                <div class="csi-product-price"><span>$ </span><?php echo number_format((float)$rp->get_price(), 0, ',', '.'); ?></div>
                                <?php if ($rp->is_purchasable() && $rp->is_in_stock()): ?>
                                <a href="<?php echo esc_url('?add-to-cart=' . $rp->get_id()); ?>" class="csi-add-btn ajax_add_to_cart" data-product_id="<?php echo esc_attr($rp->get_id()); ?>" rel="nofollow"><span class="csi-add-btn__icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span><span class="csi-add-btn__text">Agregar</span></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="csi-carousel__btn" id="csi-related-next" aria-label="Siguiente">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
        <div class="csi-carousel-footer">
            <div class="csi-carousel-dots" id="csi-related-dots"></div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php endwhile; ?>
<?php get_footer(); ?>
