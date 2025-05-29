<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isInstructor()) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ver_entregas.php"); // Redirige a la lista general
    exit();
}

$id_actividad_user = $_GET['id'];

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("
    SELECT 
        au.*,
        a.titulo,
        a.fecha_entrega as fecha_limite,
        m.materia,
        f.id_ficha,
        fo.nombre as formacion,
        u.nombres,
        u.apellidos,
        u.documento
    FROM actividades_user au
    JOIN actividades a ON au.id_actividad = a.id_actividad
    JOIN materia_ficha mf ON a.id_materia_ficha = mf.id_materia_ficha
    JOIN materias m ON mf.id_materia = m.id_materia
    JOIN fichas f ON mf.id_ficha = f.id_ficha
    LEFT JOIN formacion fo ON f.id_formacion = fo.id_formacion
    JOIN usuarios u ON au.id_user = u.id
    WHERE au.id_actividad_user = :id
    AND mf.id_instructor = :id_instructor
");
$stmt->bindParam(':id', $id_actividad_user);
$stmt->bindParam(':id_instructor', $_SESSION['user_id']);
$stmt->execute();

$entrega = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entrega) {
    header("Location: ver_entregas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entrega de Actividad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Entrega de <?php echo htmlspecialchars($entrega['titulo']); ?></h2>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Información del Aprendiz</h5>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($entrega['nombres'] . ' ' . $entrega['apellidos']); ?></p>
                <p><strong>Documento:</strong> <?php echo htmlspecialchars($entrega['documento']); ?></p>
                <p><strong>Materia:</strong> <?php echo htmlspecialchars($entrega['materia']); ?></p>
                <p><strong>Ficha:</strong> <?php echo htmlspecialchars($entrega['formacion'] . ' - ' . $entrega['id_ficha']); ?></p>
                <p><strong>Fecha límite:</strong> <?php echo formatDate($entrega['fecha_limite']); ?></p>
                <p><strong>Fecha de envío:</strong> <?php echo formatDate($entrega['fecha_entrega']); ?></p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Respuesta del Aprendiz</h5>
                <p><?php echo nl2br(htmlspecialchars($entrega['contenido'])); ?></p>

                <?php if ($entrega['archivo'] && file_exists("../assets/uploads/" . $entrega['archivo'])): ?>
                    <p>
                        <strong>Archivo Adjunto:</strong> 
                        <a href="../assets/uploads/<?php echo htmlspecialchars($entrega['archivo']); ?>" download>
                            Descargar archivo
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="mb-3">
            <?php if ($entrega['nota'] === null): ?>
                <form action="calificar.php" method="post" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="id" value="<?php echo $entrega['id_actividad_user']; ?>">
                    <label for="nota" class="form-label mb-0"><strong>Nota:</strong></label>
                    <input type="number" name="nota" id="nota" class="form-control" min="0" max="5" step="0.1" required style="width:100px;">
                    <button type="submit" class="btn btn-success">Calificar Entrega</button>
                </form>
            <?php else: ?>
                <div class="alert alert-info">
                    <strong>Ya calificada:</strong> Nota: <?php echo htmlspecialchars($entrega['nota']); ?>
                </div>
            <?php endif; ?>
            <a href="ver_entregas.php" class="btn btn-secondary">Volver</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
