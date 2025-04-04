jQuery(document).ready(function($) {
    $("#formSalones").validate({
        rules: {
            nombre_salon: {
                required: true
            },
            capacidad: {
                required: true,
                number: true,
                min: 0
            },
            descripcion: {
                required: true,
                maxlength: 30 // Longitud máxima opcional
            },
            id_institucion: {
                required: true
            }
        },
        messages: {
            nombre_salon: {
                required: "Por favor, ingresa el nombre del salón."
            },
            capacidad: {
                required: "Por favor, ingresa la capacidad del salón.",
                number: "La capacidad debe ser un número válido.",
                min: "La capacidad debe ser mayor o igual a 0."
            },
            descripcion: {
                required: "Por favor, ingresa una descripción.",
                maxlength: "La descripción no puede superar los 30 caracteres."
            },
            id_institucion: {
                required: "Por favor, selecciona una institución."
            }
        },
        submitHandler: function(form) {
            console.log("Formulario validado y listo para enviar.");
            form.submit();
            CrearSalon();
        }
    });
});

jQuery(document).ready(function($) {
    $("#editForm").validate({
        rules: {
            nombre_salon: {
                required: true,
            },
            capacidad: {
                required: true,
                number: true,
                min: 0
            },
            descripcion: {
                required: true,
                maxlength: 30 // Longitud máxima opcional
            },
            id_institucion: {
                required: true
            }
        },
        messages: {
            nombre_salon: {
                required: "Por favor, ingresa el nombre del salón.",
            },
            capacidad: {
                required: "Por favor, ingresa la capacidad del salón.",
                number: "La capacidad debe ser un número válido.",
                min: "La capacidad debe ser mayor o igual a 0."
            },
            descripcion: {
                required: "Por favor, ingresa una descripción.",
                maxlength: "La descripción no puede superar los 30 caracteres."
            },
            id_institucion: {
                required: "Por favor, selecciona una institución."
            }
        },
        submitHandler: function(form) {
            console.log("Formulario validado y listo para enviar.");
            form.submit();
            GuardarSalon();
        }
    });

     // Contador de caracteres en el formulario de creación
     $('#descripcion').on('input', function () {
        const maxLength = $(this).attr('maxlength');
        const restantes = maxLength - $(this).val().length;
        $('#contadorCrear').text(`${restantes} caracteres disponibles`);

        if (restantes <= 20) {
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

        if (restantes <= 20) {
            $('#contadorEditar').addClass('alerta');
        } else {
            $('#contadorEditar').removeClass('alerta');
        }
    });
});



