<?php
/**
 * Template Name: Recetas
 */
get_header();
$posts = get_posts([
    'post_type'      => 'post',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'category_name'  => 'recetas',
    'orderby'        => 'date',
    'order'          => 'DESC',
]);
?>

<!-- ══════════ HERO ══════════ -->
<div class="csi-page-hero csi-page-hero--short">
    <div class="csi-page-hero__overlay" style="background:linear-gradient(135deg,rgba(95,28,23,0.85),rgba(15,15,15,0.9))"></div>
    <div class="csi-container csi-page-hero__content">
        <div class="csi-feature__label">De nuestra cocina a la tuya</div>
        <h1 class="csi-page-hero__title">Recetas</h1>
        <p class="csi-page-hero__sub">Ideas para cocinar con nuestros embutidos y fiambres artesanales</p>
    </div>
</div>

<!-- ══════════ GRID DE RECETAS ══════════ -->
<section class="csi-section">
    <div class="csi-container">
        <div class="csi-recetas-grid">
            <?php foreach ($posts as $i => $post): ?>
            <article class="csi-receta-card">
                <a href="<?php echo esc_url(get_permalink($post->ID)); ?>" class="csi-receta-card__img-wrap">
                    <?php
                    $thumb = get_the_post_thumbnail_url($post->ID, 'medium_large');
                    $imgs  = [1,2,3,4,5,6,7,8,9,10];
                    $fallback = get_template_directory_uri() . '/assets/' . $imgs[$i % 10] . '.jpg';
                    ?>
                    <img src="<?php echo $thumb ?: $fallback; ?>" alt="<?php echo esc_attr($post->post_title); ?>" loading="lazy" />
                    <div class="csi-receta-card__overlay"></div>
                </a>
                <div class="csi-receta-card__body">
                    <div class="csi-receta-card__meta">
                        <span><?php echo get_the_date('d M Y', $post->ID); ?></span>
                    </div>
                    <h2 class="csi-receta-card__title">
                        <a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo esc_html($post->post_title); ?></a>
                    </h2>
                    <p class="csi-receta-card__excerpt"><?php echo esc_html($post->post_excerpt); ?></p>
                    <a href="<?php echo esc_url(get_permalink($post->ID)); ?>" class="csi-receta-card__link">
                        Ver receta
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
