<?php
/**
 * Template Name: Nosotros
 */
get_header();
$img_base = get_template_directory_uri() . '/assets/';
?>

<!-- ══════════ HERO NOSOTROS ══════════ -->
<div class="csi-page-hero">
    <div class="csi-page-hero__bg" style="background-image:url('<?php echo $img_base; ?>galeria-7.jpg')"></div>
    <div class="csi-page-hero__overlay"></div>
    <div class="csi-container csi-page-hero__content">
        <div class="csi-feature__label">Garupá, Misiones — Desde 1994</div>
        <h1 class="csi-page-hero__title">Nuestra Historia</h1>
        <p class="csi-page-hero__sub">Tres décadas elaborando embutidos con el alma de Misiones</p>
    </div>
</div>

<!-- ══════════ HISTORIA ══════════ -->
<section class="csi-section">
    <div class="csi-container">
        <div class="csi-nosotros-grid">
            <div class="csi-nosotros-img">
                <img src="<?php echo $img_base; ?>galeria-mesa-de-trabajo-1-copia-mesa-de-trabajo-1-copia.jpg" alt="Planta de producción" />
            </div>
            <div class="csi-nosotros-text">
                <div class="csi-section-label">Nuestros orígenes</div>
                <h2 class="csi-nosotros-title">El Frigorífico El Abasto y la familia Panozzo</h2>
                <p>Campo San Isidro nació bajo el ala del Frigorífico El Abasto, fundado por <strong>Arturo Panozzo</strong> en el corazón de Garupá, Misiones. Desde los primeros chorizos artesanales hasta la expansión a fiambres de pasta fina y salamines curados, cada producto refleja el compromiso con la tradición y la materia prima local.</p>
                <p>En 1994 consolidamos nuestra identidad propia con la marca Campo San Isidro, manteniendo los procesos artesanales que nos distinguen: curación lenta, especias naturales y sin conservantes artificiales. La receta es la misma de siempre — solo mejoramos el proceso.</p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════ VALORES ══════════ -->
<section class="csi-section csi-section--dark">
    <div class="csi-container">
        <div class="csi-section-header">
            <div class="csi-section-label">Lo que nos define</div>
            <h2 class="csi-section-title">Nuestros valores</h2>
            <div class="csi-section-line"></div>
        </div>
        <div class="csi-valores-grid">
            <div class="csi-valor-card">
                <div class="csi-valor-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3 class="csi-valor-title">Calidad artesanal</h3>
                <p class="csi-valor-desc">Cada producto pasa por un proceso de elaboración manual, sin atajos industriales. La calidad se controla en cada etapa, desde la selección de carnes hasta el embutido final.</p>
            </div>
            <div class="csi-valor-card">
                <div class="csi-valor-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <h3 class="csi-valor-title">Raíces locales</h3>
                <p class="csi-valor-desc">Trabajamos con proveedores locales de Misiones y la región NEA. Apoyamos a los productores cercanos y mantenemos una cadena de valor que fortalece la economía regional.</p>
            </div>
            <div class="csi-valor-card">
                <div class="csi-valor-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                </div>
                <h3 class="csi-valor-title">Tradición familiar</h3>
                <p class="csi-valor-desc">Las recetas originales de Arturo Panozzo se transmiten de generación en generación. Cada chorizo, cada salame, cada fiambre lleva el sello de una familia que vive para la charcutería.</p>
            </div>
            <div class="csi-valor-card">
                <div class="csi-valor-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <h3 class="csi-valor-title">Envío en frío</h3>
                <p class="csi-valor-desc">Desarrollamos un sistema propio de logística refrigerada para llevar nuestros productos a todo el país manteniendo la cadena de frío intacta desde la planta hasta tu puerta.</p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════ PROCESO ══════════ -->
<section class="csi-section">
    <div class="csi-container">
        <div class="csi-section-header">
            <div class="csi-section-label">Cómo lo hacemos</div>
            <h2 class="csi-section-title">El proceso artesanal</h2>
            <div class="csi-section-line"></div>
        </div>
        <div class="csi-proceso-grid">
            <div class="csi-paso">
                <div class="csi-paso__num">01</div>
                <h3 class="csi-paso__title">Selección de materia prima</h3>
                <p class="csi-paso__desc">Trabajamos exclusivamente con carnes de proveedores de confianza de la región. Cada lote es inspeccionado antes de ingresar a la planta.</p>
            </div>
            <div class="csi-paso">
                <div class="csi-paso__num">02</div>
                <h3 class="csi-paso__title">Condimentado y curado</h3>
                <p class="csi-paso__desc">Las especias se mezclan a mano siguiendo las recetas originales. El curado en cámara puede durar entre 24 horas y 45 días según el producto.</p>
            </div>
            <div class="csi-paso">
                <div class="csi-paso__num">03</div>
                <h3 class="csi-paso__title">Embutido artesanal</h3>
                <p class="csi-paso__desc">Usamos tripas naturales para todos nuestros embutidos. El embutido manual permite controlar el calibre y la densidad de cada pieza.</p>
            </div>
            <div class="csi-paso">
                <div class="csi-paso__num">04</div>
                <h3 class="csi-paso__title">Control y despacho</h3>
                <p class="csi-paso__desc">Cada lote pasa por control de calidad interno antes de ser empacado y despachado en cadena de frío hacia todo el país.</p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════ GALERÍA ══════════ -->
<section class="csi-section csi-section--dark">
    <div class="csi-container">
        <div class="csi-section-header">
            <div class="csi-section-label">La planta</div>
            <h2 class="csi-section-title">Galería</h2>
            <div class="csi-section-line"></div>
        </div>
        <div class="csi-galeria-grid">
            <?php
            $imgs = [
                'galeria-7.jpg',
                'galeria-mesa-de-trabajo-1-copia-mesa-de-trabajo-1-copia.jpg',
                'galeria-7.jpg',
                'galeria-mesa-de-trabajo-1-copia-mesa-de-trabajo-1-copia.jpg',
            ];
            foreach ($imgs as $img): ?>
            <div class="csi-galeria-item">
                <img src="<?php echo $img_base . $img; ?>" alt="Campo San Isidro" loading="lazy" />
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
