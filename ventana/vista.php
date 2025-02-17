<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario por Pasos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .hidden { display: none; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Formulario por Pasos</h2>

        <form id="multiStepForm">
            <!-- Paso 1 -->
            <div class="step">
                <h4>Paso 1: Selección de Período y Materias</h4>
                <div class="mb-3">
                    <label for="periodo" class="form-label">Selecciona un periodo:</label>
                    <select class="form-select" id="periodo">
                        <option value="">Seleccione...</option>
                        <option value="1">Periodo 1</option>
                        <option value="2">Periodo 2</option>
                    </select>
                </div>

                <div class="card">
                    <div class="card-header"> 
                        <h5>Materias</h5>
                    </div>
                    <div class="card-body"> 
                        <div class="table-responsive">
                            <table id="datos_modulo" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Nombre</th>
                                        <th>Programa</th>
                                        <th>Descripción</th>
                                        <th>Seleccionar</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-primary next">Siguiente</button>
                </div>
            </div>

            <!-- Paso 2 -->
            <div class="step hidden">
                <h4>Paso 2: Asignación de Docente y Salón</h4>

                <div class="card">
                    <div class="card-header">
                        <h5>Docentes</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="docente">Selecciona un docente</label>
                            <select id="numero_documento" name="numero_documento" class="form-control" required>
                                <option value="">-- Selecciona un docente --</option>
                                <?php
                                if ($result_docentes->num_rows > 0) {
                                    while ($row_docente = $result_docentes->fetch_assoc()) {
                                        echo '<option value="' . $row_docente['numero_documento'] . '">' . $row_docente['nombres'] . " " . $row_docente['apellidos'] . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No hay docentes disponibles</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group mt-3">
                            <label for="salon">Selecciona un salón</label>
                            <select id="id_salon" name="id_salon" class="form-control" required>
                                <option value="">-- Selecciona un salón --</option>
                                <?php
                                if ($result_salones->num_rows > 0) {
                                    while ($row_salon = $result_salones->fetch_assoc()) {
                                        echo '<option value="' . $row_salon['id_salon'] . '">' . $row_salon['nombre_salon'] . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No hay salones disponibles</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="row mt-3">
                            <!-- Columna izquierda: Hora de Inicio y Hora de Salida -->
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-12">
                                        <label for="hora_inicio">Hora de Inicio</label>
                                        <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" step="900" required>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <label for="hora_salida">Hora de Salida</label>
                                        <input type="time" id="hora_salida" name="hora_salida" class="form-control" step="900" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Columna derecha: Fecha -->
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <label for="fecha">Fecha de la clase</label>
                                <input type="date" id="fecha" name="fecha" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-secondary prev me-2">Anterior</button>
                    <button type="button" class="btn btn-success" onclick="CrearClase()">Programar Clase</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const steps = document.querySelectorAll(".step");
        const nextBtns = document.querySelectorAll(".next");
        const prevBtns = document.querySelectorAll(".prev");
        let currentStep = 0;

        function showStep(index) {
            steps.forEach((step, i) => {
                step.classList.toggle("hidden", i !== index);
            });
        }

        nextBtns.forEach(btn => {
            btn.addEventListener("click", () => {
                if (currentStep < steps.length - 1) {
                    currentStep++;
                    showStep(currentStep);
                }
            });
        });

        prevBtns.forEach(btn => {
            btn.addEventListener("click", () => {
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
        });

        function CrearClase() {
            alert("Clase programada con éxito!");
        }

        showStep(currentStep);
    </script>
</body>
</html>
