<?php
// Iniciar sesión y cargar la protección CSRF.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/csrf.php';

// Verificar que la persona haya iniciado sesión.
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.html');
    exit();
}

// Este archivo solamente procesa formularios POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: nuevo_ticket.php');
    exit();
}

// El personal de soporte gestiona tickets desde su panel.
if (($_SESSION['rol'] ?? '') === 'soporte') {
    header('Location: admin_tickets.php');
    exit();
}

// Validar el token CSRF.
validar_csrf();

// Leer y validar los datos recibidos.
$asunto = isset($_POST['asunto']) && is_string($_POST['asunto'])
    ? trim($_POST['asunto'])
    : '';

$categoria = isset($_POST['categoria']) && is_string($_POST['categoria'])
    ? trim($_POST['categoria'])
    : '';

$prioridad = isset($_POST['prioridad']) && is_string($_POST['prioridad'])
    ? trim($_POST['prioridad'])
    : '';

$descripcion = isset($_POST['descripcion']) && is_string($_POST['descripcion'])
    ? trim($_POST['descripcion'])
    : '';

$usuario_id = (int) $_SESSION['usuario_id'];

// Categorías y prioridades autorizadas.
$categorias_permitidas = [
    'Registro de Notas',
    'Control de Entradas y Salidas',
    'Módulo de Anuncios',
    'Problema General'
];

$prioridades_permitidas = [
    'Baja',
    'Media',
    'Alta',
    'Urgente'
];

if (
    !in_array($categoria, $categorias_permitidas, true) ||
    !in_array($prioridad, $prioridades_permitidas, true)
) {
    $_SESSION['mensaje_error'] =
        'La categoría o prioridad seleccionada no es válida.';

    header('Location: nuevo_ticket.php');
    exit();
}

// Verificar que el usuario tenga una institución asignada.
if (empty($_SESSION['colegio_id'])) {
    $_SESSION['mensaje_error'] =
        'Error de sesión: usuario sin institución asignada.';

    header('Location: login.html');
    exit();
}

$colegio_id = (int) $_SESSION['colegio_id'];

// Validar los campos obligatorios.
if ($asunto === '' || $descripcion === '') {
    $_SESSION['mensaje_error'] =
        'El asunto y la descripción son obligatorios.';

    header('Location: nuevo_ticket.php');
    exit();
}

// Limitar el asunto a los 150 caracteres de la columna SQL.
$longitud_asunto = preg_match_all('/./us', $asunto, $coincidencias);

if ($longitud_asunto === false || $longitud_asunto > 150) {
    $_SESSION['mensaje_error'] =
        'El asunto no puede superar los 150 caracteres.';

    header('Location: nuevo_ticket.php');
    exit();
}

// Conectar a MySQL antes de guardar archivos.
require_once __DIR__ . '/conexion.php';

$nombre_archivo_final = null;
$ruta_archivo_guardado = null;

// Procesar el archivo adjunto, si se seleccionó uno.
$archivo = $_FILES['archivo'] ?? null;

if ($archivo !== null) {

    if (
        !is_array($archivo) ||
        !isset($archivo['error']) ||
        is_array($archivo['error'])
    ) {
        $_SESSION['mensaje_error'] =
            'La información del archivo adjunto no es válida.';

        header('Location: nuevo_ticket.php');
        exit();
    }

    $error_archivo = (int) $archivo['error'];

    // El archivo adjunto es opcional.
    if ($error_archivo !== UPLOAD_ERR_NO_FILE) {

        // Comprobar errores de subida de PHP.
        if ($error_archivo !== UPLOAD_ERR_OK) {

            if (
                $error_archivo === UPLOAD_ERR_INI_SIZE ||
                $error_archivo === UPLOAD_ERR_FORM_SIZE
            ) {
                $_SESSION['mensaje_error'] =
                    'El archivo supera el límite de 5 MB.';

            } elseif ($error_archivo === UPLOAD_ERR_PARTIAL) {
                $_SESSION['mensaje_error'] =
                    'El archivo se recibió incompleto. Intente de nuevo.';

            } else {
                $_SESSION['mensaje_error'] =
                    'No se pudo recibir el archivo adjunto. Intente de nuevo.';
            }

            header('Location: nuevo_ticket.php');
            exit();
        }

        $tmp_name = $archivo['tmp_name'] ?? '';
        $file_size = (int) ($archivo['size'] ?? 0);

        // Máximo: 5 MiB.
        if ($file_size <= 0 || $file_size > 5242880) {
            $_SESSION['mensaje_error'] =
                'El archivo debe tener contenido y no superar los 5 MB.';

            header('Location: nuevo_ticket.php');
            exit();
        }

        // Comprobar que se trate de una subida HTTP válida.
        if (!is_uploaded_file($tmp_name)) {
            $_SESSION['mensaje_error'] =
                'No se pudo validar el archivo recibido.';

            header('Location: nuevo_ticket.php');
            exit();
        }

        // Detectar el tipo real de archivo mediante MIME.
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if ($finfo === false) {
            error_log('No se pudo iniciar la validación MIME del adjunto.');

            $_SESSION['mensaje_error'] =
                'No se pudo validar el archivo adjunto.';

            header('Location: nuevo_ticket.php');
            exit();
        }

        $mime = finfo_file($finfo, $tmp_name);
        finfo_close($finfo);

        // Tipos de archivo admitidos.
        $allowed_mimes = [
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            'image/gif'       => 'gif',
            'application/pdf' => 'pdf',
            'application/zip' => 'zip'
        ];

        if (
            !is_string($mime) ||
            !array_key_exists($mime, $allowed_mimes)
        ) {
            $_SESSION['mensaje_error'] =
                'El formato de archivo adjunto no está permitido.';

            header('Location: nuevo_ticket.php');
            exit();
        }

        $ext = $allowed_mimes[$mime];
        $directorio_uploads =
            __DIR__ . DIRECTORY_SEPARATOR . 'uploads';

        // Crear el directorio si hace falta.
        if (
            !is_dir($directorio_uploads) &&
            !@mkdir($directorio_uploads, 0755, true) &&
            !is_dir($directorio_uploads)
        ) {
            error_log(
                'No se pudo crear el directorio de archivos adjuntos: ' .
                $directorio_uploads
            );

            $_SESSION['mensaje_error'] =
                'No se pudo preparar el almacenamiento del archivo.';

            header('Location: nuevo_ticket.php');
            exit();
        }

        // Comprobar que PHP pueda escribir en el volumen.
        if (!is_writable($directorio_uploads)) {
            error_log(
                'El directorio de adjuntos no permite escritura: ' .
                $directorio_uploads
            );

            $_SESSION['mensaje_error'] =
                'No se pudo guardar el archivo adjunto. Intente más tarde.';

            header('Location: nuevo_ticket.php');
            exit();
        }

        // Generar un nombre aleatorio, sin usar el nombre original.
        $nombre_archivo_final =
            bin2hex(random_bytes(16)) . '.' . $ext;

        $ruta_archivo_guardado =
            $directorio_uploads .
            DIRECTORY_SEPARATOR .
            $nombre_archivo_final;

        // Guardar físicamente el archivo en el volumen.
        if (!move_uploaded_file($tmp_name, $ruta_archivo_guardado)) {

            error_log(
                'Falló move_uploaded_file() al guardar el adjunto en: ' .
                $ruta_archivo_guardado
            );

            $nombre_archivo_final = null;
            $ruta_archivo_guardado = null;

            $_SESSION['mensaje_error'] =
                'No se pudo guardar el archivo adjunto. Intente de nuevo.';

            header('Location: nuevo_ticket.php');
            exit();
        }
    }
}

