<?php
require_once('../includes/config.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['documento'];
    $tipo = $_POST['tipo'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $password = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);

    try {
        $conexion = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insertar en usuarios como instructor (id_rol = 2)
        $stmt = $conexion->prepare("INSERT INTO usuarios 
            (id, id_tipo, nombres, apellidos, correo, contraseña, telefono, id_rol, id_estado, fecha_registro, nit)
            VALUES (:id, :tipo, :nombres, :apellidos, :correo, :password, :telefono, 2, 1, CURDATE(), 159)");

        $stmt->execute([
            ':id' => $id,
            ':tipo' => $tipo,
            ':nombres' => $nombres,
            ':apellidos' => $apellidos,
            ':correo' => $correo,
            ':password' => $password,
            ':telefono' => $telefono
        ]);

        $mensaje = "Instructor registrado exitosamente.";
    } catch (PDOException $e) {
        $error = "Error al registrar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Instructor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f8ff;
        }
        .card {
            border-radius: 15px;
            max-width: 700px;
            margin: auto;
        }
        .card-header {
            background-color: #0d6efd;
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            text-align: center;
            font-size: 1.5rem;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #0d6efd;
        }
    </style>
</head>
<body class="container py-5">

    <h2>Registrar Instructor</h2>

    <?php if (isset($mensaje)) echo "<div class='alert alert-success text-center'>$mensaje</div>"; ?>
    <?php if (isset($error)) echo "<div class='alert alert-danger text-center'>$error</div>"; ?>

    <div class="card shadow">
        <div class="card-header">Formulario de Registro</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>Documento</label>
                    <input type="number" name="documento" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Tipo de documento</label>
                    <select name="tipo" class="form-select" required>
                        <option value="1">Cédula</option>
                        <option value="2">Tarjeta de identidad</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Nombres</label>
                    <input type="text" name="nombres" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Correo</label>
                    <input type="email" name="correo" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Contraseña</label>
                    <input type="password" name="contraseña" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Registrar</button>
                    <a href="dashboard.php" class="btn btn-secondary">Volver al panel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
