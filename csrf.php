<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Genera un token si no existe
function obtener_token_csrf() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Imprime el campo oculto en el HTML
function campo_csrf() {
    $token = obtener_token_csrf();
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

// Valida el token recibido en POST
function validar_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token_post = $_POST['csrf_token'] ?? '';
        $token_sesion = $_SESSION['csrf_token'] ?? '';

        if (empty($token_post) || empty($token_sesion) || !hash_equals($token_sesion, $token_post)) {
            $_SESSION['mensaje_error'] = "Error de validación de seguridad (CSRF inválido). Intente de nuevo.";
            // Redirigir a la página anterior o a la principal
            $referer = $_SERVER['HTTP_REFERER'] ?? 'admin_tickets.php';
            header("Location: " . $referer);
            exit();
        }
    }
}
?>