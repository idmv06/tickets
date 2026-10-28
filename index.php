<?php
// Página de bienvenida del Sistema de Tickets.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#500497">
    <meta name="description" content="Sistema web para registrar, organizar y dar seguimiento a solicitudes de soporte técnico.">
    <title>Sistema de Tickets</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="app.css" rel="stylesheet">
    <link href="landing.css" rel="stylesheet">
</head>
<body class="app-background">

    <!-- Barra superior (misma que el resto de la plataforma) -->
    <nav class="navbar navbar-dark mb-4 p-3 border-bottom border-white border-opacity-10">
        <div class="container d-flex justify-content-between">
            <a href="#inicio" class="navbar-brand fw-bold">
                <i class="bi bi-ticket-detailed-fill me-2"></i>Sistema de Tickets
            </a>
            <div class="d-flex align-items-center gap-3">
                <a href="#como-funciona" class="text-white-50 text-decoration-none d-none d-sm-inline">Cómo funciona</a>
                <a href="login.html" class="btn btn-outline-light btn-sm fw-bold">
                    <i class="bi bi-box-arrow-in-right"></i> Acceder
                </a>
            </div>
        </div>
    </nav>

    <main class="container" id="inicio">

        <!-- Inicio -->
        <section class="row align-items-center gy-5 gx-lg-5 landing-hero">
            <div class="col-lg-6">
                <h1 class="fw-bold mb-3">Las solicitudes de soporte, en un solo lugar.</h1>
                <p class="fs-5 text-muted-glass mb-4">
                    Reporte un problema, adjunte una captura y siga su caso hasta que se resuelva.
                    El equipo de soporte atiende todo desde una misma bandeja.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="login.html" class="btn btn-morado btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión
                    </a>
                    <a href="#como-funciona" class="btn btn-outline-light btn-lg px-4">Cómo funciona</a>
                </div>
            </div>

            <!-- Ejemplo con el mismo aspecto de la bandeja real -->
            <div class="col-lg-6">
                <div class="card card-glass shadow-sm">
                    <div class="card-header fw-bold">
                        <i class="bi bi-inbox-fill me-2"></i>Mis Reportes y Solicitudes
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-glass align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Asunto</th>
                                        <th>Estado</th>
                                        <th class="d-none d-sm-table-cell">Atendido por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">#3</td>
                                        <td>No me permite subir notas</td>
                                        <td><span class="badge bg-warning text-dark">Pendiente</span></td>
                                        <td class="d-none d-sm-table-cell"><span class="badge bg-secondary text-white">Pendiente de asignación</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">#2</td>
                                        <td>Error al iniciar sesión</td>
                                        <td><span class="badge bg-info text-dark">En Proceso</span></td>
                                        <td class="d-none d-sm-table-cell"><span class="badge bg-light text-dark"><i class="bi bi-headset me-1"></i>Soporte</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">#1</td>
                                        <td>Proyector del aula 4</td>
                                        <td><span class="badge bg-success">Resuelto</span></td>
                                        <td class="d-none d-sm-table-cell"><span class="badge bg-light text-dark"><i class="bi bi-headset me-1"></i>Soporte</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <p class="small text-muted-glass mt-2 mb-0">Ejemplo ilustrativo.</p>
            </div>
        </section>

        <!-- Qué permite hacer -->
        <section class="landing-section" id="funcionalidades">
            <h2 class="fw-bold h3 mb-4">Qué se puede hacer</h2>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card card-glass h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-ticket-detailed-fill fs-3"></i>
                            <h3 class="h5 fw-bold mt-3">Reportar un inconveniente</h3>
                            <p class="mb-0 text-muted-glass">
                                Describa lo que ocurre, elija una categoría y una prioridad, y adjunte una captura o un documento si ayuda a explicarlo.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-glass h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-chat-left-text-fill fs-3"></i>
                            <h3 class="h5 fw-bold mt-3">Seguir cada caso</h3>
                            <p class="mb-0 text-muted-glass">
                                Consulte el estado de sus tickets y la conversación con soporte sin tener que preguntar por otro medio.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-glass h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-headset fs-3"></i>
                            <h3 class="h5 fw-bold mt-3">Atender desde una bandeja</h3>
                            <p class="mb-0 text-muted-glass">
                                Soporte filtra por estado o agente, asigna responsables, deja notas internas y consulta reportes.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cómo funciona -->
        <section class="landing-section" id="como-funciona">
            <h2 class="fw-bold h3 mb-4">Cómo funciona</h2>
            <div class="card card-glass">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4 d-flex gap-3">
                            <span class="step-badge">1</span>
                            <div>
                                <h3 class="h6 fw-bold mb-1">Se registra la solicitud</h3>
                                <p class="mb-0 text-muted-glass small">Una persona del colegio crea el ticket con los detalles del problema.</p>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex gap-3">
                            <span class="step-badge">2</span>
                            <div>
                                <h3 class="h6 fw-bold mb-1">Soporte la atiende</h3>
                                <p class="mb-0 text-muted-glass small">Se asigna a un agente, que responde y actualiza el estado.</p>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex gap-3">
                            <span class="step-badge">3</span>
                            <div>
                                <h3 class="h6 fw-bold mb-1">Queda resuelta y documentada</h3>
                                <p class="mb-0 text-muted-glass small">El historial del caso se conserva y se puede imprimir como PDF.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cierre -->
        <section class="landing-section pt-0">
            <div class="card card-glass">
                <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h2 class="h4 fw-bold mb-1">¿Ya tiene una cuenta?</h2>
                        <p class="mb-0 text-muted-glass">Ingrese para crear o consultar sus solicitudes.</p>
                    </div>
                    <a href="login.html" class="btn btn-morado btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="container pb-4 text-center">
        <p class="small text-muted-glass mb-0">Proyecto de práctica profesional · Gestión de solicitudes de soporte técnico</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>