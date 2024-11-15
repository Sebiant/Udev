<?php
    include_once '../componentes/header.php';
?>
    

<div class="container">
        <h1 class="text-center">Salones</h1>

        <div class="row">
            <div class="col-2 offset-10">
                <div class="text-center">
                    <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary w-100 " data-bs-toggle="modal" data-bs-target="#modalSalones" id="botonCrear">
                            <i class="bi bi-plus-circle"></i> Crear
                        </button>
                </div>

            </div>
        </div>
        <br />
        <br />

        <div class="table-responsive">
            <table id="datos_salones" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre_salon</th>
                        <th>Capacidad</th>
                        <th>Descripcion</th>
                        <th>Id_institucion</th>
                        <th>Estado</th>
                        <th>Modificar</th>
                        <th>Borrar</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalSalones" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Agregar Salones</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <form id="formSalones">

            <div class="mb-3">
          <input type="hidden" name="accion" value="crear" id="accion">
          <input type="hidden" name="id_salon" id="id_salon">

          <div class="mb-3">
            <label for="nombre_salon" class="form-label">Nombre_salon:</label>
            <input type="text" name="nombre_salon" id="nombre_salon" class="form-control">
          </div>
          <div class="mb-3">
            <label for="capacidad" class="form-label">Capacidad:</label>
            <input type="number" name="capacidad" id="capacidad" class="form-control">
          </div>
          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripcion:</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control">
          </div>
          <div class="mb-3">
            <label for="id_instituto" class="form-label">Id_institucion:</label>
            <input type="number" name="id_institucion" id="id_institucion" class="form-control">
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
                    <h5 class="modal-title">Editar salon</h5>
                </div>
                <form id="editForm">
                    <div class="modal-body">
                        <input type="hidden" name="id_salon">
                        
                    <div class="mb-3">
                        <label for="nombre_salon" class="form-label">Nombre_salon:</label>
                        <input type="text"  class="form-control" name="nombre_salon">
                    </div>
                    <div class="mb-3">
                        <label for="capacidad" class="form-label">Capacidad:</label>
                        <input type="number"   class="form-control" name="capacidad">
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripcion:</label>
                        <input type="text"   class="form-control" name="descripcion">
                    </div>
                    <div class="mb-3">
                        <label for="id_instituto" class="form-label">Id_institucion:</label>
                        <input type="number" class="form-control" name="id_institucion">
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
    <script src="js/consultas_salones.js"></script>
    <script src="js/datatable_salones.js"></script>
