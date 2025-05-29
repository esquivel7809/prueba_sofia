// Funcionalidad general del sitio
document.addEventListener('DOMContentLoaded', function() {
    // Validación de formularios
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const fileInputs = form.querySelector('input[type="file"]');
            
            if (fileInputs) {
                fileInputs.forEach(input => {
                    if (input.files.length > 0) {
                        const file = input.files[0];
                        const fileSize = file.size;
                        const fileType = file.name.split('.').pop().toLowerCase();
                        const allowedTypes = ['pdf', 'doc', 'docx', 'zip', 'rar', 'jpg', 'png'];
                        
                        if (fileSize > 5 * 1024 * 1024) { // 5MB
                            alert('El archivo excede el tamaño máximo permitido (5MB)');
                            e.preventDefault();
                            return;
                        }
                        
                        if (!allowedTypes.includes(fileType)) {
                            alert('Tipo de archivo no permitido');
                            e.preventDefault();
                            return;
                        }
                    }
                });
            }
        });
    });
    
    // Manejo de fechas
    const dateInputs = document.querySelectorAll('input[type="datetime-local"]');
    
    dateInputs.forEach(input => {
        const now = new Date();
        const minDate = new Date(now.getTime() + 24 * 60 * 60 * 1000); // Mínimo 1 día en el futuro
        
        input.min = minDate.toISOString().slice(0, 16);
    });
});