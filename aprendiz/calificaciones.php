<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAprendiz()) {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Obtener las calificaciones del aprendiz con escala 1.0 a 5.0
$stmt = $conn->prepare("
    SELECT 
        a.titulo,
        m.materia,
        f.id_ficha,
        fo.nombre as formacion,
        au.contenido,
        au.archivo as archivo_entrega,
        au.fecha_entrega,
        au.nota,
        a.fecha_entrega as fecha_limite,
        au.id_actividad_user, -- agregamos para el modal
        CASE 
            WHEN au.nota >= 3.0 THEN 'Aprobado'
            WHEN au.nota IS NULL THEN 'Sin calificar'
            ELSE 'Reprobado'
        END as estado
    FROM actividades_user au
    JOIN actividades a ON au.id_actividad = a.id_actividad
    JOIN materia_ficha mf ON a.id_materia_ficha = mf.id_materia_ficha
    JOIN materias m ON mf.id_materia = m.id_materia
    JOIN fichas f ON mf.id_ficha = f.id_ficha
    JOIN formacion fo ON f.id_formacion = fo.id_formacion
    WHERE au.id_user = :id_user
    AND au.nota IS NOT NULL
    ORDER BY au.fecha_entrega DESC
");
$stmt->bindParam(':id_user', $_SESSION['user_id']);
$stmt->execute();
$calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Calificaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>Mis Calificaciones</h2>
        
        <?php if (empty($calificaciones)): ?>
            <div class="alert alert-info">No tienes calificaciones aún.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Materia</th>
                            <th>Ficha</th>
                            <th>Fecha Entrega</th>
                            <th>Nota</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($calificaciones as $calificacion): ?>
                            <tr class="<?php 
                                if ($calificacion['estado'] == 'Aprobado') echo 'entregada';
                                elseif ($calificacion['estado'] == 'Reprobado') echo 'vencida';
                            ?>">
                                <td><?php echo htmlspecialchars($calificacion['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($calificacion['materia']); ?></td>
                                <td><?php echo htmlspecialchars($calificacion['formacion'] . ' - ' . $calificacion['id_ficha']); ?></td>
                                <td><?php echo formatDate($calificacion['fecha_entrega']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        if ($calificacion['nota'] >= 3.0) echo 'success';
                                        elseif ($calificacion['nota'] >= 2.0) echo 'warning';
                                        else echo 'danger';
                                    ?>">
                                        <?php echo number_format($calificacion['nota'], 1); ?>
                                    </span>
                                </td>
                                <td><?php echo $calificacion['estado']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detalleModal<?php echo $calificacion['id_actividad_user']; ?>">
                                        Detalles
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Modal para detalles -->
                            <div class="modal fade" id="detalleModal<?php echo $calificacion['id_actividad_user']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detalles de la Tarea</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Título:</strong> <?php echo htmlspecialchars($calificacion['titulo']); ?></p>
                                            <p><strong>Materia:</strong> <?php echo htmlspecialchars($calificacion['materia']); ?></p>
                                            <p><strong>Ficha:</strong> <?php echo htmlspecialchars($calificacion['formacion'] . ' - ' . $calificacion['id_ficha']); ?></p>
                                            <p><strong>Fecha de Entrega:</strong> <?php echo formatDate($calificacion['fecha_limite']); ?></p>
                                            <p><strong>Tu Entrega:</strong> <?php echo formatDate($calificacion['fecha_entrega']); ?></p>
                                            <p><strong>Nota:</strong> <?php echo number_format($calificacion['nota'], 1); ?></p>
                                            <p><strong>Estado:</strong> <?php echo $calificacion['estado']; ?></p>
                                            <hr>
                                            <p><strong>Tu Respuesta:</strong></p>
                                            <p><?php echo nl2br(htmlspecialchars($calificacion['contenido'])); ?></p>
                                            
                                            <?php if ($calificacion['archivo_entrega']): ?>
                                                <p>
                                                    <strong>Archivo Adjunto:</strong> 
                                                    <a href="../assets/uploads/<?php echo htmlspecialchars($calificacion['archivo_entrega']); ?>" download>
                                                        Descargar
                                                    </a>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
