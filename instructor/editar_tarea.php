<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isInstructor()) {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: ver_tareas.php");
    exit();
}

$id_actividad = $_GET['id'];

// Verificar que la tarea existe y pertenece al instructor
$stmt = $conn->prepare("
    SELECT a.*, m.materia, m.id_materia, f.id_ficha, fo.nombre as formacion
    FROM actividades a
    JOIN materia_ficha mf ON a.id_materia_ficha = mf.id_materia_ficha
    JOIN materias m ON mf.id_materia = m.id_materia
    JOIN fichas f ON mf.id_ficha = f.id_ficha
    JOIN formacion fo ON f.id_formacion = fo.id_formacion
    WHERE a.id_actividad = :id_actividad
    AND f.id_instructor = :id_instructor
    AND a.fecha_entrega > NOW()
");
$stmt->bindParam(':id_actividad', $id_actividad);
$stmt->bindParam(':id_instructor', $_SESSION['user_id']);
$stmt->execute();

$tarea = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tarea) {
    header("Location: ver_tareas.php");
    exit();
}

// Obtener materias y fichas para selects
$materias = [];
$fichas = [];

try {
    $stmt = $conn->prepare("SELECT * FROM materias");
    $stmt->execute();
    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("
        SELECT f.id_ficha, fo.nombre as formacion 
        FROM fichas f
        JOIN formacion fo ON f.id_formacion = fo.id_formacion
        WHERE f.id_instructor = :id_instructor
    ");
    $stmt->bindParam(':id_instructor', $_SESSION['user_id']);
    $stmt->execute();
    $fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener datos: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_entrega = $_POST['fecha_entrega'];
    $id_materia = $_POST['id_materia'];
    $id_ficha = $_POST['id_ficha'];
    $archivo_nombre = $tarea['archivo'];

    // Eliminar archivo si lo pide el usuario
    if (isset($_POST['eliminar_archivo']) && $archivo_nombre) {
        if (file_exists(UPLOAD_DIR . $archivo_nombre)) {
            unlink(UPLOAD_DIR . $archivo_nombre);
        }
        $archivo_nombre = null;
    }

    // Subir nuevo archivo si se envió
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
        if ($archivo_nombre && file_exists(UPLOAD_DIR . $archivo_nombre)) {
            unlink(UPLOAD_DIR . $archivo_nombre);
        }

        $archivo_subido = uploadFile($_FILES['archivo']);
        if ($archivo_subido) {
            $archivo_nombre = $archivo_subido;
        } else {
            $error = "Error al subir el archivo.";
        }
    }

    if (empty($error)) {
        try {
            if ($id_materia != $tarea['id_materia'] || $id_ficha != $tarea['id_ficha']) {
                $stmt = $conn->prepare("
                    SELECT id_materia_ficha FROM materia_ficha
                    WHERE id_materia = :id_materia AND id_ficha = :id_ficha
                ");
                $stmt->bindParam(':id_materia', $id_materia);
                $stmt->bindParam(':id_ficha', $id_ficha);
                $stmt->execute();
                $materia_ficha = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$materia_ficha) {
                    $stmt = $conn->prepare("
                        INSERT INTO materia_ficha (id_materia, id_ficha, id_instructor)
                        VALUES (:id_materia, :id_ficha, :id_instructor)
                    ");
                    $stmt->bindParam(':id_materia', $id_materia);
                    $stmt->bindParam(':id_ficha', $id_ficha);
                    $stmt->bindParam(':id_instructor', $_SESSION['user_id']);
                    $stmt->execute();
                    $id_materia_ficha = $conn->lastInsertId();
                } else {
                    $id_materia_ficha = $materia_ficha['id_materia_ficha'];
                }
            } else {
                $id_materia_ficha = $tarea['id_materia_ficha'];
            }

            // Actualizar tarea
            $stmt = $conn->prepare("
                UPDATE actividades SET
                    titulo = :titulo,
                    descripcion = :descripcion,
                    fecha_entrega = :fecha_entrega,
                    id_materia_ficha = :id_materia_ficha,
                    archivo = :archivo
                WHERE id_actividad = :id_actividad
            ");
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':fecha_entrega', $fecha_entrega);
            $stmt->bindParam(':id_materia_ficha', $id_materia_ficha);
            $stmt->bindParam(':archivo', $archivo_nombre);
            $stmt->bindParam(':id_actividad', $id_actividad);
            $stmt->execute();

            header("Location: ver_tarea_detalle.php?id=$id_actividad");
            exit();
        } catch (PDOException $e) {
            $error = "Error al actualizar la tarea: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h2>Editar Tarea: <?php echo htmlspecialchars($tarea['titulo']); ?></h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" class="form-control" name="titulo" value="<?php echo htmlspecialchars($tarea['titulo']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="5" required><?php echo htmlspecialchars($tarea['descripcion']); ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Materia</label>
                <select class="form-select" name="id_materia" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($materias as $materia): ?>
                        <option value="<?php echo $materia['id_materia']; ?>" <?php echo ($materia['id_materia'] == $tarea['id_materia']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($materia['materia']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Ficha</label>
                <select class="form-select" name="id_ficha" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($fichas as $ficha): ?>
                        <option value="<?php echo $ficha['id_ficha']; ?>" <?php echo ($ficha['id_ficha'] == $tarea['id_ficha']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($ficha['formacion'] . ' - Ficha ' . $ficha['id_ficha']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha de entrega</label>
            <input type="datetime-local" class="form-control" name="fecha_entrega" 
                   value="<?php echo date('Y-m-d\TH:i', strtotime($tarea['fecha_entrega'])); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Archivo</label>
            <?php if ($tarea['archivo']): ?>
                <p>
                    Actual: <a href="../assets/uploads/<?php echo htmlspecialchars($tarea['archivo']); ?>" download>
                        <?php echo htmlspecialchars($tarea['archivo']); ?>
                    </a>
                    <label class="ms-3">
                        <input type="checkbox" name="eliminar_archivo" value="1"> Eliminar archivo
                    </label>
                </p>
            <?php endif; ?>
            <input type="file" class="form-control" name="archivo">
            <div class="form-text">PDF, DOC, DOCX, ZIP, JPG, PNG. Máx: 5MB</div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="ver_tarea_detalle.php?id=<?php echo $tarea['id_actividad']; ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

</body>
</html>
