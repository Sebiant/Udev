function crearDocente() {
    // Validar el formulario antes de enviar
    const form = document.getElementById('formDocente');
    
    if (form.checkValidity()) {
        // Si el formulario es válido, proceder a enviar los datos
        $('#modalDocentes').modal('hide'); // Cierra el modal antes de enviar
        // Aquí puedes agregar tu lógica para enviar el formulario, por ejemplo, usando AJAX
        $.ajax({
            url: 'Docentes-Controlador.php',
            type: 'POST',
            data: $(form).serialize(), // Serializa el formulario
            success: function(response) {
                console.log(response);
                // Manejar la respuesta del servidor
                alert('Docente creado exitosamente.');
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

function actualizarContador() {
    const descripcion = document.getElementById('descripcion_especialidad');
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


function actualizarContadorEditar() {
    const descripcion = document.getElementById('descripcion_especialidad_editar');
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

function validarCamposFaltantes() {
    // Seleccionar los campos del formulario
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

    // Verificar qué campos están vacíos
    campos.forEach(campo => {
        const valor = $(`#editForm [name="${campo.nombre}"]`).val();
        if (!valor) {
            camposFaltantes.push(campo.etiqueta);
        }
    });

    // Mostrar la lista de campos faltantes
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

// Inicializar la validación en tiempo real
function inicializarValidacionDinamica() {
    // Escuchar eventos en los campos del formulario
    $('#editForm input, #editForm select').on('input change', validarCamposFaltantes);
}

// Llamar a esta función al abrir el modal
function abrirModalModificar() {
    validarCamposFaltantes(); // Validar al cargar el modal
    inicializarValidacionDinamica(); // Activar validación dinámica
}
