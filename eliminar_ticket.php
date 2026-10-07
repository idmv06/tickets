<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'soporte') {
    header("Location: login.html");
    exit();
}

if (isset($_GET['id'])) {
    $ticket_id = intval($_GET['id']);

    $sql_check = "SELECT estado FROM tickets WHERE ticket_id = ?";
    $stmt_chk = mysqli_prepare($conexion, $sql_check);
    mysqli_stmt_bind_param($stmt_chk, "i", $ticket_id);
    mysqli_stmt_execute($stmt_chk);
    $res = mysqli_stmt_get_result($stmt_chk);
    $ticket = mysqli_fetch_assoc($res);

    if ($ticket && $ticket['estado'] === 'Cerrado') {
        $sql_del = "DELETE FROM tickets WHERE ticket_id = ?";
        $stmt_del = mysqli_prepare($conexion, $sql_del);
        mysqli_stmt_bind_param($stmt_del, "i", $ticket_id);
        mysqli_stmt_execute($stmt_del);
        mysqli_stmt_close($stmt_del);
    }
}

header("Location: admin_tickets.php");
exit();