<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isInstructor()) {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

if (!isset($_GET['id'])) {
    header("Location: ver_tareas.php");
    exit();
}

$id_actividad = $_GET['id'];

// Obtener los detalles de la tarea
$stmt = $conn->prepare("
    SELECT 
        a.*,
        m.materia,
        f.id_ficha,
        fo.nombre as formacion,
        COUNT(au.id_actividad_user) as total_entregas,
        SUM(CASE WHEN au.nota IS NOT NULL THEN 1 ELSE 0 END) as entregas_calificadas
    FROM actividades a
    JOIN materia_ficha mf ON a.id_materia_ficha = mf.id_materia_ficha
    JOIN materias m ON mf.id_materia = m.id_materia
    JOIN fichas f ON mf.id_ficha = f.id_ficha
    JOIN formacion fo ON f.id_formacion = fo.id_formacion
    LEFT JOIN actividades_user au ON a.id_actividad = au.id_actividad
    WHERE a.id_actividad = :id_actividad
    AND f.id_instructor = :id_instructor
    GROUP BY a.id_actividad
");
$stmt->bindParam(':id_actividad', $id_actividad);
$stmt->bindParam(':id_instructor', $_SESSION['user_id']);
$stmt->execute();

$tarea = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tarea) {
    header("Location: ver_tareas.php");
    exit();
}

// Obtener estadísticas de calificaciones
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total,
        AVG(nota) as promedio,
        MIN(nota) as minima,
        MAX(nota) as maxima
    FROM actividades_user
    WHERE id_actividad = :id_actividad
    AND nota IS NOT NULL
");
$stmt->bindParam(':id_actividad', $id_actividad);
$stmt->execute();
$estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <a href="ver_tareas.php" class="btn btn-secondary mb-3">← Volver a Mis Tareas</a>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?php echo htmlspecialchars($tarea['titulo']); ?></h3>
                <p class="card-subtitle text-muted">
                    <?php echo htmlspecialchars($tarea['formacion'] . ' - Ficha ' . $tarea['id_ficha'] . ' | ' . $tarea['materia']); ?>
                </p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5>Descripción:</h5>
                        <p><?php echo nl2br(htmlspecialchars($tarea['descripcion'])); ?></p>
                        
                        <?php if ($tarea['archivo']): ?>
                            <h5 class="mt-4">Archivo Adjunto:</h5>
                            <a href="../assets/uploads/<?php echo htmlspecialchars($tarea['archivo']); ?>" download class="btn btn-outline-primary">
                                Descargar Archivo
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Información de Entrega</h5>
                                <p><strong>Fecha Límite:</strong> <?php echo formatDate($tarea['fecha_entrega']); ?></p>
                                <p><strong>Estado:</strong> 
                                    <span class="badge bg-<?php echo (strtotime($tarea['fecha_entrega']) > time()) ? 'success' : 'danger'; ?>">
                                        <?php echo (strtotime($tarea['fecha_entrega']) > time()) ? 'Activa' : 'Vencida'; ?>
                                    </span>
                                </p>
                                <hr>
                                <p><strong>Entregas:</strong> <?php echo $tarea['total_entregas']; ?></p>
                                <p><strong>Calificadas:</strong> <?php echo $tarea['entregas_calificadas']; ?></p>
                                
                                <?php if ($estadisticas['total'] > 0): ?>
                                    <hr>
                                    <h6>Estadísticas de Calificaciones:</h6>
                                    <p><strong>Promedio:</strong> <?php echo round($estadisticas['promedio'], 1); ?></p>
                                    <p><strong>Mínima:</strong> <?php echo $estadisticas['minima']; ?></p>
                                    <p><strong>Máxima:</strong> <?php echo $estadisticas['maxima']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="ver_entregas.php?filtro=<?php echo $tarea['id_actividad']; ?>" class="btn btn-primary">
                    Ver Todas las Entregas
                </a>
                <?php if (strtotime($tarea['fecha_entrega']) > time()): ?>
                    <a href="editar_tarea.php?id=<?php echo $tarea['id_actividad']; ?>" class="btn btn-warning">Editar Tarea</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>