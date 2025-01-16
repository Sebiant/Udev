function crearPrograma() {
    const formData = new FormData(document.getElementById('formPrograma'));

    console.log('Acción: crear');
    console.log('Datos del Formulario:', ...formData.entries());

    $.ajax({
        url: 'Programas-Controlador.php?accion=crear',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Programa creado:', response);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

function editarPrograma() {
    const formData = new FormData(document.getElementById('formPrograma'));

    console.log('Acción: editar');
    console.log('Datos del Formulario:', ...formData.entries());

    fetch('Programas-Controlador.php?accion=editar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log('Recargando la página...');
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function activarPrograma() {
    const id_programa = document.getElementById('id_programa_eliminar').value;

    console.log('ID Programa a Activar:', id_programa);

    fetch('Programas-Controlador.php?accion=activar', {
        method: 'POST',
        body: new URLSearchParams({ id_programa })
    })
    .then(response => response.text())
    .then(data => {
        console.log('Recargando la página...');
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

    // Cambiar el estilo si el límite está cerca o alcanzado
    if (caracteresRestantes <= 20) {
        contador.classList.add('alerta');
    } else {
        contador.classList.remove('alerta');
    }
}

