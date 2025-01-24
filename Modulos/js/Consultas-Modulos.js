function crearModulo() {
    const form = document.getElementById('formModulo');
    
    if (form.checkValidity()) {
        $('#modalModulos').modal('hide');
        $.ajax({
            url: 'Modulos-Controlador.php',
            type: 'POST',
            data: $(form).serialize(), 
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Hubo un error al crear el módulo.');
            }
        });
    } else {
        form.classList.add('was-validated');
    }
}
