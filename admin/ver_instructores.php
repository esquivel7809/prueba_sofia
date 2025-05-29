<?php
session_start();
require_once('../includes/config.php');

// Verifica que el administrador esté logueado
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

try {
    $conexion = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conexion->query("
        SELECT id, nombres, apellidos, correo, telefono 
        FROM usuarios 
        WHERE id_rol = 2
    ");
    $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al consultar instructores: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Instructores</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
        }
        h2 {
            color: #0d6efd;
        }
        .table th {
            background-color: #e1efff;
        }
    </style>
</head>
<body class="container mt-5">
    <h2 class="mb-4 text-center">Lista de Instructores Registrados</h2>

    <a href="dashboard.php" class="btn btn-secondary mb-3">← Volver al Panel</a>

    <?php if (empty($instructores)): ?>
        <div class="alert alert-info">No hay instructores registrados.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover shadow-sm">
                <thead class="table-primary text-center">
                    <tr>
                        <th># Documento</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($instructores as $instructor): ?>
                        <tr>
                            <td><?= htmlspecialchars($instructor['id']) ?></td>
                            <td><?= htmlspecialchars($instructor['nombres']) ?></td>
                            <td><?= htmlspecialchars($instructor['apellidos']) ?></td>
                            <td><?= htmlspecialchars($instructor['correo']) ?></td>
                            <td><?= htmlspecialchars($instructor['telefono']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>
