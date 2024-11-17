
<?php
    
    include '../conexion.php';

    $sql = "SELECT id_docente, nombres, apellidos FROM docentes";
$result = $conn->query($sql);
    
    include_once '../componentes/header.php';
    ?>
<div class="container">
        <h1 class="text-center">Cuentas de cobro admin</h1>

        <div class="row">
            <div class="col-2 offset-10">
                <div class="text-center">
                    <div class="d-flex justify-content-start">
    <button type="button" class="btn btn-primary mx-1" id="botonPdf">
        <i class="bi bi-plus-circle"></i> PDF
    </button>
    <button type="button" class="btn btn-primary mx-1" id="botonCsv">
        <i class="bi bi-plus-circle"></i> CSV
    </button>
    
</div>
    </div>
    </div>
    </div>
    <br/>
    <br/>
    
    <div class="card">
  <h5 class="card-header">Filtrar cuenta de cobro</h5>
  <div class="card-body">
    <h5 class="card-title">Seleccione los campos necesarios</h5>
    <p class="card-text"></p>
    <a href="#" class="btn btn-primary">?</a>
    
    <select id="docente" name="id_docente" required>
         <option value="">Seleccione un docente</option>
         <?php
         if ($result->num_rows > 0) {
         while ($row = $result->fetch_assoc()) {
         echo '<option value="' . $row['id_docente'] . '">' . $row['nombres'] . " " . $row['apellidos'] . '</option>';
         }
         } else {
          echo '<option value="">No hay docentes disponibles</option>';
          }
            ?>
            </select>

  </div>
  <br/>
<br/>
</div>


        <div class="table-responsive">
            <table id="datos_cuentacobro_admin" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Pago</th>
                        <th>Valor de la hora</th>
                        <th>Monto</th>
                        <th>Verificar</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalCobroAdmin" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="formCobroAdmin">
                
        </div>
        </div>
    </div>
    </div>
    
                </form>
            </div>
        </div>
    </div>

    <?php
    include_once '../componentes/footer.php';
    ?>
    <script src="js/consultas_docente.js"></script>
    <script src="js/datatable_docentes.js"></script>