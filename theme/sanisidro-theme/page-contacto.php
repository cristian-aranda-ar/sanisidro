<?php
/**
 * Template Name: Contacto
 */
get_header();
?>

<!-- ══════════ CONTACT HERO ══════════ -->
<div class="csi-page-hero csi-page-hero--short">
    <div class="csi-page-hero__overlay" style="background:rgba(15,15,15,0.8)"></div>
    <div class="csi-container csi-page-hero__content">
        <div class="csi-feature__label">Estamos para ayudarte</div>
        <h1 class="csi-page-hero__title">Contacto</h1>
        <p class="csi-page-hero__sub">Escribinos o llamanos — respondemos de lunes a sábado</p>
    </div>
</div>

<!-- ══════════ CONTACT GRID ══════════ -->
<section class="csi-section">
    <div class="csi-container">
        <div class="csi-contact-grid">

            <!-- Info -->
            <div class="csi-contact-info">
                <h2 class="csi-contact-info__title">Hablemos</h2>
                <p class="csi-contact-info__lead">Si tenés consultas sobre pedidos, envíos, precios mayoristas o cualquier otra cosa, no dudes en escribirnos. Respondemos en el día.</p>

                <div class="csi-contact-items">
                    <div class="csi-contact-item">
                        <div class="csi-contact-item__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                        </div>
                        <div>
                            <div class="csi-contact-item__label">Teléfono</div>
                            <a href="tel:+5493764381746" class="csi-contact-item__value">+549 3764 381746</a>
                        </div>
                    </div>
                    <div class="csi-contact-item">
                        <div class="csi-contact-item__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                        </div>
                        <div>
                            <div class="csi-contact-item__label">WhatsApp</div>
                            <a href="https://wa.me/5493764440000" class="csi-contact-item__value" target="_blank">+549 3764 440000</a>
                        </div>
                    </div>
                    <div class="csi-contact-item">
                        <div class="csi-contact-item__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div>
                            <div class="csi-contact-item__label">Email</div>
                            <a href="mailto:ventas@camposanisidro.com.ar" class="csi-contact-item__value">ventas@camposanisidro.com.ar</a>
                        </div>
                    </div>
                    <div class="csi-contact-item">
                        <div class="csi-contact-item__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div>
                            <div class="csi-contact-item__label">Dirección</div>
                            <div class="csi-contact-item__value">Garupá, Misiones, Argentina</div>
                        </div>
                    </div>
                    <div class="csi-contact-item">
                        <div class="csi-contact-item__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <div class="csi-contact-item__label">Horarios</div>
                            <div class="csi-contact-item__value">Lun – Sáb: 8:00 a 18:00 hs</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="csi-contact-form-wrap">
                <form class="csi-contact-form" onsubmit="return false;">
                    <h3 class="csi-contact-form__title">Envianos un mensaje</h3>
                    <div class="csi-cf-row">
                        <div class="csi-cf-field">
                            <label class="csi-cf-label">Nombre</label>
                            <input type="text" class="csi-cf-input" placeholder="Tu nombre" />
                        </div>
                        <div class="csi-cf-field">
                            <label class="csi-cf-label">Email</label>
                            <input type="email" class="csi-cf-input" placeholder="tu@email.com" />
                        </div>
                    </div>
                    <div class="csi-cf-field">
                        <label class="csi-cf-label">Asunto</label>
                        <select class="csi-cf-input">
                            <option value="">Seleccioná un motivo</option>
                            <option>Consulta de pedido</option>
                            <option>Precios mayoristas</option>
                            <option>Envíos y logística</option>
                            <option>Otro</option>
                        </select>
                    </div>
                    <div class="csi-cf-field">
                        <label class="csi-cf-label">Mensaje</label>
                        <textarea class="csi-cf-input csi-cf-textarea" placeholder="Contanos en qué te podemos ayudar..." rows="5"></textarea>
                    </div>
                    <button type="submit" class="csi-cf-btn">
                        Enviar mensaje
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>
