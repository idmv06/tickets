<?php
session_start();
include 'conexion.php';

// Validar que sea un usuario de soporte logueado
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'soporte') {
    header("Location: login.html");
    exit();
}

// Consultar los tickets reales uniendo tablas para obtener nombres claros
$sql = "SELECT t.*, c.nombre AS nombre_colegio, u.nombre AS nombre_usuario 
        FROM tickets t
        INNER JOIN colegios c ON t.colegio_id = c.colegio_id
        INNER JOIN usuarios u ON t.usuario_id = u.usuario_id
        ORDER BY t.fecha_creacion DESC";

$resultado = mysqli_query($conexion, $sql);
$total_tickets = mysqli_num_rows($resultado);
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Soporte - Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <!-- CSS -->
    <link href="admin.css" rel="stylesheet">
</head>



<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">Soporte Técnico - Panel Admin</a>
        <div class="d-flex align-items-center text-white">
    <span class="me-3"><i class="bi bi-shield-lock-fill"></i> <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
    
    <!-- Botón para abrir el modal de registrar admin -->
    <button type="button" class="btn btn-outline-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalRegistrarAdmin">
        <i class="bi bi-person-plus"></i> Registrar Admin
    </button>
    
    <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
    </div>
    </div>
    </nav>
    
    <div class="container mb-5">

        <!-- Resumen -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-3">
                    <h6 class="text-muted mb-1">Total de Tickets en Sistema</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?php echo $total_tickets; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-3 border-start border-warning border-4">
                    <h6 class="text-muted mb-1">Estado Activo</h6>
                    <h2 class="fw-bold mb-0 text-warning">Monitoreo en vivo</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-3 border-start border-success border-4">
                    <h6 class="text-muted mb-1">Base de Datos</h6>
                    <h2 class="fw-bold mb-0 text-success">Conectada</h2>
                </div>
            </div>
        </div>

        <!-- Tabla de Tickets Dinámica -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Tickets Recibidos</h5>
         <!-- Filtro por estado -->
         <select class="form-select form-select-sm style-select" style="width: 180px;">
         <option value="todos">Todos los estados</option>
            <option value="Pendiente">Pendientes</option>
         <option value="En Proceso">En Proceso</option>
         <option value="Resuelto">Resueltos</option>
            </select>
        </div>
        
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Colegio / Remitente</th>
                                <th>Asunto</th>
                                <th>Categoría</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($total_tickets > 0) {
                                while ($ticket = mysqli_fetch_assoc($resultado)) {
                                    $badgePrioridad = 'bg-secondary';
                                    if ($ticket['prioridad'] === 'Alta') $badgePrioridad = 'bg-danger';
                                    if ($ticket['prioridad'] === 'Urgente') $badgePrioridad = 'bg-dark';
                                    if ($ticket['prioridad'] === 'Media') $badgePrioridad = 'bg-warning text-dark';

                                    $badgeEstado = 'bg-secondary';
                                    if ($ticket['estado'] === 'Pendiente') $badgeEstado = 'bg-warning text-dark';
                                    if ($ticket['estado'] === 'En Proceso') $badgeEstado = 'bg-info text-dark';
                                    if ($ticket['estado'] === 'Resuelto') $badgeEstado = 'bg-success';

                                    echo "<tr>
                                        <td><strong>#" . $ticket['ticket_id'] . "</strong></td>
                                        <td>
                                            <div><strong>" . htmlspecialchars($ticket['nombre_colegio']) . "</strong></div>
                                            <small class='text-muted'>" . htmlspecialchars($ticket['nombre_usuario']) . "</small>
                                        </td>
                                        <td>" . htmlspecialchars($ticket['asunto']) . "</td>
                                        <td>" . htmlspecialchars($ticket['categoria']) . "</td>
                                        <td><span class='badge {$badgePrioridad}'>" . $ticket['prioridad'] . "</span></td>
                                        <td><span class='badge {$badgeEstado}'>" . $ticket['estado'] . "</span></td>
                                        <td>" . $ticket['fecha_creacion'] . "</td>
                                        <td class='text-center'>
                                            <a href='ver_ticket.php?id=" . $ticket['ticket_id'] . "' class='btn btn-sm btn-outline-primary me-1'>
                                                Ver / Atender
                                             </a>";

                                            // Si el estado del ticket es Cerrado, se muestra el botón de borrar
                                            if ($ticket['estado'] === 'Cerrado') {
                                                echo "<a href='eliminar_ticket.php?id=" . $ticket['ticket_id'] . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"¿Estás seguro de eliminar este ticket cerrado?\");'>
                                                    Borrar
                                                </a>";
                                            }

                                    echo "</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center py-4 text-muted'>No hay tickets registrados en este momento.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
<!-- Alertas de éxito o error -->
<?php if (isset($_SESSION['mensaje_exito'])): ?>
    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
        <?php echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['mensaje_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
        <?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Modal para Registrar Nuevo Admin -->
<div class="modal fade" id="modalRegistrarAdmin" tabindex="-1" aria-labelledby="modalRegistrarAdminLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-content-morado rounded-4">
      
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalRegistrarAdminLabel">
            <i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Administrador
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form action="registrar_admin.php" method="POST">
          <div class="modal-body p-4">
            <div class="mb-3">
                <label for="nombreAdmin" class="form-label fw-bold">Nombre Completo</label>
                <input type="text" class="form-control" id="nombreAdmin" name="nombre" required placeholder="Ej: Juan Pérez">
            </div>
            <div class="mb-3">
                <label for="correoAdmin" class="form-label fw-bold">Correo Electrónico</label>
                <input type="email" class="form-control" id="correoAdmin" name="correo" required placeholder="soporte@colegio.com">
            </div>
            <div class="mb-3">
                <label for="contrasenaAdmin" class="form-label fw-bold">Contraseña</label>
                <input type="password" class="form-control" id="contrasenaAdmin" name="contrasena" required placeholder="Crea una contraseña segura">
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-modal-morado">Registrar Usuario</button>
          </div>
      </form>

    </div>
  </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>