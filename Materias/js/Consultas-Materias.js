function crearMateria() {
    const form = document.getElementById('formMateria');
    
    if (form.checkValidity()) {
        $('#modalMaterias').modal('hide'); 
        $.ajax({
            url: 'Materias-Controlador.php?accion=crear',
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

function editarMateria() {
    const formData = new FormData(document.getElementById('formMateria'));

    console.log('Acción: Editar');
    console.log('Datos del Formulario:', ...formData.entries());

    fetch('Materias-Controlador.php?accion=editar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function activarMateria() {
    const id_materia = document.getElementById('id_materia_eliminar').value;

    console.log('ID Materia a Activar:', id_materia);

    fetch('Materias-Controlador.php?accion=editar', {
        method: 'POST',
        body: new URLSearchParams({ id_materia })
    })
    .then(response => response.text())
    .then(data => {
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function actualizarContador() {
    const descripcion = document.getElementById('descripcion');
    const contador = document.getElementById('contador');
    const maxLength = descripcion.getAttribute('maxlength');
    const caracteresRestantes = maxLength - descripcion.value.length;

    contador.textContent = `${caracteresRestantes} caracteres disponibles`;

    if (caracteresRestantes <= 20) {
        contador.classList.add('alerta');
    } else {
        contador.classList.remove('alerta');
    }
}
