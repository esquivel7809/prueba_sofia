<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isInstructor()) {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Obtener las tareas creadas por el instructor
$stmt = $conn->prepare("
    SELECT 
        a.id_actividad,
        a.titulo,
        a.descripcion,
        a.archivo,
        a.fecha_entrega,
        m.materia,
        f.id_ficha,
        fo.nombre as formacion,
        COUNT(au.id_actividad_user) as entregas,
        SUM(CASE WHEN au.nota IS NOT NULL THEN 1 ELSE 0 END) as calificadas,
        CASE 
            WHEN NOW() > a.fecha_entrega THEN 'Vencida'
            ELSE 'Activa'
        END as estado
    FROM actividades a
    JOIN materia_ficha mf ON a.id_materia_ficha = mf.id_materia_ficha
    JOIN materias m ON mf.id_materia = m.id_materia
    JOIN fichas f ON mf.id_ficha = f.id_ficha
    LEFT JOIN formacion fo ON f.id_formacion = fo.id_formacion
    LEFT JOIN actividades_user au ON a.id_actividad = au.id_actividad
    GROUP BY a.id_actividad
    ORDER BY a.fecha_entrega DESC
");
// $stmt->bindParam(':id_instructor', $_SESSION['user_id']); // Ya no es necesario
$stmt->execute();
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tareas Creadas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .vencida {
            background-color: #ffdddd;
        }
        .activa {
            background-color: #ddffdd;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mis Tareas Creadas</h2>
            <a href="crear_tarea.php" class="btn btn-primary">Crear Nueva Tarea</a>
        </div>
        
        <?php if (empty($tareas)): ?>
            <div class="alert alert-info">No has creado ninguna tarea aún.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Materia</th>
                            <th>Ficha</th>
                            <th>Fecha Entrega</th>
                            <th>Estado</th>
                            <th>Entregas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tareas as $tarea): ?>
                            <tr class="<?php echo strtolower($tarea['estado']); ?>">
                                <td><?php echo htmlspecialchars($tarea['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($tarea['materia']); ?></td>
                                <td><?php echo htmlspecialchars($tarea['formacion'] . ' - ' . $tarea['id_ficha']); ?></td>
                                <td><?php echo formatDate($tarea['fecha_entrega']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $tarea['estado'] == 'Activa' ? 'success' : 'danger'; ?>">
                                        <?php echo $tarea['estado']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $tarea['entregas']; ?> entregas
                                    <?php if ($tarea['entregas'] > 0): ?>
                                        (<?php echo $tarea['calificadas']; ?> calificadas)
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="ver_tarea_detalle.php?id=<?php echo $tarea['id_actividad']; ?>" class="btn btn-sm btn-info">Ver</a>
                                        <a href="ver_entregas.php?filtro=<?php echo $tarea['id_actividad']; ?>" class="btn btn-sm btn-primary">Entregas</a>
                                        <?php if ($tarea['estado'] == 'Activa'): ?>
                                            <a href="editar_tarea.php?id=<?php echo $tarea['id_actividad']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>