function crearPrograma() {
    const formData = new FormData(document.getElementById('formPrograma'));

    console.log('Acción: Crear');
    console.log('Datos del Formulario:', ...formData.entries());

    fetch('controlador-programas.php?accion=crear', {
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

function editarPrograma() {
    const formData = new FormData(document.getElementById('formPrograma'));

    console.log('Acción: Editar');
    console.log('Datos del Formulario:', ...formData.entries());

    fetch('controlador-programas.php?accion=editar', {
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

function activarPrograma() {
    const id_programa = document.getElementById('id_programa_eliminar').value;

    console.log('ID Programa a Activar:', id_programa);

    fetch('controlador-programas.php?accion=activar', {
        method: 'POST',
        body: new URLSearchParams({ id_programa })
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
