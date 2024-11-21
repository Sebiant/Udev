<?php
    include_once '../componentes/header.php';
    include_once '../conexion.php';
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
                        <th>Id Salon</th>
                        <th>Nombre_salon</th>
                        <th>Capacidad</th>
                        <th>Descripcion</th>
                        <th>Instituciones</th>
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
            <label for="nombre_salon" class="form-label">Nombre salon:</label>
            <input type="text" name="nombre_salon" id="nombre_salon" class="form-control" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Escribe el nombre del salón.">
          </div>
          <div class="mb-3">
            <label for="capacidad" class="form-label">Capacidad:</label>
            <input type="number" name="capacidad" id="capacidad" class="form-control" min="0" step="1" required>
          </div>
          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripcion:</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control" required>
          </div>
          <div class="form-group">
                        <label for="id_institucion">Institución:</label>
                        <select class="form-control" name="id_institucion" required>
                            <option value="">-- Selecciona una institución --</option>
                            <?php
                            // Cargar las instituciones en las opciones del select
                           
                            $sql = "SELECT id_institucion, nombre FROM instituciones";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='".$row['id_institucion']."'>".$row['nombre']."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
          <div class="form-group">
                        <label for="estado">Estado:</label>
                        <input type="checkbox" name="estado" required> Activo
                    </div>
          <div class="modal-footer">
              <button type="submit" class="btn btn-success">Guardar</button>
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
                        <input type="text"  class="form-control" name="nombre_salon" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacidad" class="form-label">Capacidad:</label>
                        <input type="number"   class="form-control" name="capacidad" min="0" step="1" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Escribe el nombre del salón.">
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripcion:</label>
                        <input type="text"   class="form-control" name="descripcion" required>
                    </div>
                    <div class="form-group">
                        <label for="id_institucion">Institución:</label>
                        <select class="form-control" name="id_institucion" required>
                            <option value="">-- Selecciona una institución --</option>
                            <?php
                            // Cargar las instituciones en las opciones del select
                           
                            $sql = "SELECT id_institucion, nombre FROM instituciones";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='".$row['id_institucion']."'>".$row['nombre']."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <input type="checkbox" name="estado" required> Activo
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
    <script src="js/Consultas-Salones.js"></script>
    <script src="js/Datatables-Salones.js"></script>