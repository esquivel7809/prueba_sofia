<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAprendiz()) {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Obtener las tareas asignadas al aprendiz
$stmt = $conn->prepare("
    SELECT 
        a.id_actividad, 
        a.titulo, 
        a.descripcion, 
        a.archivo as archivo_tarea, 
        a.fecha_entrega,
        m.materia,
        f.id_ficha,
        fo.nombre as formacion,
        au.id_actividad_user,
        au.archivo as archivo_entrega,
        au.fecha_entrega as fecha_entrega_aprendiz,
        au.nota,
        CASE 
            WHEN au.id_actividad_user IS NULL THEN 'Pendiente'
            WHEN au.id_actividad_user IS NOT NULL AND au.nota IS NULL THEN 'Entregada'
            ELSE 'Calificada'
        END as estado,
        CASE 
            WHEN NOW() > a.fecha_entrega THEN 'Vencida'
            ELSE 'Disponible'
        END as estado_entrega
    FROM actividades a
    JOIN materia_ficha mf ON a.id_materia_ficha = mf.id_materia_ficha
    JOIN materias m ON mf.id_materia = m.id_materia
    JOIN fichas f ON mf.id_ficha = f.id_ficha
    JOIN formacion fo ON f.id_formacion = fo.id_formacion
    JOIN user_ficha uf ON f.id_ficha = uf.id_ficha
    LEFT JOIN actividades_user au ON a.id_actividad = au.id_actividad AND au.id_user = :id_user
    WHERE uf.id_user = :id_user
    ORDER BY a.fecha_entrega DESC
");
$stmt->bindParam(':id_user', $_SESSION['user_id']);
$stmt->execute();
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .vencida {
            background-color: #ffdddd;
        }
        .entregada {
            background-color: #ddffdd;
        }
        .calificada {
            background-color: #d0ebff;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>Mis Tareas</h2>
        
        <?php if (empty($tareas)): ?>
            <div class="alert alert-info">No tienes tareas asignadas actualmente.</div>
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tareas as $tarea): ?>
                            <tr class="<?php 
                                if ($tarea['estado_entrega'] == 'Vencida') echo 'vencida';
                                elseif ($tarea['estado'] == 'Entregada') echo 'entregada';
                                elseif ($tarea['estado'] == 'Calificada') echo 'calificada';
                            ?>">
                                <td><?php echo htmlspecialchars($tarea['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($tarea['materia']); ?></td>
                                <td><?php echo htmlspecialchars($tarea['formacion'] . ' - ' . $tarea['id_ficha']); ?></td>
                                <td><?php echo formatDate($tarea['fecha_entrega']); ?></td>
                                <td>
                                    <?php echo $tarea['estado']; ?>
                                    <?php if ($tarea['estado_entrega'] == 'Vencida'): ?>
                                        <span class="badge bg-danger">Vencida</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="ver_tarea.php?id=<?php echo $tarea['id_actividad']; ?>" class="btn btn-sm btn-info">Ver</a>
                                    <?php if ($tarea['estado_entrega'] == 'Disponible' && $tarea['estado'] == 'Pendiente'): ?>
                                        <a href="entregar_tarea.php?id=<?php echo $tarea['id_actividad']; ?>" class="btn btn-sm btn-primary">Entregar</a>
                                    <?php elseif ($tarea['estado'] == 'Calificada'): ?>
                                        <span class="badge bg-success">Nota: <?php echo $tarea['nota']; ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>