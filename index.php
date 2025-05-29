<?php
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirección según el rol
if (isLoggedIn()) {
    if (isInstructor()) {
        header("Location: instructor/dashboard.php");
        exit();
    } elseif (isAprendiz()) {
        header("Location: aprendiz/dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TeamTalks | Inicio</title>
    <link rel="icon" href="assets/img/icon2.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e7f3ff, #ffffff);
        }
        h5 {
            background-color: #0E4A86;
            border-radius: 35px;
            padding: 20px;
            color: white;
            width: fit-content;
        }
        .main-content {
            padding-top: 50px;
        }
        .images img {
            max-height: 300px;
            width: 48%;
        }
        @media (max-width: 768px) {
            .images img {
                width: 100%;
                max-height: none;
            }
        }
    </style>
</head>
<body>

    <?php include 'includes/design/header.php'; ?>

    <main class="container-fluid main-content py-4 py-lg-5">
        <div class="row align-items-center">
            <!-- Texto -->
            <div class="col-lg-6 col-md-12 order-lg-2 order-2 px-4 px-md-5">
                <div class="content text-center text-md-start">
                    <h5 class="mb-3 mb-lg-4">
                        <i class='bx bxs-bell'></i> Trabajo en equipo con TeamTalks <br class="d-none d-md-block"> My WorkPlace
                    </h5>
                    <h1 class="display-5 fw-bold mb-3 mb-lg-4">
                        Más <span class="text-primary fw-bold">participación</span> <br class="d-none d-md-block"> en el lugar de aprendizaje
                    </h1>
                    <div class="fs-5 mb-3">
                        <span class="text-primary fw-bold">TeamTalks</span>, su plataforma de colaboración.
                    </div>
                    <div class="fs-5 mb-3">
                        Más comunicación, participación de los estudiantes y docentes gracias a nuestras funciones como trabajos en grupo de manera simultánea.
                    </div>
                    <div class="fs-5 mb-4">
                        Todo esto lo encuentras en <span class="text-primary fw-bold">TeamTalks</span> sin ningún coste adicional.
                    </div>
                    <div class="fs-5 mb-3">
                        <span class="text-primary fw-bold">¿Listo para comenzar?</span>
                    </div>
                    
                </div>
            </div>

            <!-- Imágenes -->
            <div class="col-lg-6 col-md-12 order-lg-1 order-1 mb-4 mb-lg-0">
                <div class="images d-flex flex-wrap justify-content-center gap-3 px-2 px-md-0">
                    <img src="assets/img/img2.jpg" alt="Imagen 1" class="img-fluid rounded shadow-lg">
                    <img src="assets/img/img1.jpg" alt="Imagen 2" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/design/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
