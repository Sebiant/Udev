jQuery(document).ready(function($) {
    $("#formSalones").validate({
        rules: {
            nombre_salon: {
                required: true,
                pattern: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/ // Solo letras y espacios
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
                pattern: "El nombre solo puede contener letras y espacios."
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
                pattern: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/ // Solo letras y espacios
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
                pattern: "El nombre solo puede contener letras y espacios."
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
});



