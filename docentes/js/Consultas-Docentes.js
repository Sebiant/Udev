jQuery(document).ready(function($) {
    $("#formDocente").validate({
        rules: {
            tipo_documento: {
                required: true
            },
            numero_documento: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            nombres: {
                required: true
            },
            apellidos: {
                required: true
            },
            especialidad: {
                required: true
            },
            descripcion_especialidad: {
                required: true,
                maxlength: 20
            },
            telefono: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            direccion: {
                required: true
            },
            email: {
                required: true,
                pattern: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
            }
        },
        messages: {
            tipo_documento: {
                required: "Por favor selecciona un tipo de documento."
            },
            numero_documento: {
                required: "Por favor ingresa un número de documento.",
                digits: "Solo se permiten números.",
                minlength: "Debe contener 10 digitos.",
                maxlength: "Debe contener 10 dígitos."
            },
            nombres: {
                required: "Por favor ingresa tu nombre."
            },
            apellidos: {
                required: "Por favor ingresa tu apellido."
            },
            especialidad: {
                required: "Por favor ingresa tu especialidad."
            },
            descripcion_especialidad: {
                required: "Por favor ingresa la descripción de la especialidad.",
                maxlength: "No puedes exceder los 20 caracteres."
            },
            telefono: {
                required: "Por favor ingresa un número de teléfono.",
                digits: "Solo se permiten números.",
                minlength: "Debe contener 10 dígitos.",
                maxlength: "Debe contener 10 dígitos."
            },
            direccion: {
                required: "Por favor ingresa tu dirección."
            },
            email: {
                required: "Por favor ingresa un correo electrónico.",
                email: "Por favor ingresa un correo válido.",
                pattern: "El correo debe ser un dominio '@' - '.com'."
            }
        },
        submitHandler: function(form) {
            console.log("Formulario validado y listo para enviar.");
            form.submit();
            crearDocente();
        }
    });

    jQuery(document).ready(function($) {
        $("#editForm").validate({
            rules: {
                tipo_documento: {
                    required: true
                },
                numero_documento: {
                    required: true,
                    digits: true
                },
                nombres: {
                    required: true
                },
                apellidos: {
                    required: true
                },
                especialidad: {
                    required: true
                },
                descripcion_especialidad: {
                    required: true,
                    maxlength: 20
                },
                telefono: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 10
                },
                direccion: {
                    required: true
                },
                email: {
                    required: true,
                    email: true,
                    pattern: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com)$/
                }
            },
            messages: {
                tipo_documento: {
                    required: "Por favor selecciona un tipo de documento."
                },
                numero_documento: {
                    required: "Por favor ingresa un número de documento.",
                    digits: "Solo se permiten números."
                },
                nombres: {
                    required: "Por favor ingresa tu nombre."
                },
                apellidos: {
                    required: "Por favor ingresa tu apellido."
                },
                especialidad: {
                    required: "Por favor ingresa tu especialidad."
                },
                descripcion_especialidad: {
                    required: "Por favor ingresa la descripción de la especialidad.",
                    maxlength: "No puedes exceder los 20 caracteres."
                },
                telefono: {
                    required: "Por favor ingresa un número de teléfono.",
                    digits: "Solo se permiten números.",
                    minlength: "Debe contener 10 dígitos.",
                    maxlength: "Debe contener 10 dígitos."
                },
                direccion: {
                    required: "Por favor ingresa tu dirección."
                },
                email: {
                    required: "Por favor ingresa un correo electrónico.",
                    email: "Por favor ingresa un correo válido.",
                    pattern: "El correo debe ser un dominio '.com'."
                }
            },
            submitHandler: function(form) {
                console.log("Formulario validado y listo para enviar.");
                form.submit();
                guardarCambiosDocente();
            }
        });

        // Contador de caracteres
        $('#descripcion_especialidad').on('input', function() {
            const maxLength = $(this).attr('maxlength');
            const restantes = maxLength - $(this).val().length;
            $('#contadorCrear').text(`${restantes} caracteres disponibles`);

            if (restantes <= 20) {
                $('#contadorCrear').addClass('alerta');
            } else {
                $('#contadorCrear').removeClass('alerta');
            }
        });
    });

        // Contador de caracteres
        $('#descripcion_especialidad_edit').on('input', function() {
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