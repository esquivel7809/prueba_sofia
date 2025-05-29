<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isInstructor()) {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Obtener las materias del instructor
$materias = [];

try {
    // Obtener todas las materias de materia_ficha y mostrar el nombre de la materia
    $stmt = $conn->prepare("
        SELECT mf.id_materia_ficha, m.materia, mf.id_ficha
        FROM materia_ficha mf
        JOIN materias m ON mf.id_materia = m.id_materia
        ORDER BY m.materia
    ");
    $stmt->execute();
    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener datos: " . $e->getMessage();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_entrega = $_POST['fecha_entrega'];
    $id_materia_ficha = $_POST['id_materia_ficha'];
    
    // Manejo de archivo
    $archivo_nombre = '';
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
        $archivo_nombre = uploadFile($_FILES['archivo']);
        if (!$archivo_nombre) {
            $error = "Error al subir el archivo. Asegúrese de que es del tipo correcto y no excede el tamaño máximo.";
        }
    }
    
    if (empty($error)) {
        try {
            // Insertar directamente la actividad asociada a la materia
            $stmt = $conn->prepare("
                INSERT INTO actividades (id_materia_ficha, titulo, descripcion, archivo, fecha_entrega )
                VALUES (:id_materia_ficha, :titulo, :descripcion, :archivo, :fecha_entrega)
            ");
            $stmt->bindParam(':id_materia_ficha', $id_materia_ficha);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':archivo', $archivo_nombre);
            $stmt->bindParam(':fecha_entrega', $fecha_entrega);
            
            $stmt->execute();
            
            $success = "Tarea creada exitosamente!";
        } catch (PDOException $e) {
            $error = "Error al crear la tarea: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="form-container">
            <h2 class="text-center mb-4">Crear Nueva Tarea</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título de la Tarea</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                </div>
                
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="id_materia_ficha" class="form-label">Materia</label>
                        <select class="form-select" id="id_materia_ficha" name="id_materia_ficha" required>
                            <option value="">Seleccione una materia</option>
                            <?php foreach ($materias as $materia): ?>
                                <option value="<?php echo htmlspecialchars($materia['id_materia_ficha']); ?>">
                                    <?php echo htmlspecialchars($materia['materia'] . " (Ficha: " . $materia['id_ficha'] . ")"); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="fecha_entrega" class="form-label">Fecha de Entrega</label>
                    <input type="datetime-local" class="form-control" id="fecha_entrega" name="fecha_entrega" 
                           min="<?php echo date('Y-m-d\TH:i', strtotime('+1 day')); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="archivo" class="form-label">Archivo Adjunto (Opcional)</label>
                    <input type="file" class="form-control" id="archivo" name="archivo">
                    <div class="form-text">Formatos permitidos: PDF, DOC, DOCX, ZIP, RAR, JPG, PNG. Tamaño máximo: 5MB</div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="dashboard.php" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Crear Tarea</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación del formulario
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                const fechaEntrega = document.getElementById('fecha_entrega').value;
                const fechaActual = new Date();
                const fechaSeleccionada = new Date(fechaEntrega);
                
                if (fechaSeleccionada < fechaActual) {
                    e.preventDefault();
                    alert('La fecha de entrega no puede ser en el pasado');
                    return false;
                }
                
                // Si necesitas validar el select, usa el nuevo id
                const materiaSelect = document.getElementById('id_materia_ficha');
                if (materiaSelect && materiaSelect.value === "") {
                    e.preventDefault();
                    alert('Debe seleccionar una materia');
                    return false;
                }
                
                const fileInput = document.getElementById('archivo');
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const allowedTypes = ['application/pdf', 'application/msword', 
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/zip', 'application/x-rar-compressed', 
                                        'image/jpeg', 'image/png'];
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    
                    if (!allowedTypes.includes(file.type)) {
                        e.preventDefault();
                        alert('Tipo de archivo no permitido');
                        return false;
                    }
                    
                    if (file.size > maxSize) {
                        e.preventDefault();
                        alert('El archivo excede el tamaño máximo permitido (5MB)');
                        return false;
                    }
                }
            });
        });
    </script>
</body>
</html>