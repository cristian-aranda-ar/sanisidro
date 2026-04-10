<?php
/**
 * Campo San Isidro — My Account
 */
defined('ABSPATH') || exit;
$current_user = wp_get_current_user();
?>
<?php get_header(); ?>

<!-- ══════════ ACCOUNT HERO ══════════ -->
<div class="csi-account-hero">
    <div class="csi-container">
        <div class="csi-account-hero__inner">
            <div class="csi-account-hero__avatar">
                <?php echo get_avatar($current_user->ID, 72, '', '', ['class' => 'csi-account-avatar']); ?>
            </div>
            <div>
                <div class="csi-account-hero__label">Bienvenido/a</div>
                <h1 class="csi-account-hero__name"><?php echo esc_html($current_user->display_name ?: $current_user->user_login); ?></h1>
                <div class="csi-account-hero__email"><?php echo esc_html($current_user->user_email); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════ ACCOUNT BODY ══════════ -->
<section class="csi-section csi-account-section">
    <div class="csi-container">
        <div class="csi-account-layout">

            <!-- Sidebar nav -->
            <aside class="csi-account-sidebar">
                <?php
                $nav_items = wc_get_account_menu_items();
                $current   = WC()->query->get_current_endpoint();
                ?>
                <nav class="csi-account-nav">
                    <?php foreach ($nav_items as $endpoint => $label):
                        $url     = wc_get_account_endpoint_url($endpoint);
                        $active  = ($endpoint === 'dashboard' && !$current) || $endpoint === $current;
                    ?>
                    <a href="<?php echo esc_url($url); ?>"
                       class="csi-account-nav__link <?php echo $active ? 'is-active' : ''; ?>">
                        <?php echo esc_html($label); ?>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                    <?php endforeach; ?>
                </nav>
            </aside>

            <!-- Content -->
            <div class="csi-account-content">
                <?php do_action('woocommerce_account_content'); ?>
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>
