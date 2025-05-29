<?php
require_once 'db.php';

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isInstructor() {
    return isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 3;
}

function isAprendiz() {
    return isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 4;
}

function login($email, $password) {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $user['contraseña'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nombres'] = $user['nombres'];
            $_SESSION['apellidos'] = $user['apellidos'];
            $_SESSION['id_rol'] = $user['id_rol'];
            $_SESSION['correo'] = $user['correo'];
            
            return true;
        }
    }
    return false;
}

function logout() {
    session_unset();
    session_destroy();
}