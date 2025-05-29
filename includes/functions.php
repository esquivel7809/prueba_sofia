<?php
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