function crearDocente() {
    const form = document.getElementById('formDocente');
    
    if (form.checkValidity()) {
        $('#modalDocentes').modal('hide'); 
        $.ajax({
            url: 'Docentes-Controlador.php',
            type: 'POST',
            data: $(form).serialize(),
            success: function(response) {
                console.log(response);
                console.log('Recargando la página...');
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

function activarDocente() {
    const id_docente = document.getElementById('id_docente_eliminar').value;

    console.log('ID Docente a Activar:', id_docente);

    fetch('Docentes-Controlador.php?accion=activar', {
        method: 'POST',
        body: new URLSearchParams({ id_docente })
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
    const descripcion = document.getElementById('descripcion_especialidad');
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


function actualizarContadorEditar() {
    const descripcion = document.getElementById('descripcion_especialidad_editar');
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

function validarCamposFaltantes() {
    const campos = [
        { nombre: "tipo_documento", etiqueta: "Tipo de documento" },
        { nombre: "numero_documento", etiqueta: "Número de documento" },
        { nombre: "nombres", etiqueta: "Nombres" },
        { nombre: "apellidos", etiqueta: "Apellidos" },
        { nombre: "especialidad", etiqueta: "Especialidad" },
        { nombre: "descripcion_especialidad", etiqueta: "Descripcion Especialidad"},
        { nombre: "telefono", etiqueta: "Teléfono" },
        { nombre: "direccion", etiqueta: "Dirección" },
        { nombre: "email", etiqueta: "Correo electrónico" }
    ];

    let camposFaltantes = [];

    campos.forEach(campo => {
        const valor = $(`#editForm [name="${campo.nombre}"]`).val();
        if (!valor) {
            camposFaltantes.push(campo.etiqueta);
        }
    });

    const listaFaltantes = document.getElementById("camposFaltantes");
    if (camposFaltantes.length > 0) {
        listaFaltantes.innerHTML = `
            <strong>Por favor completa los siguientes campos:</strong>
            <ul>${camposFaltantes.map(campo => `<li>${campo}</li>`).join("")}</ul>
        `;
        listaFaltantes.style.display = "block";
    } else {
        listaFaltantes.style.display = "none";
    }
}

function inicializarValidacionDinamica() {
    $('#editForm input, #editForm select').on('input change', validarCamposFaltantes);
}

function abrirModalModificar() {
    validarCamposFaltantes(); 
    inicializarValidacionDinamica(); 
}
