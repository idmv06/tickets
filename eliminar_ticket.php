<?php
session_start();
include 'conexion.php';
require_once 'csrf.php';

// Validar que el usuario sea de soporte (admin)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'soporte') {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf(); // Blindaje contra CSRF

    $ticket_id = intval($_POST['ticket_id'] ?? 0);

    if ($ticket_id > 0) {
        // Opcional: Eliminar primero comentarios asociados para mantener integridad referencial
        $sql_com = "DELETE FROM comentarios_tickets WHERE ticket_id = ?";
        $stmt_com = mysqli_prepare($conexion, $sql_com);
        mysqli_stmt_bind_param($stmt_com, "i", $ticket_id);
        mysqli_stmt_execute($stmt_com);
        mysqli_stmt_close($stmt_com);

        // Eliminar el ticket
        $sql = "DELETE FROM tickets WHERE ticket_id = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $ticket_id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['mensaje_exito'] = "El ticket #$ticket_id fue eliminado permanentemente.";
        } else {
            error_log("Error DB al eliminar ticket: " . mysqli_error($conexion));
            $_SESSION['mensaje_error'] = "Ocurrió un error al intentar eliminar el ticket.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['mensaje_error'] = "Identificador de ticket no válido.";
    }
}

mysqli_close($conexion);
header("Location: admin_tickets.php");
exit();
?>