<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>">TeamTalks</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (isLoggedIn()): ?>
                    <?php if (isInstructor()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/instructor/dashboard.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/instructor/crear_tarea.php">Crear Tarea</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/instructor/ver_entregas.php">Ver Entregas</a>
                        </li>
                    <?php elseif (isAprendiz()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/aprendiz/dashboard.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/aprendiz/tareas.php">Mis Tareas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/aprendiz/calificaciones.php">Calificaciones</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <span class="nav-link">Bienvenido, <?php echo $_SESSION['nombres']; ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/logout.php">Cerrar Sesión</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/login.php">Iniciar Sesión</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>