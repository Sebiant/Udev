jQuery(document).ready(function($) {
    $("#formModulo").validate({
        rules: {
            fecha_inicio: {
                required: true,
                date: true,
                min: "2025-01-01", // Fecha mínima permitida
                max: "2026-12-31"  // Fecha máxima permitida
            },
            fecha_fin: {
                required: true,
                date: true,
                min: "2025-01-01",
                max: "2026-12-31"  // Fecha máxima permitida
            },
            id_programa: {
                required: true
            }
        },
        messages: {
            fecha_inicio: {
                required: "Por favor, selecciona una fecha de inicio.",
                date: "Ingresa una fecha válida.",
                min: "La fecha de inicio no puede ser anterior al 01 de enero de 2025.",
                max: "La fecha de inicio no puede ser posterior al 31 de diciembre de 2026."
            },
            fecha_fin: {
                required: "Por favor, selecciona una fecha de fin.",
                date: "Ingresa una fecha válida.",
                min: "La fecha de fin no puede ser anterior al 01 de enero de 2025.",
                max: "La fecha de fin no puede ser posterior al 31 de diciembre de 2026."
            },
            id_programa: {
                required: "Por favor, selecciona un programa."
            }
        },
        submitHandler: function(form) {
            console.log("Formulario validado y listo para enviar.");
            form.submit();
            crearModulo();
        }
    });
});

jQuery(document).ready(function($) {
    $("#editForm").validate({
        rules: {
            fecha_inicio: {
                required: true,
                date: true,
                min: "2025-01-01", // Fecha mínima permitida
                max: "2026-12-31"  // Fecha máxima permitida
            },
            fecha_fin: {
                required: true,
                date: true,
                min: "2025-01-01",
                max: "2026-12-31"  // Fecha máxima permitida
            },
            id_programa: {
                required: true
            }
        },
        messages: {
            fecha_inicio: {
                required: "Por favor, selecciona una fecha de inicio.",
                date: "Ingresa una fecha válida.",
                min: "La fecha de inicio no puede ser anterior al 01 de enero de 2025.",
                max: "La fecha de inicio no puede ser posterior al 31 de diciembre de 2026."
            },
            fecha_fin: {
                required: "Por favor, selecciona una fecha de fin.",
                date: "Ingresa una fecha válida.",
                min: "La fecha de fin no puede ser anterior al 01 de enero de 2025.",
                max: "La fecha de fin no puede ser posterior al 31 de diciembre de 2026."
            },
            id_programa: {
                required: "Por favor, selecciona un programa."
            }
        },
        submitHandler: function(form) {
            console.log("Formulario validado y listo para enviar.");
            form.submit();
            GuardarModulo();
        }
    });
});

