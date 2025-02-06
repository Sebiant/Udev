$('#btnAceptar').on('click', function(e){
    e.preventDefault();
    
    $.ajax ({
        url: 'CuentaCobroDocente_controlador.php?accion=Aceptar',
        type: 'POST',
        data: {
            
            },
    })
})

