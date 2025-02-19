$(document).ready(function() {
    $("#multiStepForm").validate({
        rules: {
            periodo: {
                required: true
            },
            numero_documento: {
                required: true
            },
            id_salon: {
                required: true
            },
            hora_inicio: {
                required: true
            },
            hora_salida: {
                required: true
            },
            fecha: {
                required: true
            }
        },
        messages: {
            periodo: {
                required: "Por favor, selecciona un periodo."
            },
            numero_documento: {
                required: "Por favor, selecciona un docente."
            },
            id_salon: {
                required: "Por favor, selecciona un salón."
            },
            hora_inicio: {
                required: "Por favor, selecciona la hora de inicio."
            },
            hora_salida: {
                required: "Por favor, selecciona la hora de salida."
            },
            fecha: {
                required: "Por favor, selecciona la fecha de la clase."
            }
        },
        errorElement: "div",
        errorPlacement: function(error, element) {
            error.addClass("invalid-feedback");
            element.closest(".form-group").append(error);
        }
    });
    $(".next").click(function() {
        if ($("#multiStepForm").valid()) {
            $(".step").addClass("hidden");
            $(".step").eq($(".step").index(".step") + 1).removeClass("hidden");
        }
    });

    $(".prev").click(function() {
        $(".step").addClass("hidden");
        $(".step").eq($(".step").index(".step") - 1).removeClass("hidden");
    });
});

