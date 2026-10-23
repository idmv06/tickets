<?php
session_start();

// Validar que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

// Si entra un usuario de soporte, lo enviamos a su panel
if ($_SESSION['rol'] === 'soporte') {
    header("Location: admin_tickets.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Ticket - Plataforma Académica</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="app.css">

    <style>
        html, body {
            min-height: 100vh;
            margin: 0;
        }
    </style>

</head>
<body class="app-background">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Portal de Colegio</a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3"><i class="bi bi-person-fill"></i> Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                
                <!-- Botón de acceso a Mis Tickets -->
                <a href="mis_tickets.php" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-ticket-perforated"></i> Ver mis tickets enviados</a>
                
                <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        
                        <h3 class="mb-2 text-morado fw-bold">Reportar un Inconveniente</h3>
                        <p class="text-muted mb-4">Complete los datos a continuación para enviar una solicitud al equipo de soporte.</p>
                        <?php if (!empty($_SESSION['mensaje_error'])): ?>
                        <div class="alert alert-danger" role="alert">
                         <?= htmlspecialchars(
                            $_SESSION['mensaje_error'],
                            ENT_QUOTES,
                            'UTF-8'
                            ) ?>
                            </div>

                            <?php unset($_SESSION['mensaje_error']); ?>
                        <?php endif; ?>

                        <!-- Formulario procesado por PHP -->
                        <form action="guardar_ticket.php" method="POST" enctype="multipart/form-data">
                         <?php require_once 'csrf.php'; campo_csrf(); ?>
                            <!-- Asunto -->
                            <div class="mb-3">
                                <label for="asunto" class="form-label fw-bold">Asunto del problema</label>
                                <input type="text" class="form-control" id="asunto" name="asunto" required placeholder="Ej: No me permite subir notas de 10-A">
                            </div>

                            <!-- Categoría y Prioridad -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="categoria" class="form-label fw-bold">Categoría</label>
                                    <select class="form-select" id="categoria" name="categoria">
                                        
                                        <option value="Registro de Notas">
                                         Registro de Notas
                                        </option>

                                        <option value="Control de Entradas y Salidas">
                                            Control de Entradas y Salidas
                                        </option>

                                        <option value="Módulo de Anuncios">
                                            Módulo de Anuncios
                                        </option>

                                        <option value="Problema General">
                                            Problema General
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="prioridad" class="form-label fw-bold">Prioridad</label>
                                    <select class="form-select" id="prioridad" name="prioridad">
                                        <option value="Baja">Baja</option>
                                        <option value="Media" selected>Media</option>
                                        <option value="Alta">Alta</option>
                                        <option value="Urgente">Urgente</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="mb-4">
                                <label for="descripcion" class="form-label fw-bold">Descripción detallada</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required placeholder="Describa con detalle lo que sucede..."></textarea>
                            </div>

                            <!-- adjuntar archivo/captura -->
                            <div class="mb-4">
                             <label for="archivo" class="form-label fw-bold">Adjuntar captura o documento (Opcional)</label>
                             <input type="file" class="form-control" id="archivo" name="archivo" accept=".jpg,.jpeg,.png,.gif,.pdf,.zip">
                             <div class="form-text text-white-50">Formatos permitidos: Imágenes (PNG, JPG), PDF, ZIP. Máx: 5MB.</div>
                            </div>

                            <!-- Botón -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-morado btn-lg">Enviar Ticket a Soporte</button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
