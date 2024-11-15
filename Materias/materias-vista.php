<?php
    include_once '../componentes/header.php';
    ?>
<div class="container">
        <h1 class="text-center">Materias</h1>

        <div class="row">
            <div class="col-2 offset-10">
                <div class="text-center">
                    <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary w-100 " data-bs-toggle="modal" data-bs-target="#modalMateria" id="botonCrear">
                            <i class="bi bi-plus-circle"></i> Crear
                        </button>
                </div>

            </div>
        </div>
        <br />
        <br />

        <div class="table-responsive">
            <table id="datos_materia" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th>frecuencia</th>
                        <th>Modalidad</th>
                        <th>estado</th>
                        <th>Modificar</th>
                        <th>Borrar</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalMateria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Agregar Materias</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="formMateria">
                <div class="mb-3">
          <input type="hidden" name="accion" value="crear" id="accion">
          <input type="hidden" name="id_materia" id="id_materia">

          <div class="mb-3">
            <label for="tipo_documento" class="form-label">Nombre de la materia:</label>
            <input type="text" name="tipo_documento" id="tipo_documento" class="form-control">
          </div>
          <div class="mb-3">
            <label for="numero_documento" class="form-label">Descripcion de la materia:</label>
            <input type="text" name="numero_documento" id="numero_documento" class="form-control">
          </div>
          <div class="mb-3">
            <label for="nombres" class="form-label">Frecuencia de la materia:</label>
            <input type="text" name="nombres" id="nombres" class="form-control">
          </div>
          <div class="mb-3">
            <label for="apellidos" class="form-label">Modalidad de la materia:</label>
            <input type="text" name="apellidos" id="apellidos" class="form-control">
          </div>
        </form>
      </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="id_materia" id="id_materia">
            <input type="hidden" name="operacion" id="operacion">
            <button type="submit" class="btn btn-success" onclick="crearMateria()">Guardar</button>
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
                    <h5 class="modal-title">Editar materia</h5>
                </div>
                <form id="editForm">
                    <div class="modal-body">
                        <input type="hidden" name="id_materia">
                        <div class="form-group">
                            <label for="tipo_documento">Nombre de la materia</label>
                            <input type="text" class="form-control" name="tipo_documento">
                        </div>
                        <div class="form-group">
                            <label for="numero_documento">Descripcion de la materia</label>
                            <input type="text" class="form-control" name="numero_documento">
                        </div>
                        <div class="form-group">
                            <label for="nombres">frecuencia de la materia</label>
                            <input type="text" class="form-control" name="nombres">
                        </div>
                        <div class="form-group">
                            <label for="apellidos">Modalidad de la materia</label>
                            <input type="text" class="form-control" name="apellidos">
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
    <script src="js/consultas_materias.js"></script>
    <script src="js/datatables_materias.js"></script>
