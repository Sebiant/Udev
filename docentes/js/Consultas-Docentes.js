function crearDocente() {
    const formData = new FormData(document.getElementById('formDocente'));

    console.log('Acción: Crear');
    console.log('Datos del Formulario:', ...formData.entries());

    fetch('Docentes-Controlador.php?accion=crear', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        //alert(data);
        console.log('Recargando la página...');
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function activarDocente() {
    const id_docente = document.getElementById('id_docente_eliminar').value;

    console.log('ID Docente a Activar:', id_docente);

    fetch('Docentes-Controlador.php?accion=activar', {
        method: 'POST',
        body: new URLSearchParams({ id_docente })
    })
    .then(response => response.text())
    .then(data => {
        //alert(data);
        console.log('Recargando la página...');
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