// Insertar el ticket en MySQL.
// Se guarda el nombre del archivo, no la imagen en sí.
$sql = "INSERT INTO tickets
        (
            colegio_id,
            usuario_id,
            categoria,
            prioridad,
            estado,
            asunto,
            descripcion,
            archivo_adjunto
        )
        VALUES (?, ?, ?, ?, 'Pendiente', ?, ?, ?)";

$stmt = mysqli_prepare($conexion, $sql);

// Si falla la preparación, limpiar el archivo subido.
if (!$stmt) {

    error_log(
        'Error DB al preparar inserción de ticket: ' .
        mysqli_error($conexion)
    );

    if (
        $ruta_archivo_guardado !== null &&
        is_file($ruta_archivo_guardado)
    ) {
        if (!@unlink($ruta_archivo_guardado)) {
            error_log(
                'No se pudo eliminar el adjunto tras fallar la consulta: ' .
                $ruta_archivo_guardado
            );
        }
    }

    mysqli_close($conexion);

    $_SESSION['mensaje_error'] =
        'Ocurrió un error interno en el sistema. Intente más tarde.';

    header('Location: nuevo_ticket.php');
    exit();
}

// Vincular los datos a la consulta preparada.
mysqli_stmt_bind_param(
    $stmt,
    'iisssss',
    $colegio_id,
    $usuario_id,
    $categoria,
    $prioridad,
    $asunto,
    $descripcion,
    $nombre_archivo_final
);

// Ejecutar el INSERT.
if (!mysqli_stmt_execute($stmt)) {

    error_log(
        'Error DB al ejecutar inserción de ticket: ' .
        mysqli_stmt_error($stmt)
    );

    mysqli_stmt_close($stmt);

    // Si SQL falla, no dejar un archivo huérfano.
    if (
        $ruta_archivo_guardado !== null &&
        is_file($ruta_archivo_guardado)
    ) {
        if (!@unlink($ruta_archivo_guardado)) {
            error_log(
                'No se pudo eliminar el adjunto tras fallar el INSERT: ' .
                $ruta_archivo_guardado
            );
        }
    }

    mysqli_close($conexion);

    $_SESSION['mensaje_error'] =
        'Ocurrió un error al procesar su solicitud en la base de datos. Intente más tarde.';

    header('Location: nuevo_ticket.php');
    exit();
}

// Ticket creado correctamente.
mysqli_stmt_close($stmt);
mysqli_close($conexion);

mostrarExito();
exit();


// Pantalla de confirmación.
function mostrarExito(): void
{
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ticket Enviado</title>

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
        >

        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        >

        <link rel="stylesheet" href="app.css">
    </head>

    <body class="app-background d-flex align-items-center justify-content-center min-vh-100 py-4">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card card-glass shadow-lg border-0 p-4 rounded-4">
                        <div class="card-body">

                            <div class="mb-3">
                                <i class="bi bi-check-circle-fill text-success display-4"></i>
                            </div>

                            <h3 class="fw-bold mb-3 text-white">
                                ¡Ticket enviado con éxito!
                            </h3>

                            <p class="text-muted-glass mb-4">
                                Su solicitud ha sido registrada correctamente.
                                El equipo de soporte técnico la revisará pronto.
                            </p>

                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                <a href="nuevo_ticket.php" class="btn btn-morado px-4">
                                    Crear otro ticket
                                </a>

                                <a href="mis_tickets.php" class="btn btn-outline-light px-4">
                                    Ver mis tickets
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
}
?>
