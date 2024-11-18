function crearCuentaCobroDocente() {
    const formData = new FormData(document.getElementById('formCuentaDeCobro'));

    console.log('Acción: Crear');
    console.log('Datos del Formulario:', ...formData.entries());

    fetch('cuenta-de-cobro-docente_controlador.php?accion=crear', {
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

function editarCuentaCobroDocente() {
    const formData = new FormData(document.getElementById('formCuentaCorbo'));

    console.log('Acción: Editar');
    console.log('Datos del Formulario:', ...formData.entries());

    fetch('cuenta-de-cobro-docente_controlador.php?accion=editar', {
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


