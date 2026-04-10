<?php
/**
 * Campo San Isidro — Single Post (Recetas)
 */
get_header();
while (have_posts()) : the_post();
$thumb = get_the_post_thumbnail_url(get_the_ID(), 'large');
$fallback = get_template_directory_uri() . '/assets/galeria-7.jpg';
?>

<!-- ══════════ RECIPE HERO ══════════ -->
<div class="csi-page-hero csi-page-hero--recipe">
    <div class="csi-page-hero__bg" style="background-image:url('<?php echo $thumb ?: $fallback; ?>')"></div>
    <div class="csi-page-hero__overlay"></div>
    <div class="csi-container csi-page-hero__content">
        <div class="csi-breadcrumb--inline" style="margin-bottom:16px">
            <a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a>
            <span class="csi-breadcrumb__sep">›</span>
            <a href="<?php echo esc_url(home_url('/recetas')); ?>">Recetas</a>
            <span class="csi-breadcrumb__sep">›</span>
            <span><?php the_title(); ?></span>
        </div>
        <div class="csi-feature__label"><?php echo esc_html(get_the_excerpt()); ?></div>
        <h1 class="csi-page-hero__title"><?php the_title(); ?></h1>
        <div class="csi-recipe-meta">
            <span><?php echo get_the_date('d M Y'); ?></span>
            <span class="csi-breadcrumb__sep">·</span>
            <span><?php echo ceil(str_word_count(strip_tags(get_the_content())) / 200); ?> min de lectura</span>
        </div>
    </div>
</div>

<!-- ══════════ RECIPE CONTENT ══════════ -->
<section class="csi-section">
    <div class="csi-container">
        <div class="csi-recipe-layout">
            <div class="csi-recipe-content">
                <?php the_content(); ?>
            </div>
            <aside class="csi-recipe-aside">
                <div class="csi-recipe-aside__card">
                    <h3 class="csi-recipe-aside__title">Productos usados</h3>
                    <?php
                    $products = wc_get_products(['status' => 'publish', 'limit' => 3, 'orderby' => 'rand']);
                    foreach ($products as $p):
                        $img_id = $p->get_image_id();
                    ?>
                    <a href="<?php echo esc_url(get_permalink($p->get_id())); ?>" class="csi-recipe-prod">
                        <div class="csi-recipe-prod__img">
                            <?php if ($img_id): echo wp_get_attachment_image($img_id, 'thumbnail'); endif; ?>
                        </div>
                        <div class="csi-recipe-prod__info">
                            <div class="csi-recipe-prod__name"><?php echo esc_html($p->get_name()); ?></div>
                            <div class="csi-recipe-prod__price">$ <?php echo number_format((float)$p->get_price(), 0, ',', '.'); ?></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="csi-recipe-aside__cta">Ver todos los productos →</a>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php endwhile; ?>
<?php get_footer(); ?>
