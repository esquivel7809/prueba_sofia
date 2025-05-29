<?php
require_once 'db.php';

// ✅ Iniciar sesión solo si no ha sido iniciada aún
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si el usuario ha iniciado sesión.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Verifica si el usuario es instructor (rol 3).
 */
function isInstructor() {
    return isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 3;
}

/**
 * Verifica si el usuario es aprendiz (rol 4).
 */
function isAprendiz() {
    return isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 4;
}

/**
 * Intenta iniciar sesión con email y contraseña.
 * Retorna true si las credenciales son válidas, false si no.
 */
function login($email, $password) {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['contraseña'])) {
            // Establece las variables de sesión
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['nombres']   = $user['nombres'];
            $_SESSION['apellidos'] = $user['apellidos'];
            $_SESSION['id_rol']    = $user['id_rol'];
            $_SESSION['correo']    = $user['correo'];
            return true;
        }
    }

    return false;
}

/**
 * Cierra la sesión del usuario.
 */
function logout() {
    session_unset();
    session_destroy();
}
