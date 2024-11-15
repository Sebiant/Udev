<?php
    include_once '../componentes/header.php';
    ?>
<div class="container">
        <h1 class="text-center">Docentes</h1>

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
            <table id="datos_docente" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Docente</th>
                        <th>Especialidad</th>
                        <th>Descripción</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Email</th>
                        <th>estado</th>
                        <th>Modificar</th>  
                        <th>Borrar</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalDocentes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Agregar Docentes</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="formDocente">
                <div class="mb-3">
          <input type="hidden" name="accion" value="crear" id="accion">
          <input type="hidden" name="id_docente" id="id_docente">

          <div class="mb-3">
            <label for="tipo_documento" class="form-label">Tipo de Documento:</label>
            <input type="text" name="tipo_documento" id="tipo_documento" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="numero_documento" class="form-label">Número de Documento:</label>
            <input type="text" name="numero_documento" id="numero_documento" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="nombres" class="form-label">Nombres:</label>
            <input type="text" name="nombres" id="nombres" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="apellidos" class="form-label">Apellidos:</label>
            <input type="text" name="apellidos" id="apellidos" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="especialidad" class="form-label">Especialidad:</label>
            <input type="text" name="especialidad" id="especialidad" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="descripcion_especialidad" class="form-label">Descripción Especialidad:</label>
            <input type="text" name="descripcion_especialidad" id="descripcion_especialidad" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="direccion" class="form-label">Dirección:</label>
            <input type="text" name="direccion" id="direccion" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
          </div>
          <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" name="declara_renta" id="declara_renta">
            <label class="form-check-label" for="declara_renta">Declara Renta</label>
          </div>
          <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" name="retenedor_iva" id="retenedor_iva">
            <label class="form-check-label" for="retenedor_iva">Retenedor IVA</label>
          </div>
          <div class="form-group">
                <input type="checkbox" name="estado"> Activo
            </div>
        </form>
      </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="id_docente" id="id_docente">
            <input type="hidden" name="operacion" id="operacion">
            <button type="submit" class="btn btn-success" onclick="crearDocente()">Guardar</button>
        </div>
        </div>
        </div>
    </div>
    </div>

    <!-- Modal de edición -->
<div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Docente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" name="accion" value="editar" id="accion_editar">
                    <input type="hidden" name="id_docente" id="id_docente_editar">

                    <div class="mb-3">
                        <label for="tipo_documento_editar" class="form-label">Tipo de Documento:</label>
                        <input type="text" name="tipo_documento" id="tipo_documento_editar" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="numero_documento_editar" class="form-label">Número de Documento:</label>
                        <input type="text" name="numero_documento" id="numero_documento_editar" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="nombres_editar" class="form-label">Nombres:</label>
                        <input type="text" name="nombres" id="nombres_editar" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="apellidos_editar" class="form-label">Apellidos:</label>
                        <input type="text" name="apellidos" id="apellidos_editar" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="especialidad_editar" class="form-label">Especialidad:</label>
                        <input type="text" name="especialidad" id="especialidad_editar" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="descripcion_especialidad_editar" class="form-label">Descripción Especialidad:</label>
                        <input type="text" name="descripcion_especialidad" id="descripcion_especialidad_editar" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="telefono_editar" class="form-label">Teléfono:</label>
                        <input type="text" name="telefono" id="telefono_editar" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="direccion_editar" class="form-label">Dirección:</label>
                        <input type="text" name="direccion" id="direccion_editar" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="email_editar" class="form-label">Email:</label>
                        <input type="email" name="email" id="email_editar" class="form-control">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="declara_renta" id="declara_renta_editar">
                        <label class="form-check-label" for="declara_renta_editar">Declara Renta</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="retenedor_iva" id="retenedor_iva_editar">
                        <label class="form-check-label" for="retenedor_iva_editar">Retenedor IVA</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="estado" id="estado_editar">
                        <label class="form-check-label" for="estado_editar">Estado</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" onclick="guardarCambiosDocente()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

    <?php
    include_once '../componentes/footer.php';
    ?>
    <script src="js/consultas_docente.js"></script>
    <script src="js/datatable_docentes.js"></script>