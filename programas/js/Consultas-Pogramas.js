function crearPrograma() {
    const formData = new FormData(document.getElementById('formPrograma'));

    console.log('Acción: crear');
    console.log('Datos del Formulario:', ...formData.entries());
    
    $.ajax({
        url: 'Controlador-Programas.php?accion=crear',
        type: 'POST',
        data: formData,
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
    
}

function editarPrograma() {
    const formData = new FormData(document.getElementById('formPrograma'));

    console.log('Acción: editar');
    console.log('Datos del Formulario:', ...formData.entries());

    fetch('Controlador-Programas.php?accion=editar', {
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

    fetch('Controlador-Programas.php?accion=activar', {
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
