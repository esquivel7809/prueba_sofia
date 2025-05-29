<?php
session_start();
require_once('../includes/config.php');

// Verifica que el admin esté logueado
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

try {
    $conexion = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conexion->query("SELECT u.id, u.nombres, u.apellidos, u.correo, u.telefono, f.id_ficha 
                              FROM usuarios u 
                              INNER JOIN fichas f ON u.id_ficha = f.id_ficha 
                              WHERE u.id_rol = 4");
    $aprendices = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al consultar los aprendices: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Aprendices</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="mb-4">Lista de Aprendices Registrados</h2>

    <a href="dashboard.php" class="btn btn-secondary mb-3">← Volver al Panel</a>

    <?php if (count($aprendices) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th># Documento</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Ficha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aprendices as $aprendiz): ?>
                        <tr>
                            <td><?= htmlspecialchars($aprendiz['id']) ?></td>
                            <td><?= htmlspecialchars($aprendiz['nombres']) ?></td>
                            <td><?= htmlspecialchars($aprendiz['apellidos']) ?></td>
                            <td><?= htmlspecialchars($aprendiz['correo']) ?></td>
                            <td><?= htmlspecialchars($aprendiz['telefono']) ?></td>
                            <td><?= htmlspecialchars($aprendiz['id_ficha']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No hay aprendices registrados.</div>
    <?php endif; ?>
</body>
</html>
