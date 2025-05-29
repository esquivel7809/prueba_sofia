<?php
session_start();

// Verificar que el administrador esté logueado
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

$admin = $_SESSION['admin'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f8ff;
        }
        .card {
            border-radius: 15px;
        }
        .header {
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
        }
        .btn-custom {
            background-color: #0d6efd;
            color: white;
        }
        .btn-custom:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="header text-center">
                <h2>Bienvenido, <?php echo htmlspecialchars($admin['nombres']); ?> (Administrador)</h2>
            </div>
            <div class="card-body">
                <p class="text-center">Selecciona una opción para gestionar el sistema:</p>
                <div class="d-flex flex-column align-items-center gap-3">
                    <a href="registrar_aprendiz.php" class="btn btn-custom w-50">Registrar Aprendiz</a>
                    <a href="registrar_instructor.php" class="btn btn-outline-primary w-50">Registrar Instructor</a>
                    <a href="ver_aprendices.php" class="btn btn-outline-primary w-50">Ver Lista de Aprendices</a>
                    <a href="ver_instructores.php" class="btn btn-outline-primary w-50">Ver Lista de Instructores</a>
                    <a href="logout.php" class="btn btn-danger w-50">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
