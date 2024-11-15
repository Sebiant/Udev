<?php
    include_once '../componentes/header.php';
?>

<div class="container">
        <h1 class="text-center">Instituciones</h1>

        <div class="row">
            <div class="col-2 offset-10">
                <div class="text-center">
                    <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary w-100 " data-bs-toggle="modal" data-bs-target="#modalInstituciones" id="botonCrear">
                            <i class="bi bi-plus-circle"></i> Crear
                        </button>
                </div>

            </div>
        </div>
        <br />
        <br />

        <div class="table-responsive">
            <table id="datos_instituciones" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombres</th>
                        <th>Dirección</th>
                        <th>Estado</th>
                        <th>Modificar</th>
                        <th>Borrar</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalInstituciones" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Agregar Instituciones</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <form id="formInstituciones">

            <div class="mb-3">
          <input type="hidden" name="accion" value="crear" id="accion">
          <input type="hidden" name="id_institucion" id="id_institucion">

          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" name="nombre" id="nombre" class="form-control">
          </div>
          <div class="mb-3">
            <label for="direccion" class="form-label">Dirección:</label>
            <input type="text" name="direccion" id="direccion" class="form-control">
          </div>
          <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" name="estado" id="estado">
            <label class="form-check-label" for="estado">Estado</label>
          </div>
          <div class="modal-footer">
              <button class="btn btn-success" onclick="crearInstitucion()">Guardar</button>
          </div>
        </form>
      </div>
        </div>
        </div>
        </div>
        </div>
    </div>
    </div>

    <!-- Modal de edición -->
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar institucion</h5>
                </div>
                <form id="editForm">
                    <div class="modal-body">
                        <input type="hidden" name="id_institucion">
                        
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" name="nombre">
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" class="form-control" name="direccion">
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="estado" id="estado">
                            <label class="form-check-label" for="estado">Estado</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php
    include_once '../componentes/footer.php';
    ?>
    <script src="js/consultas_institucion.js"></script>
    <script src="js/datatable_instituciones.js"></script>



