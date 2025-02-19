<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UDEV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>

<body>
    <?php include_once '../Componentes/header.php'; ?>

    <h1 class="text-center">Gestión Estudiantes</h1>
    <div class="container">
        <div class="row">
            <div class="col-2 offset-10 text-center">
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalUsuario" id="botonCrear">
                    <i class="bi bi-plus-circle-fill"></i> Crear
                </button>
            </div>
        </div>
        <br>
        <div class="card">
            <div class="card-header">
                <h5>Estudiantes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="Estudiantes" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Fecha nacimiento</th>
                                <th>Imagen</th>
                                <th>Estado</th>
                                <th>Editar</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include("../Componentes/footer.php"); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function () {
            $("#botonCrear").click(function () {
                $("#formulario")[0].reset();
                $(".modal-title").text("Crear estudiante");
                $("#action").val("crear").removeClass('btn-success').addClass('btn-primary');
                $("#operacion").val("crear");
                $("#imagen_subida").html("");
            });

            var dataTable = $('#Estudiantes').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [],
                "ajax": {
                    url: "Estudiantes-Controller.php",
                    type: "POST"
                },
                "columnDefs": [{
                    "targets": [0, 3, 4],
                    "orderable": false,
                }]
            });

            $(document).on('submit', '#formulario', function (event) {
                event.preventDefault();
                var nombres = $("#nombre").val();
                var apellidos = $("#apellidos").val();
                var fecha_nacimiento_estudiante = $("#fecha_nacimiento_estudiante").val();
                if (nombres != '' && apellidos != '' && fecha_nacimiento_estudiante != '') {
                    $.ajax({
                        url: "Estudiantes-Controller.php",
                        method: 'POST',
                        data: new FormData(this),
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            alert(data);
                            $('#formulario')[0].reset();
                            $('#modalUsuario').modal('hide');
                            $('.modal-backdrop').remove();
                            dataTable.ajax.reload();
                        }
                    });
                } else {
                    alert("Algunos campos son obligatorios");
                }
            });
        });
    </script>
</body>

</html>
