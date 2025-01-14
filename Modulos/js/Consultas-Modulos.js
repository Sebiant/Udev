function crearModulo() {
    // Validar el formulario antes de enviar
    const form = document.getElementById('formModulo');
    
    if (form.checkValidity()) {
        // Si el formulario es válido, proceder a enviar los datos
        $('#modalModulos').modal('hide'); // Cierra el modal antes de enviar
        // Aquí puedes agregar tu lógica para enviar el formulario, por ejemplo, usando AJAX
        $.ajax({
            url: 'Modulos-Controlador.php',
            type: 'POST',
            data: $(form).serialize(), // Serializa el formulario
            success: function(response) {
                // Manejar la respuesta del servidor
                alert('Módulo creado exitosamente.');
                console.log('Recargando la página...');
                location.reload();
            },
            error: function() {
                alert('Hubo un error al crear el módulo.');
            }
        });
    } else {
        // Si el formulario no es válido, mostrar los mensajes de error
        form.classList.add('was-validated');
    }
}
