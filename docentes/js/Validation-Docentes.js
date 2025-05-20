jQuery(document).ready(function($) {
    $.validator.addMethod("emailOnly", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
    }, "Por favor ingresa un correo válido.");
    $.validator.addMethod("lettersOnly", function(value, element) {
        return this.optional(element) || /^[a-zA-ZáéíóúÁÉÍÓÚÑñ\s]+$/.test(value);
    }, "Por favor, ingrese solo letras.");
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
                required: true,
                lettersOnly: true
            },
            apellidos: {
                required: true,
                lettersOnly: true
            },
            perfil_profesional: {
                required: true
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
                emailOnly: true
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
            perfil_profesional: {
                required: "Por favor ingresa tu especialidad."
            },
            telefono: {
                required: "Por favor ingresa un número de teléfono.",
                digits: "Solo se permiten números.",
                minlength: "Debe contener 10 dígitos.",
                maxlength: "Debe contener 10 dígitos.",
            },
            direccion: {
                required: "Por favor ingresa tu dirección."
            },
            email: {
                required: "Por favor ingresa un correo electrónico."
            }
        },
        submitHandler: function(form) {
            console.log("Formulario validado y listo para enviar.");
            form.submit();
            crearDocente();
        }
    });

    jQuery(document).ready(function($) {
        $.validator.addMethod("emailOnly", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
        }, "Por favor ingresa un correo válido.");
        $.validator.addMethod("lettersOnly", function(value, element) {
            return this.optional(element) || /^[a-zA-ZáéíóúÁÉÍÓÚÑñ\s]+$/.test(value);
        }, "Por favor, ingrese solo letras.");
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
                    required: true,
                    lettersOnly: true
                },
                apellidos: {
                    required: true,
                    lettersOnly: true
                },
                perfil_profesional: {
                    required: true
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
                    emailOnly: true
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
                perfil_profesional: {
                    required: "Por favor ingresa tu perfil profesional."
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
                    required: "Por favor ingresa un correo electrónico."
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