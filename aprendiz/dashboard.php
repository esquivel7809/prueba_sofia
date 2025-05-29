<?php
// Iniciar sesión antes de cualquier output
session_start();

require_once '../includes/auth.php';

// Verificar si el usuario está logueado y es aprendiz
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

if (!isAprendiz()) {
    // Redirigir a otro panel si es instructor o rol distinto
    header("Location: ../instructor/dashboard.php");
    exit();
}

// Conexión a la base de datos
require_once '../includes/config.php';

$db = new Database();
$conn = $db->getConnection();

// Obtener las fichas del aprendiz
$stmt = $conn->prepare("
    SELECT f.*, fo.nombre as nombre_formacion 
    FROM fichas f
    JOIN formacion fo ON f.id_formacion = fo.id_formacion
    JOIN user_ficha uf ON f.id_ficha = uf.id_ficha
    WHERE uf.id_user = :id_user
");
$stmt->bindParam(':id_user', $_SESSION['user_id']);
$stmt->execute();
$fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Aprendiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombres']); ?></h2>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Mis Tareas</h5>
                        <p class="card-text">Revisa las tareas asignadas y sus fechas de entrega.</p>
                        <a href="tareas.php" class="btn btn-primary">Ver Tareas</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Mis Calificaciones</h5>
                        <p class="card-text">Revisa las calificaciones de tus tareas entregadas.</p>
                        <a href="calificaciones.php" class="btn btn-success">Ver Calificaciones</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-5">
            <h4>Mis Fichas</h4>
            <div class="list-group">
                <?php foreach ($fichas as $ficha): ?>
                    <a href="#" class="list-group-item list-group-item-action">
                        <?php echo htmlspecialchars($ficha['nombre_formacion'] . ' - Ficha ' . $ficha['id_ficha']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
