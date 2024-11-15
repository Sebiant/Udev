
<?php
    include_once '../componentes/header.php';
    ?>
<div class="container">
        <h1 class="text-center">Programas</h1>

        <div class="row">
            <div class="col-2 offset-10">
                <div class="text-center">
                    <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary w-100 " data-bs-toggle="modal" data-bs-target="#modalDocentes" id="botonCrear">
                            <i class="bi bi-plus-circle"></i> Crear
                        </button>
                </div>

            </div>
        </div>
        <br />
        <br />

        <div class="table-responsive">
            <table id="datos_programa" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Duracion</th>
                        <th>Cant modulos</th>
                        <th>Descripción</th>
                        <th>estado</th>
                        <th>Modificar</th>
                        <th>Borrar</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalPrograma" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Agregar Programa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="formPrograma">
                <div class="mb-3">
          <input type="hidden" name="accion" value="crear" id="accion">
          <input type="hidden" name="id_programa" id="id_programa">

          <div class="mb-3">
            <label for="tipo_programa" class="form-label">Tipo de Programa:</label>
            <input type="text" name="tipo_programa" id="tipo_programa" class="form-control">
          </div>
          <div class="mb-3">
            <label for="nombre_programa" class="form-label">Nombre del programa:</label>
            <input type="text" name="nombre_programa" id="nombre_programa" class="form-control">
          </div>
          <div class="mb-3">
            <label for="duracion_programa" class="form-label">Duracion:</label>
            <input type="text" name="duracion_programa" id="duracion_programa" class="form-control">
          </div>
          <div class="mb-3">
            <label for="cantidad_modulos" class="form-label">Cant de modulos:</label>
            <input type="text" name="cantidad_modulos" id="cantidad_modulos" class="form-control">
          </div>
          <div class="mb-3">
            <label for="descripcion_programa" class="form-label">Descripcion:</label>
            <input type="text" name="descripcion_programa" id="descripcion_programa" class="form-control">
          
        </form>
      </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="id_programa" id="id_programa">
            <input type="hidden" name="operacion" id="operacion">
            <button type="submit" class="btn btn-success" onclick="crearPrograma()">Guardar</button>
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
                    <h5 class="modal-title">Editar Programa</h5>
                </div>
                <form id="editForm">
                    <div class="modal-body">
                        <input type="hidden" name="id_programa">
                        <div class="form-group">
                            <label for="tipo_programa">Tipo</label>
                            <input type="text" class="form-control" name="tipo_programa">
                        </div>
                        <div class="form-group">
                            <label for="nombre_programa">Número del programa</label>
                            <input type="text" class="form-control" name="nombre_programa">
                        </div>
                        <div class="form-group">
                            <label for="duracion_programa">Duracion</label>
                            <input type="text" class="form-control" name="duracion_programa">
                        </div>
                        <div class="form-group">
                            <label for="cantidad_modulos">Cant de modulos</label>
                            <input type="text" class="form-control" name="cantidad_modulos">
                        </div>
                        <div class="form-group">
                            <label for="descripcion_programa">Descripcion</label>
                            <input type="text" class="form-control" name="descripcion_programa">
                     
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
    <script src="js/consultas_programas.js"></script>
    <script src="js/datatable_programas.js"></script>

