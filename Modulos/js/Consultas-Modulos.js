jQuery(document).ready(function ($) {
    // Validación del formulario de creación
    $("#formMateria").validate({
        rules: {
            tipo: {
                required: true
            },
            nombre: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
            id_programa: {
                required: true
            },
            descripcion: {
                required: true,
                maxlength: 30
            }
        },
        messages: {
            tipo: {
                required: "Por favor, ingrea el tipo de la materia."
            },
            nombre: {
                required: "Por favor, ingresa el nombre de la materia.",
                minlength: "El nombre debe tener al menos 3 caracteres.",
                maxlength: "El nombre no puede superar los 100 caracteres."
            },
            id_programa: {
                required: "Por favor, ingresa el programa."
            },
            descripcion: {
                required: "Por favor, ingresa una descripción.",
                maxlength: "La descripción no puede superar los 30 caracteres."
            }
        },
        submitHandler: function (form) {
            console.log("Formulario validado y listo para enviar.");
            form.submit();
            crearMateria();
        }
    });

    // Validación del formulario de edición
    $("#editForm").validate({
        rules: {
            tipo: {
                required: true
            },
            nombre: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
            id_programa: {
                required: true
            },
            descripcion: {
                required: true,
                maxlength: 30
            }
        },
        messages: {
            tipo: {
                required: "Por favor, ingrea el tipo de la materia."
            },
            nombre: {
                required: "Por favor, ingresa el nombre de la materia.",
                minlength: "El nombre debe tener al menos 3 caracteres.",
                maxlength: "El nombre no puede superar los 100 caracteres."
            },
            id_programa: {
                required: "Por favor, ingresa el programa."
            },
            descripcion: {
                required: "Por favor, ingresa una descripción.",
                maxlength: "La descripción no puede superar los 30 caracteres."
            }
        },
        submitHandler: function (form) {
            console.log("Formulario validado y listo para enviar.");
            form.submit();
            GuardarMateria();
        }
    });

    // Contador de caracteres en el formulario de creación
    $('#descripcion').on('input', function () {
        const maxLength = $(this).attr('maxlength');
        const restantes = maxLength - $(this).val().length;
        $('#contadorCrear').text(`${restantes} caracteres disponibles`);

        if (restantes <= 10) {
            $('#contadorCrear').addClass('alerta');
        } else {
            $('#contadorCrear').removeClass('alerta');
        }
    });

    // Contador de caracteres en el formulario de edición
    $('#descripcion_edit').on('input', function () {
        const maxLength = $(this).attr('maxlength');
        const restantes = maxLength - $(this).val().length;
        $('#contadorEditar').text(`${restantes} caracteres disponibles`);

        if (restantes <= 10) {
            $('#contadorEditar').addClass('alerta');
        } else {
            $('#contadorEditar').removeClass('alerta');
        }
    });
});
