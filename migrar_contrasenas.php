<?php
include 'conexion.php';

$sql = "SELECT usuario_id, password FROM usuarios";
$res = mysqli_query($conexion, $sql);

while ($user = mysqli_fetch_assoc($res)) {
    // Only hash passwords that aren't already hashed
    if (password_get_info($user['password'])['algo'] === 0) {
        $hashed = password_hash($user['password'], PASSWORD_DEFAULT);
        
        $up = "UPDATE usuarios SET password = ? WHERE usuario_id = ?";
        $stmt = mysqli_prepare($conexion, $up);
        mysqli_stmt_bind_param($stmt, "si", $hashed, $user['usuario_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

echo "Contraseñas actualizadas con éxito.";
?>
