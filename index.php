<?php
// Página de bienvenida del Sistema de Tickets.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#500497">
    <meta name="description" content="Sistema web para registrar, organizar y dar seguimiento a solicitudes de soporte técnico desde una plataforma centralizada.">
    <title>Sistema de Tickets | Soporte técnico organizado</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="landing.css" rel="stylesheet">
</head>
<body>
    <header class="hero-section" id="inicio">
        <nav class="navbar site-navbar" aria-label="Navegación principal">
            <div class="container site-container d-flex align-items-center justify-content-between gap-3">
                <a class="brand-lockup" href="#inicio" aria-label="Sistema de Tickets, inicio">
                    <span class="brand-icon"><i class="bi bi-ticket-detailed-fill" aria-hidden="true"></i></span>
                    <span class="brand-name">Sistema <span>de Tickets</span></span>
                </a>

                <div class="nav-actions d-flex align-items-center gap-2 gap-md-3">
                    <a class="nav-text-link d-none d-sm-inline-flex" href="#funcionalidades">Funcionalidades</a>
                    <a class="nav-text-link d-none d-md-inline-flex" href="#como-funciona">Cómo funciona</a>
                    <a class="nav-login" href="login.html">
                        <span>Acceder</span>
                        <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </nav>

        <div class="container site-container hero-content">
            <div class="hero-copy">
                <div class="eyebrow"><span class="eyebrow-dot"></span> PLATAFORMA DE SOPORTE TÉCNICO</div>
                <h1>Cada solicitud,<br><span>en su lugar.</span></h1>
                <p class="hero-description">
                    Un espacio centralizado para registrar, organizar y dar seguimiento a las solicitudes de soporte técnico.
                    Menos solicitudes dispersas; más claridad en cada paso.
                </p>

                <div class="hero-actions">
                    <a class="btn-landing btn-landing-primary" href="login.html">
                        Acceder a la plataforma <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                    <a class="btn-landing btn-landing-secondary" href="#funcionalidades">
                        Conocer el sistema <i class="bi bi-chevron-down" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="hero-highlights" aria-label="Aspectos principales">
                    <span><i class="bi bi-check2-circle" aria-hidden="true"></i> Seguimiento de solicitudes</span>
                    <span><i class="bi bi-check2-circle" aria-hidden="true"></i> Comunicación centralizada</span>
                </div>
            </div>

            <div class="hero-visual" aria-label="Vista ilustrativa de una solicitud de soporte">
                <div class="orbit orbit-one"></div>
                <div class="orbit orbit-two"></div>
                <div class="preview-window">
                    <div class="preview-topbar">
                        <div class="window-dots" aria-hidden="true"><span></span><span></span><span></span></div>
                        <span class="preview-topbar-label"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i> Panel de soporte</span>
                        <span class="preview-menu-icon"><i class="bi bi-three-dots" aria-hidden="true"></i></span>
                    </div>

                    <div class="preview-body">
                        <div class="preview-heading-row">
                            <div>
                                <p class="preview-kicker">CENTRO DE ATENCIÓN</p>
                                <h2>Solicitudes</h2>
                            </div>
                            <span class="preview-counter"><i class="bi bi-list-check" aria-hidden="true"></i> Seguimiento</span>
                        </div>

                        <div class="preview-summary">
                            <div class="summary-icon"><i class="bi bi-inboxes-fill" aria-hidden="true"></i></div>
                            <div><strong>Todo más organizado</strong><span>Información en un solo lugar</span></div>
                            <i class="bi bi-arrow-up-right summary-arrow" aria-hidden="true"></i>
                        </div>

                        <div class="preview-list-label">EJEMPLOS DE SOLICITUDES</div>
                        <div class="ticket-row">
                            <span class="ticket-icon ticket-icon-purple"><i class="bi bi-person-lock" aria-hidden="true"></i></span>
                            <div class="ticket-info"><strong>Problema de acceso</strong><span>Consulta registrada</span></div>
                            <span class="status-pill status-review"><span></span>En revisión</span>
                        </div>
                        <div class="ticket-row">
                            <span class="ticket-icon ticket-icon-blue"><i class="bi bi-chat-left-text" aria-hidden="true"></i></span>
                            <div class="ticket-info"><strong>Consulta de funcionamiento</strong><span>Con información de seguimiento</span></div>
                            <span class="status-pill status-open"><span></span>Recibido</span>
                        </div>
                        <div class="ticket-row">
                            <span class="ticket-icon ticket-icon-green"><i class="bi bi-check2-circle" aria-hidden="true"></i></span>
                            <div class="ticket-info"><strong>Solicitud atendida</strong><span>Actualización disponible</span></div>
                            <span class="status-pill status-done"><span></span>Resuelto</span>
                        </div>
                        <div class="preview-footnote"><i class="bi bi-info-circle" aria-hidden="true"></i> Vista conceptual con ejemplos ilustrativos.</div>
                    </div>
                </div>
                <div class="floating-note floating-note-top"><span class="floating-note-icon"><i class="bi bi-chat-square-dots-fill" aria-hidden="true"></i></span><span><strong>Conversación</strong><small>Contexto a mano</small></span></div>
                <div class="floating-note floating-note-bottom"><span class="floating-note-icon floating-note-icon-light"><i class="bi bi-check-lg" aria-hidden="true"></i></span><span><strong>Seguimiento claro</strong><small>Del registro a la resolución</small></span></div>
            </div>
        </div>

        <div class="hero-bottom-glow" aria-hidden="true"></div>
    </header>

    <main>
        <section class="features-section section-space" id="funcionalidades">
            <div class="container site-container">
                <div class="section-heading">
                    <span class="section-kicker">UNA FORMA MÁS CLARA DE TRABAJAR</span>
                    <h2>El soporte, <span>mejor organizado.</span></h2>
                    <p>Las herramientas principales para reportar incidencias y coordinar su atención en una misma plataforma.</p>
                </div>

                <div class="feature-grid">
                    <article class="feature-card">
                        <div class="feature-icon feature-icon-violet"><i class="bi bi-inbox-fill" aria-hidden="true"></i></div>
                        <span class="feature-number">01 / REGISTRO</span>
                        <h3>Solicitudes centralizadas</h3>
                        <p>Registrar un problema, describir lo ocurrido y adjuntar archivos relevantes para explicar mejor cada caso.</p>
                        <span class="feature-link">Crear y documentar <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
                    </article>

                    <article class="feature-card">
                        <div class="feature-icon feature-icon-blue"><i class="bi bi-arrow-repeat" aria-hidden="true"></i></div>
                        <span class="feature-number">02 / SEGUIMIENTO</span>
                        <h3>Estado siempre visible</h3>
                        <p>Consultar el avance de las solicitudes y mantener la información relacionada con cada ticket en contexto.</p>
                        <span class="feature-link">Revisar el progreso <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
                    </article>

                    <article class="feature-card">
                        <div class="feature-icon feature-icon-pink"><i class="bi bi-people-fill" aria-hidden="true"></i></div>
                        <span class="feature-number">03 / COORDINACIÓN</span>
                        <h3>Trabajo en equipo</h3>
                        <p>Asignar responsables, organizar la atención y usar comentarios públicos o internos según corresponda.</p>
                        <span class="feature-link">Coordinar la atención <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
                    </article>
                </div>
            </div>
        </section>

        <section class="workflow-section section-space" id="como-funciona">
            <div class="container site-container workflow-layout">
                <div class="workflow-copy">
                    <span class="section-kicker">UN FLUJO SENCILLO</span>
                    <h2>De un reporte a una <span>solución.</span></h2>
                    <p>El proceso queda documentado para que las personas usuarias y el equipo de soporte puedan consultar la información que necesitan.</p>
                    <a class="workflow-link" href="login.html">Entrar al sistema <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </div>

                <div class="workflow-steps">
                    <div class="workflow-step">
                        <div class="step-number">1</div>
                        <div class="step-copy"><h3>Registrar</h3><p>Describir la situación y aportar los detalles necesarios.</p></div>
                        <i class="bi bi-file-earmark-plus step-decoration" aria-hidden="true"></i>
                    </div>
                    <div class="step-connector" aria-hidden="true"></div>
                    <div class="workflow-step">
                        <div class="step-number">2</div>
                        <div class="step-copy"><h3>Atender y dar seguimiento</h3><p>Revisar el estado, intercambiar información y coordinar responsables.</p></div>
                        <i class="bi bi-chat-square-text step-decoration" aria-hidden="true"></i>
                    </div>
                    <div class="step-connector" aria-hidden="true"></div>
                    <div class="workflow-step">
                        <div class="step-number">3</div>
                        <div class="step-copy"><h3>Resolver</h3><p>Consultar las actualizaciones y mantener el historial de la solicitud.</p></div>
                        <i class="bi bi-check2-circle step-decoration" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </section>

        <section class="final-cta-section">
            <div class="container site-container">
                <div class="final-cta-card">
                    <div class="final-cta-symbol"><i class="bi bi-ticket-detailed-fill" aria-hidden="true"></i></div>
                    <div class="final-cta-copy">
                        <span class="section-kicker">SISTEMA DE TICKETS</span>
                        <h2>¿Listo para continuar?</h2>
                        <p>Acceda a la plataforma para registrar o consultar las solicitudes de soporte.</p>
                    </div>
                    <a class="btn-landing btn-landing-primary" href="login.html">Ingresar al sistema <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container site-container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <a class="footer-brand" href="#inicio"><i class="bi bi-ticket-detailed-fill" aria-hidden="true"></i> Sistema de Tickets</a>
            <p>Proyecto de práctica profesional · Gestión de solicitudes de soporte técnico</p>
        </div>
    </footer>
</body>
</html>
