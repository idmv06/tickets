<?php
session_start();
include 'conexion.php';

// Validar que el usuario sea de soporte
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'soporte') {
    header("Location: login.html");
    exit();
}

// Valores por defecto para las tarjetas
$total_tickets = 0;
$total_pendientes = 0;
$total_proceso = 0;
$total_urgentes = 0;

//Consulta para las métricas del panel (KPIs)
$sql_kpis = "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
    SUM(CASE WHEN estado = 'En Proceso' THEN 1 ELSE 0 END) AS proceso,
    SUM(CASE WHEN prioridad = 'Urgente' AND estado != 'Cerrado' THEN 1 ELSE 0 END) AS urgentes
FROM tickets";

$res_kpis = mysqli_query($conexion, $sql_kpis);

if ($res_kpis && $fila_kpis = mysqli_fetch_assoc($res_kpis)) {
    $total_tickets = $fila_kpis['total'] ?? 0;
    $total_pendientes = $fila_kpis['pendientes'] ?? 0;
    $total_proceso = $fila_kpis['proceso'] ?? 0;
    $total_urgentes = $fila_kpis['urgentes'] ?? 0;
}

//Consulta para el listado completo de tickets para la tabla
$sql_tickets = "SELECT t.*, c.nombre AS nombre_colegio, u.nombre AS nombre_usuario 
                FROM tickets t
                INNER JOIN colegios c ON t.colegio_id = c.colegio_id
                INNER JOIN usuarios u ON t.usuario_id = u.usuario_id
                ORDER BY t.fecha_creacion DESC";
$resultado = mysqli_query($conexion, $sql_tickets);
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

        <!-- Alertas de éxito o error -->
        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <?php echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Fila de Métricas / KPIs reales -->
        <div class="row g-3 mb-4">
            <!-- Total Registrados -->
            <div class="col-md-3">
                <div class="card card-glass shadow-sm border-0 text-center py-3">
                    <div class="card-body p-2">
                        <p class="text-muted-glass small mb-1 fw-bold text-uppercase">Total Recibidos</p>
                        <h2 class="fw-bold mb-0"><?php echo $total_tickets; ?></h2>
                    </div>
                </div>
            </div>

            <!-- Pendientes -->
            <div class="col-md-3">
                <div class="card card-glass shadow-sm border-0 text-center py-3 border-start border-warning border-4">
                    <div class="card-body p-2">
                        <p class="text-warning small mb-1 fw-bold text-uppercase"><i class="bi bi-clock-history me-1"></i> Pendientes</p>
                        <h2 class="fw-bold mb-0 text-warning"><?php echo $total_pendientes; ?></h2>
                    </div>
                </div>
            </div>

            <!-- En Proceso -->
            <div class="col-md-3">
                <div class="card card-glass shadow-sm border-0 text-center py-3 border-start border-info border-4">
                    <div class="card-body p-2">
                        <p class="text-info small mb-1 fw-bold text-uppercase"><i class="bi bi-gear-wide-connected me-1"></i> En Proceso</p>
                        <h2 class="fw-bold mb-0 text-info"><?php echo $total_proceso; ?></h2>
                    </div>
                </div>
            </div>

            <!-- Urgentes -->
            <div class="col-md-3">
                <div class="card card-glass shadow-sm border-0 text-center py-3 border-start border-danger border-4">
                    <div class="card-body p-2">
                        <p class="text-danger small mb-1 fw-bold text-uppercase"><i class="bi bi-exclamation-diamond-fill me-1"></i> Casos Urgentes</p>
                        <h2 class="fw-bold mb-0 text-danger"><?php echo $total_urgentes; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Tickets Dinámica -->
        <div class="card card-glass shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <!-- Título -->
                    <div class="col-md-3 mb-2 mb-md-0">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-funnel me-2"></i>Tickets Recibidos</h5>
                    </div>
                    
                    <!-- Barra de herramientas (Buscador y Filtros) -->
                    <div class="col-md-9">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            
                            <!-- Buscador de texto libre -->
                            <div class="input-group input-group-sm" style="max-width: 250px;">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="buscadorTickets" class="form-control border-start-0 bg-light" placeholder="Buscar ID, colegio, asunto...">
                            </div>
                            
                            <!-- Filtro por Categoría -->
                            <select id="filtroCategoria" class="form-select form-select-sm" style="width: auto;">
                                <option value="todas">Todas las categorías</option>
                                <option value="Registro de Notas">Registro de Notas</option>
                                <option value="Control de Entradas">Control de Entradas</option>
                                <option value="Anuncios">Anuncios</option>
                                <option value="General">General</option>
                            </select>

                            <!-- Filtro por Prioridad -->
                            <select id="filtroPrioridad" class="form-select form-select-sm" style="width: auto;">
                                <option value="todas">Todas las prioridades</option>
                                <option value="Baja">Baja</option>
                                <option value="Media">Media</option>
                                <option value="Alta">Alta</option>
                                <option value="Urgente">Urgente</option>
                            </select>

                            <!-- Filtro por Estado -->
                            <select id="filtroEstado" class="form-select form-select-sm" style="width: auto;">
                                <option value="todos">Todos los estados</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="En Proceso">En Proceso</option>
                                <option value="Resuelto">Resuelto</option>
                                <option value="Cerrado">Cerrado</option>
                            </select>
                        </div>
                    </div>
                </div>
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
                            if ($total_tickets > 0 && $resultado && mysqli_num_rows($resultado) > 0) {
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

    <!-- Registrar Nuevo Admin -->
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

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para Búsqueda -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const buscador = document.getElementById("buscadorTickets");
        const filtroEstado = document.getElementById("filtroEstado");
        const filtroCategoria = document.getElementById("filtroCategoria");
        const filtroPrioridad = document.getElementById("filtroPrioridad");
        
        const filas = document.querySelectorAll("table tbody tr");

        function filtrarTickets() {
            const textoBusqueda = buscador.value.toLowerCase();
            const estadoSeleccionado = filtroEstado.value.toLowerCase();
            const categoriaSeleccionada = filtroCategoria.value.toLowerCase();
            const prioridadSeleccionada = filtroPrioridad.value.toLowerCase();

            filas.forEach(fila => {
                const textoFila = fila.textContent.toLowerCase();
                
                const celdaCategoria = fila.cells[3] ? fila.cells[3].textContent.toLowerCase() : "";
                const celdaPrioridad = fila.cells[4] ? fila.cells[4].textContent.toLowerCase() : "";
                const celdaEstado = fila.cells[5] ? fila.cells[5].textContent.toLowerCase() : "";

                const coincideTexto = textoFila.includes(textoBusqueda);
                const coincideEstado = (estadoSeleccionado === "todos") || celdaEstado.includes(estadoSeleccionado);
                const coincideCategoria = (categoriaSeleccionada === "todas") || celdaCategoria.includes(categoriaSeleccionada);
                const coincidePrioridad = (prioridadSeleccionada === "todas") || celdaPrioridad.includes(prioridadSeleccionada);

                if (coincideTexto && coincideEstado && coincideCategoria && coincidePrioridad) {
                    fila.style.display = ""; 
                } else {
                    fila.style.display = "none"; 
                }
            });
        }

        buscador.addEventListener("keyup", filtrarTickets);
        filtroEstado.addEventListener("change", filtrarTickets);
        filtroCategoria.addEventListener("change", filtrarTickets);
        filtroPrioridad.addEventListener("change", filtrarTickets);
    });
    </script>

</body>
</html>
