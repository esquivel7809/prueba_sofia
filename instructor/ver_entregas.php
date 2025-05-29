<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isInstructor()) {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$user_id = $_SESSION['user_id'];

$msg = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'calificado') {
    $msg = "La tarea fue calificada exitosamente.";
}

// Obtener entregas pendientes de calificación para este instructor
$stmt = $conn->prepare("
    SELECT 
        au.id_actividad_user,
        a.titulo,
        u.nombres,
        u.apellidos,
        m.materia,
        au.fecha_entrega,
        f.id_ficha,
        fo.nombre as formacion
    FROM actividades_user au
    JOIN actividades a ON au.id_actividad = a.id_actividad
    JOIN materia_ficha mf ON a.id_materia_ficha = mf.id_materia_ficha
    JOIN materias m ON mf.id_materia = m.id_materia
    JOIN fichas f ON mf.id_ficha = f.id_ficha
    LEFT JOIN formacion fo ON f.id_formacion = fo.id_formacion
    JOIN usuarios u ON au.id_user = u.id
    WHERE au.nota IS NULL
    -- AND mf.id_instructor = :id_instructor
    ORDER BY au.fecha_entrega DESC
");

// Prueba sin filtro para ver si hay entregas
// $stmt->bindParam(':id_instructor', $user_id);
$stmt->execute();

$entregas = $stmt->fetchAll(PDO::FETCH_ASSOC);

error_log("Entregas encontradas: " . count($entregas));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ver Entregas Pendientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>Entregas Pendientes de Calificación</h2>
        
        <?php if ($msg): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        
        <?php if (count($entregas) === 0): ?>
            <div class="alert alert-info">No hay entregas pendientes de calificación.</div>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Título Actividad</th>
                        <th>Aprendiz</th>
                        <th>Materia</th>
                        <th>Ficha</th>
                        <th>Fecha de Entrega</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entregas as $entrega): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($entrega['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($entrega['nombres'] . ' ' . $entrega['apellidos']); ?></td>
                            <td><?php echo htmlspecialchars($entrega['materia']); ?></td>
                            <td><?php echo htmlspecialchars($entrega['formacion'] . ' - ' . $entrega['id_ficha']); ?></td>
                            <td><?php echo htmlspecialchars(formatDate($entrega['fecha_entrega'])); ?></td>
                            <td>
                                <a href="calificar.php?id=<?php echo urlencode($entrega['id_actividad_user']); ?>" class="btn btn-primary btn-sm">Calificar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <a href="dashboard.php" class="btn btn-secondary mt-3">Volver al Panel</a>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>