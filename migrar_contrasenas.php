<?php
include 'conexion.php';

$sql = "SELECT usuario_id, contrasena FROM usuarios";
$res = mysqli_query($conexion, $sql);

if (!$res) {
    die("Error al consultar la tabla usuarios: " . mysqli_error($conexion));
}

$contador = 0;

while ($user = mysqli_fetch_assoc($res)) {
    // A prueba de PHP 8: Si no empieza por $2y$, sabemos que es texto plano
    if (substr($user['contrasena'], 0, 4) !== '$2y$') {
        
        $hashed = password_hash($user['contrasena'], PASSWORD_DEFAULT);
        
        $up = "UPDATE usuarios SET contrasena = ? WHERE usuario_id = ?";
        $stmt = mysqli_prepare($conexion, $up);
        mysqli_stmt_bind_param($stmt, "si", $hashed, $user['usuario_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $contador++;
    }
}

echo "¡Listo! Se actualizaron " . $contador . " contraseñas a hash exitosamente.";
?>