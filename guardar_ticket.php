```php
<?php
session_start();
require_once 'conexion.php';

// Verificar que la persona haya iniciado sesión.
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

// Solo se permite enviar el formulario por POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: nuevo_ticket.php");
    exit();
}

// Obtener los datos del formulario.
$asunto = trim($_POST['asunto'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$prioridad = trim($_POST['prioridad'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

$usuario_id = (int) $_SESSION['usuario_id'];
$colegio_id = isset($_SESSION['colegio_id'])
    ? (int) $_SESSION['colegio_id']
    : null;

// Validar los campos obligatorios.
if (
    $asunto === '' ||
    $descripcion === '' ||
    $categoria === '' ||
    $prioridad === ''
) {
    exit("Error: todos los campos obligatorios deben completarse.");
}

if ($colegio_id === null) {
    exit("Error: no se encontró el colegio asociado a su sesión.");
}

// El archivo adjunto es opcional.
$nombre_archivo_final = null;
$ruta_archivo = null;

// Procesar el archivo adjunto, si existe.
if (isset($_FILES['archivo']) &&
    $_FILES['archivo']['error'] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        exit("Error: no se pudo recibir el archivo adjunto.");
    }

    $tmp_name = $_FILES['archivo']['tmp_name'];
    $file_size = $_FILES['archivo']['size'];

    // Límite de 5 MB.
    if ($file_size <= 0 || $file_size > 5242880) {
        exit("Error: el archivo debe pesar más de 0 bytes y no superar los 5 MB.");
    }

    // Comprobar el tipo real del archivo.
    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    if ($finfo === false) {
        exit("Error: no se pudo verificar el archivo.");
    }

    $mime = finfo_file($finfo, $tmp_name);
    finfo_close($finfo);

    // Tipos de archivo permitidos.
    $allowed_mimes = [
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
        'image/gif'       => 'gif',
        'application/pdf' => 'pdf',
        'application/zip' => 'zip',
        'application/x-zip-compressed' => 'zip'
    ];

    if (!isset($allowed_mimes[$mime])) {
        exit("Error: solo se permiten imágenes JPG, PNG, GIF, archivos PDF y ZIP.");
    }

    // Crear la carpeta si todavía no existe.
    $directorio = __DIR__ . '/uploads/';

    if (!is_dir($directorio)) {
        if (!mkdir($directorio, 0755, true)) {
            exit("Error: no se pudo crear la carpeta de archivos adjuntos.");
        }
    }

    if (!is_writable($directorio)) {
        exit("Error: la carpeta de archivos adjuntos no permite guardar archivos.");
    }

    // Generar un nombre aleatorio para evitar nombres repetidos.
    $extension = $allowed_mimes[$mime];
    $nombre_archivo_final = bin2hex(random_bytes(16)) . '.' . $extension;

    $ruta_archivo = $directorio . $nombre_archivo_final;

    // Guardar el archivo en el servidor.
    if (!move_uploaded_file($tmp_name, $ruta_archivo)) {
        exit("Error: no se pudo guardar el archivo adjunto.");
    }
}

// Preparar la consulta SQL.
$sql = "INSERT INTO tickets
        (colegio_id, usuario_id, categoria, prioridad, estado,
         asunto, descripcion, archivo_adjunto)
        VALUES (?, ?, ?, ?, 'Pendiente', ?, ?, ?)";

$stmt = mysqli_prepare($conexion, $sql);

if ($stmt === false) {
    // Eliminar el archivo si no se pudo preparar la consulta.
    if ($ruta_archivo !== null && file_exists($ruta_archivo)) {
        unlink($ruta_archivo);
    }

    mysqli_close($conexion);
    exit("Error al preparar la consulta de la base de datos.");
}

// Tipos: i = entero; s = texto.
// Siete parámetros: i, i, s, s, s, s, s.
mysqli_stmt_bind_param(
    $stmt,
    "iisssss",
    $colegio_id,
    $usuario_id,
    $categoria,
    $prioridad,
    $asunto,
    $descripcion,
    $nombre_archivo_final
);

// Ejecutar la consulta.
if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);

    mostrarExito();
    exit();

} else {

    // Eliminar el archivo si el ticket no se pudo registrar.
    if ($ruta_archivo !== null && file_exists($ruta_archivo)) {
        unlink($ruta_archivo);
    }

    // Registrar el error real en los logs del servidor.
    error_log("Error al guardar el ticket: " . mysqli_stmt_error($stmt));

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);

    exit("No se pudo registrar el ticket. Intente nuevamente.");
}


// Mostrar la confirmación cuando el ticket se guarda correctamente.
function mostrarExito() {
    echo <<<'HTML'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Enviado</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        html, body {
            min-height: 100%;
            margin: 0;
        }

        body {
            min-height: 100vh;
            background-image: linear-gradient(
                to bottom,
                rgb(80, 4, 151),
                rgb(139, 20, 175)
            );
            background-attachment: fixed;
            color: #ffffff;
        }

        .card-glass {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
        }

        .text-muted-glass {
            color: rgba(255, 255, 255, 0.85);
        }

        .btn-morado {
            background-color: #ffffff;
            border-color: #ffffff;
            color: rgb(139, 20, 175);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-morado:hover {
            background-color: rgb(80, 4, 151);
            border-color: rgb(80, 4, 151);
            color: #ffffff;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card card-glass shadow-lg border-0 p-4 rounded-4">
                    <div class="card-body text-center">

                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success display-4"></i>
                        </div>

                        <h1 class="h3 fw-bold mb-3">
                            ¡Ticket enviado con éxito!
                        </h1>

                        <p class="text-muted-glass mb-4">
                            Su solicitud ha sido registrada correctamente.
                            El equipo de soporte técnico la revisará pronto.
                        </p>

                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
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
    </main>
</body>
</html>
HTML;
}
?>
```