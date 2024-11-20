function crearSalon() {
    const formData = new FormData(document.getElementById('formSalones'));

    console.log('Acción: Crear');
    console.log('Datos del Formulario:', ...formData.entries());

    fetch('salones-controlador.php?accion=crear', {
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

function editarSalon() {
    const formData = new FormData(document.getElementById('formSalones'));

    console.log('Acción: Editar');
    console.log('Datos del Formulario:', ...formData.entries());

    fetch('salones-controlador.php?accion=editar', {
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

function activarSalon() {
    const id_salon = document.getElementById('id_salon_eliminar').value;

    console.log('ID Salon a Activar:', id_salon);

    fetch('salones-controlador.php?accion=activar', {
        method: 'POST',
        body: new URLSearchParams({ id_salon })
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
