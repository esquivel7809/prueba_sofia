<?php
session_start();
require_once('includes/config.php');
require_once('includes/auth.php');

$error = '';

// Redirige si ya está logueado
if (isset($_SESSION['user_id']) && isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'instructor') {
        header("Location: instructor/dashboard.php");
        exit();
    } elseif ($_SESSION['rol'] === 'aprendiz') {
        header("Location: aprendiz/dashboard.php");
        exit();
    }
}

// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login($email, $password)) {
        if (isInstructor()) {
            header("Location: instructor/dashboard.php");
        } elseif (isAprendiz()) {
            header("Location: aprendiz/dashboard.php");
        } else {
            $error = "Rol de usuario no reconocido.";
        }
        exit();
    } else {
        $error = "Correo o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="assets/img/icon2.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            background-image: url('assets/img/background.jpg');
            background-size: cover;
            background-position: center;
        }
        .logo {
            width: 180px;
            height: auto;
        }
        .bx {
            position: absolute;
            font-size: 1.7rem;
            right: 1rem;
            top: 75%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        input, select {
            height: 45px;
        }
    </style>
</head>
<body>
    <br><br><br><br>
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-9">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="row g-0">
                        <!-- Lado Bienvenida -->
                        <div class="col-md-6 d-flex flex-column justify-content-center p-5 rounded-start" style="background: #8ac5fe;">
                            <img src="assets/img/icon2.png" alt="Logo" class="logo mb-3 mx-auto d-block">
                            <h1 class="text-start">Hola</h1>
                            <h2 class="text-start">¡Bienvenido!</h2>
                            <p class="text-start">
                                Gracias por preferir <strong>TeamTalks</strong>.<br>
                                Estamos comprometidos con un ambiente<br>
                                de estudio especial para nuestros usuarios.
                            </p>
                        </div>

                        <!-- Lado Login -->
                        <div class="col-md-6 p-5">
                            <h3 class="mb-4">Iniciar sesión</h3>
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            <form method="POST" autocomplete="off">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Ingresa tu correo" required>
                                </div>
                                <div class="mb-3 position-relative">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Ingresa tu contraseña" required>
                                    <i class='bx bx-show' id="showpass" onclick="showpass()"></i>
                                </div>

                                <div class="mb-3 text-end">
                                    <a href="Recover_Password/recovery_form.php" class="link-secondary">¿Olvidaste la contraseña?</a>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="index.php" class="btn btn-secondary">Regresar</a>
                                    <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showpass() {
            const passw = document.getElementById("password");
            const iconshow = document.getElementById("showpass");
            if (passw.type === "password") {
                passw.type = "text";
                iconshow.classList.replace("bx-show", "bx-hide");
            } else {
                passw.type = "password";
                iconshow.classList.replace("bx-hide", "bx-show");
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
