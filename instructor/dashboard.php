<?php
require_once '../includes/auth.php';

if (!isLoggedIn() || !isInstructor()) {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Obtener las fichas del instructor
$stmt = $conn->prepare("
    SELECT f.*, fo.nombre as nombre_formacion 
    FROM fichas f
    JOIN formacion fo ON f.id_formacion = fo.id_formacion
    WHERE f.id_instructor = :id_instructor
");
$stmt->bindParam(':id_instructor', $_SESSION['user_id']);
$stmt->execute();
$fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Instructor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>Bienvenido, <?php echo $_SESSION['nombres']; ?></h2>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Crear Nueva Tarea</h5>
                        <p class="card-text">Agrega una nueva actividad para tus aprendices.</p>
                        <a href="crear_tarea.php" class="btn btn-primary">Crear Tarea</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ver Tareas</h5>
                        <p class="card-text">Revisa y gestiona las tareas creadas.</p>
                        <a href="ver_tareas.php" class="btn btn-primary">Ver Tareas</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ver Entregas</h5>
                        <p class="card-text">Revisa las entregas de tus aprendices.</p>
                        <a href="ver_entregas.php" class="btn btn-primary">Ver Entregas</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h4>Mis Fichas</h4>
            <div class="list-group">
                <?php foreach ($fichas as $ficha): ?>
                    <a href="#" class="list-group-item list-group-item-action">
                        <?php echo $ficha['nombre_formacion'] . ' - Ficha ' . $ficha['id_ficha']; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>