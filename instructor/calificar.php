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
    header("Location: ver_entregas.php");
    exit();
}

$id_actividad_user = $_GET['id'];

$stmt = $conn->prepare("
    SELECT 
        au.*,
        a.titulo,
        a.fecha_entrega as fecha_limite,
        m.materia,
        f.id_ficha,
        fo.nombre as formacion,
        u.nombres,
        u.apellidos
    FROM actividades_user au
    JOIN actividades a ON au.id_actividad = a.id_actividad
    JOIN materia_ficha mf ON a.id_materia_ficha = mf.id_materia_ficha
    JOIN materias m ON mf.id_materia = m.id_materia
    JOIN fichas f ON mf.id_ficha = f.id_ficha
    JOIN formacion fo ON f.id_formacion = fo.id_formacion
    JOIN usuarios u ON au.id_user = u.id
    WHERE au.id_actividad_user = :id_actividad_user
    AND f.id_instructor = :id_instructor
    AND au.nota IS NULL
");
$stmt->bindParam(':id_actividad_user', $id_actividad_user);
$stmt->bindParam(':id_instructor', $_SESSION['user_id']);
$stmt->execute();

$entrega = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entrega) {
    header("Location: ver_entregas.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nota = $_POST['nota'];
    $comentarios = $_POST['comentarios'];
    
    // Validar nota entre 1.0 y 5.0
    if (!is_numeric($nota) || $nota < 1.0 || $nota > 5.0) {
        $error = "La nota debe ser un número entre 1.0 y 5.0";
    } else {
        $resultado = ($nota >= 3.0) ? "APROBADO" : "REPROBADO";
        
        try {
            $stmt = $conn->prepare("
                UPDATE actividades_user 
                SET nota = :nota, 
                    id_estado_actividad = 2, 
                    contenido = CONCAT(contenido, '\n\nComentarios del instructor: ', :comentarios, '\nResultado: ', :resultado)
                WHERE id_actividad_user = :id_actividad_user
            ");
            $stmt->bindParam(':nota', $nota);
            $stmt->bindParam(':comentarios', $comentarios);
            $stmt->bindParam(':resultado', $resultado);
            $stmt->bindParam(':id_actividad_user', $id_actividad_user);
            $stmt->execute();
            
            $success = "Tarea calificada exitosamente como $resultado!";
        } catch (PDOException $e) {
            $error = "Error al calificar la tarea: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Calificar Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>Calificar Tarea: <?php echo htmlspecialchars($entrega['titulo']); ?></h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <a href="ver_entregas.php" class="btn btn-primary">Volver a Entregas</a>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Detalles de la Entrega</h5>
                    <p><strong>Aprendiz:</strong> <?php echo htmlspecialchars($entrega['nombres'] . ' ' . $entrega['apellidos']); ?></p>
                    <p><strong>Materia:</strong> <?php echo htmlspecialchars($entrega['materia']); ?></p>
                    <p><strong>Ficha:</strong> <?php echo htmlspecialchars($entrega['formacion'] . ' - ' . $entrega['id_ficha']); ?></p>
                    <p><strong>Fecha de Entrega:</strong> <?php echo formatDate($entrega['fecha_entrega']); ?></p>
                    <p><strong>Respuesta:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($entrega['contenido'])); ?></p>
                    
                    <?php if ($entrega['archivo']): ?>
                        <p>
                            <strong>Archivo Adjunto:</strong> 
                            <a href="../assets/uploads/<?php echo htmlspecialchars($entrega['archivo']); ?>" download>
                                Descargar
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <form method="POST" novalidate>
                <div class="mb-3">
                    <label for="nota" class="form-label">Nota (1.0 - 5.0)</label>
                    <input type="number" class="form-control" id="nota" name="nota" min="1" max="5" step="0.1" required>
                </div>
                
                <div class="mb-3">
                    <label for="comentarios" class="form-label">Comentarios</label>
                    <textarea class="form-control" id="comentarios" name="comentarios" rows="3" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Guardar Calificación</button>
                <a href="ver_entregas.php" class="btn btn-secondary">Cancelar</a>
            </form>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
