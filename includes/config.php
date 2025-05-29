<?php
// Configuración básica
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'teamtalks');
define('BASE_URL', 'http://localhost/prueba_sofia');

// Configuración para subida de archivos
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_TYPES', ['pdf', 'doc', 'docx', 'zip', 'rar', 'jpg', 'png']);