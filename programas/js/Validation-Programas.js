jQuery(document).ready(function($) {
    $("#formPrograma").validate({
        rules: {
            tipo: {
                required: true,
                minlength: 3
            },
            nombre: {
                required: true,
                minlength: 3
            },
            duracion_mes: {
                required: true,
                number: true,
                min: 1
            },
            valor_total_programa: {
                required: true,
                digits: true,
            },
            descripcion: {
                required: true,
                maxlength: 30
            }
        },
        messages: {
            tipo: {
                required: "Por favor, ingresa el tipo de programa.",
                minlength: "Debe tener al menos 3 caracteres."
            },
            nombre: {
                required: "Por favor, ingresa el nombre.",
                minlength: "Debe tener al menos 3 caracteres."
            },
            duracion_mes: {
                required: "Por favor, ingresa la duración.",
                number: "Debe ser un número válido.",
                min: "Debe ser mayor a 0."
            },
            valor_total_programa: {
                required: "Por favor, ingresa el valor del programa.",
                digits: "Solo se permiten números."
            },
            descripcion: {
                required: "Por favor, ingresa una descripción.",
                maxlength: "No puede exceder 30 caracteres."
            }
        },
        submitHandler: function(form) {
            console.log("Formulario validado y listo para enviar.");
            form.submit();
            crearPrograma(); 
        }
    });

    $("#editForm").validate({
        rules: {
            tipo: {
                required: true,
                minlength: 3
            },
            nombre: {
                required: true,
                minlength: 3
            },
            duracion_mes: {
                required: true,
                number: true,
                min: 1
            },
            valor_total_programa: {
                required: true,
                digits: true,
            },
            descripcion: {
                required: true,
                maxlength: 30
            }
        },
        messages: {
            tipo: {
                required: "Por favor, ingresa el tipo de programa.",
                minlength: "Debe tener al menos 3 caracteres."
            },
            nombre: {
                required: "Por favor, ingresa el nombre.",
                minlength: "Debe tener al menos 3 caracteres."
            },
            duracion_mes: {
                required: "Por favor, ingresa la duración.",
                number: "Debe ser un número válido.",
                min: "Debe ser mayor a 0."
            },
            valor_total_programa: {
                required: "Por favor, ingresa el valor del programa.",
                digits: "Solo se permiten números."
            },
            descripcion: {
                required: "Por favor, ingresa una descripción.",
                maxlength: "No puede exceder 30 caracteres."
            }
        },
        submitHandler: function(form) {
            console.log("Formulario validado y listo para enviar.");
            form.submit();
            editarPrograma()
        }
    });

    // Contadores de caracteres
    $('#descripcion').on('input', function() {
        const maxLength = $(this).attr('maxlength');
        const restantes = maxLength - $(this).val().length;
        $('#contadorCrear').text(`${restantes} caracteres disponibles`);

        if (restantes <= 20) {
            $('#contadorCrear').addClass('alerta');
        } else {
            $('#contadorCrear').removeClass('alerta');
        }
    });

    $('#descripcion_edit').on('input', function() {
        const maxLength = $(this).attr('maxlength');
        const restantes = maxLength - $(this).val().length;
        $('#contadorEditar').text(`${restantes} caracteres disponibles`);

        if (restantes <= 20) {
            $('#contadorEditar').addClass('alerta');
        } else {
            $('#contadorEditar').removeClass('alerta');
        }
    });
});
