jQuery(document).ready(function($) {
    $("#formCuentaCobro").validate({
        rules: {
            fecha: {
                required: true
            },
            numero_documento: {
                required: true,
                
            },
            horas_trabajadas: {
                required: true,
              
            },
            valor_hora: {
                required: true,
                
            },
            monto: {
                required: true
            }
        },
        messages: {
            fecha: {
                required: "Por favor ingrese la fecha."
            },
            numero_documento: {
                required: "Por favor ingresa un número de documento.",
            },
            horas_trabajadas: {
                required: "Por favor ingresa las horas trabajadas."
            },
            valor_hora: {
                required: "Por favor ingresa el valor hora."
            },
            monto: {
                required: "Por favor ingresa el monto."
            },
        },
        submitHandler: function(form) {
            console.log("Formulario validado y listo para enviar.");
            form.submit();
            modificarCuenta();
        }
    });
});