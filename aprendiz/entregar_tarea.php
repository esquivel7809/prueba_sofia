<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAprendiz()) {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';

// Verificar si la tarea existe y está disponible para entrega
if (!isset($_GET['id'])) {
    header("Location: tareas.php");
    exit();
}

$id_actividad = $_GET['id'];

$stmt = $conn->prepare("
    SELECT a.*, m.materia, f.id_ficha, fo.nombre as formacion
    FROM actividades a
    JOIN materia_ficha mf ON a.id_materia_ficha = mf.id_materia_ficha
    JOIN materias m ON mf.id_materia = m.id_materia
    JOIN fichas f ON mf.id_ficha = f.id_ficha
    JOIN formacion fo ON f.id_formacion = fo.id_formacion
    JOIN user_ficha uf ON f.id_ficha = uf.id_ficha
    WHERE a.id_actividad = :id_actividad 
    AND uf.id_user = :id_user
    AND NOW() <= a.fecha_entrega
    AND NOT EXISTS (
        SELECT 1 FROM actividades_user au 
        WHERE au.id_actividad = a.id_actividad 
        AND au.id_user = :id_user
    )
");
$stmt->bindParam(':id_actividad', $id_actividad);
$stmt->bindParam(':id_user', $_SESSION['user_id']);
$stmt->execute();

$tarea = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tarea) {
    header("Location: tareas.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contenido = $_POST['contenido'];
    
    // Manejo de archivo
    $archivo_nombre = '';
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
        $archivo_nombre = uploadFile($_FILES['archivo']);
        if (!$archivo_nombre) {
            $error = "Error al subir el archivo. Asegúrese de que es del tipo correcto y no excede el tamaño máximo.";
        }
    }
    
    if (empty($error)) {
        try {
            // Insertar la entrega
            $stmt = $conn->prepare("
                INSERT INTO actividades_user (
                    id_actividad, 
                    id_estado_actividad, 
                    contenido, 
                    archivo, 
                    fecha_entrega, 
                    id_user
                ) VALUES (
                    :id_actividad, 
                    1, 
                    :contenido, 
                    :archivo, 
                    NOW(), 
                    :id_user
                )
            ");
            $stmt->bindParam(':id_actividad', $id_actividad);
            $stmt->bindParam(':contenido', $contenido);
            $stmt->bindParam(':archivo', $archivo_nombre);
            $stmt->bindParam(':id_user', $_SESSION['user_id']);
            $stmt->execute();
            
            $success = "Tarea entregada exitosamente!";
        } catch (PDOException $e) {
            $error = "Error al entregar la tarea: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregar Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>Entregar Tarea: <?php echo htmlspecialchars($tarea['titulo']); ?></h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <a href="tareas.php" class="btn btn-primary">Volver a Mis Tareas</a>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Detalles de la Tarea</h5>
                    <p><strong>Materia:</strong> <?php echo htmlspecialchars($tarea['materia']); ?></p>
                    <p><strong>Ficha:</strong> <?php echo htmlspecialchars($tarea['formacion'] . ' - ' . $tarea['id_ficha']); ?></p>
                    <p><strong>Fecha de Entrega:</strong> <?php echo formatDate($tarea['fecha_entrega']); ?></p>
                    <p><strong>Descripción:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($tarea['descripcion'])); ?></p>
                    
                    <?php if ($tarea['archivo']): ?>
                        <p>
                            <strong>Archivo Adjunto:</strong> 
                            <a href="../assets/uploads/<?php echo htmlspecialchars($tarea['archivo']); ?>" download>
                                Descargar
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="contenido" class="form-label">Respuesta</label>
                    <textarea class="form-control" id="contenido" name="contenido" rows="5" required></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="archivo" class="form-label">Archivo Adjunto (Opcional)</label>
                    <input type="file" class="form-control" id="archivo" name="archivo">
                    <div class="form-text">Formatos permitidos: PDF, DOC, DOCX, ZIP, RAR, JPG, PNG. Tamaño máximo: 5MB</div>
                </div>
                
                <button type="submit" class="btn btn-primary">Entregar Tarea</button>
                <a href="tareas.php" class="btn btn-secondary">Cancelar</a>
            </form>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>