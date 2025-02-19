<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SERVICIOS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <!-- Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include_once '../Componentes/header.php' ?>
    
    <br>
    <div class="container">
        <h1 class="text-center">SERVICIOS</h1>
        <div class="row">
            <div class="col-2 offset-10">
                <div class="text-center">
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalServicio" id="botonCrear">
                        <i class="bi bi-plus-circle-fill"></i> Crear
                    </button>
                </div>
            </div>
        </div>
        <br>
        <div class="card">
            <div class="card-header">
                <h5>Solo Servicios</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datos_servicio" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Valor total</th>
                                <th>Estado</th>
                                <th>Editar</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="modalServicio" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Crear servicio</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="formulario" enctype="multipart/form-data">
                    <div class="modal-body">
                        <label for="descripcion_servicio">Descripción</label>
                        <input type="text" name="descripcion_servicio" id="descripcion_servicio" class="form-control">
                        <br>
                        <label for="valor_total_servicio">Valor total</label>
                        <input type="number" name="valor_total_servicio" id="valor_total_servicio" class="form-control">
                        <br>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="codigo_servicio" id="codigo_servicio">
                        <input type="hidden" name="operacion" id="operacion">
                        <input type="submit" name="action" id="action" class="btn btn-primary" value="Crear">
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include_once '../Componentes/footer.php' ?>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <!-- DataTables JavaScript -->
    <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            $("#botonCrear").click(function() {
                $("#formulario")[0].reset();
                $(".modal-title").text("Crear servicio");
                $("#action").val("Crear").removeClass('btn-success').addClass('btn-primary');
                $("#operacion").val("crear");
            });

            var dataTable = $('#datos_servicio').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [],
                "ajax": {
                    url: "Servicios-Controller.php",
                    type: "POST"
                },
                "columnDefs": [
                    { "targets": "_all", "className": "text-center" },
                    {
                        "targets": 2,
                        "render": function (data) {
                            return '$' + parseFloat(data).toLocaleString('es-ES', {minimumFractionDigits: 2});
                        }
                    }, 
                    {
                        "targets": [4],
                        "orderable": false,
                    }
                ]
            });
        });
    </script>
</body>
</html>
