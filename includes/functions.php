<?php
// Asegúrate de definir estas constantes antes de usarlas
if (!defined('MAX_FILE_SIZE')) define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB por ejemplo
if (!defined('ALLOWED_TYPES')) define('ALLOWED_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'png']);
if (!defined('UPLOAD_DIR')) define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');

function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

function uploadFile($file) {
    // Obtener información del archivo
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];
    
    // Obtener extensión del archivo
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Verificar errores
    if ($file_error !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Verificar tamaño
    if ($file_size > MAX_FILE_SIZE) {
        return false;
    }
    
    // Verificar tipo de archivo
    if (!in_array($file_ext, ALLOWED_TYPES)) {
        return false;
    }
    
    // Generar nombre único para el archivo
    $new_file_name = uniqid('', true) . '.' . $file_ext;
    $upload_path = UPLOAD_DIR . $new_file_name;
    
    // Crear la carpeta si no existe
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }

    // Mover el archivo a la ubicación final
    if (move_uploaded_file($file_tmp, $upload_path)) {
        return $new_file_name;
    }
    
    return false;
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}