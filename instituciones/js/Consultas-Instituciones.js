jQuery(document).ready(function($) {
    $("#formInstituciones").validate({
        rules: {
            nombre: {
                required: true,
                minlength: 3
            },
            direccion: {
                required: true
            },
        },
        messages: {
            nombre: {
                required: "Por favor ingresa tu nombre.",
                minlength: "Debe contener 3 digitos."
            },
            direccion: {
                required: "Por favor ingresa tu dirección."
            },
        },
        submitHandler: function(form) {
            console.log("Formulario validado y listo para enviar.");
            form.submit();
            crearInstitucion();
        }
    });

    jQuery(document).ready(function($) {
        $("#editForm").validate({
            rules: {
                nombre: {
                    required: true,
                    minlength: 3
                },
                direccion: {
                    required: true
                },
            },
            messages: {
                nombre: {
                    required: "Por favor ingresa tu nombre.",
                    minlength: "Debe contener 3 digitos."
                },
                direccion: {
                    required: "Por favor ingresa tu dirección."
                },
            },
            submitHandler: function(form) {
                console.log("Formulario validado y listo para enviar.");
                form.submit();
                GuardarInstitucion();
            }
        });
    });
});
