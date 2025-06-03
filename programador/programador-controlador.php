<?php
include '../Conexion.php';

$accion = $_GET['accion'] ?? 'default';

switch ($accion) {
    case 'crear':
        if (
            empty($_POST['dia']) ||
            empty($_POST['horaEntrada']) ||
            empty($_POST['horaSalida']) ||
            empty($_POST['salon']) ||
            empty($_POST['docente']) ||
            empty($_POST['periodo']) ||
            empty($_POST['modulo']) ||
            empty($_POST['modalidad'])
        ) {
            die(json_encode([
                "status" => "error",
                "message" => "Faltan datos obligatorios en el formulario."
            ]));                
        } else {
            $dia = $_POST['dia'];
            $hora_inicio = $_POST['horaEntrada'];
            $hora_salida = $_POST['horaSalida'];
            $salon = $_POST['salon'];
            $docente = $_POST['docente'];
            $periodo = $_POST['periodo'];
            $modulo = $_POST['modulo'];
            $modalidad = $_POST['modalidad'];
            $estado = 'Pendiente';
        }        
    
        // Validación 1: Hora de salida debe ser posterior a la de entrada
        if (!validarHorasEntradaSalida($hora_inicio, $hora_salida)) {
            die(json_encode(['status' => 'error', 'message' => 'La hora de salida debe ser al menos una hora después de la hora de entrada.']));
        }
    
        // Validación 2: Horario laboral válido (7:00 - 22:00)
        if (!horarioLaboralValido($hora_inicio, $hora_salida)) {
            die(json_encode(['status' => 'error', 'message' => 'El horario debe estar entre 7:00 AM y 10:00 PM.']));
        }
    
        // Validación 3: el docente debe estar capacitado para dictar clases
        if (!docentePuedeDictarModulo($conn, $docente, $modulo)) {
            die(json_encode(['status' => 'error', 'message' => 'El docente no está habilitado para dictar este módulo.']));
        }        

        $sql_periodo = "SELECT fecha_inicio, fecha_fin FROM periodos WHERE id_periodo = ?";
        $stmt_periodo = $conn->prepare($sql_periodo);
        $stmt_periodo->bind_param('i', $periodo);
        $stmt_periodo->execute();
        $result = $stmt_periodo->get_result();
        
        if (!$row = $result->fetch_assoc()) {
            die(json_encode(['status' => 'error', 'message' => 'Periodo no encontrado.']));
        }
    
        $fecha_inicio = new DateTime($row['fecha_inicio']);
        $fecha_fin = new DateTime($row['fecha_fin']);
    
        $dias = [
            "domingo" => 0, "lunes" => 1, "martes" => 2, "miercoles" => 3,
            "jueves" => 4, "viernes" => 5, "sabado" => 6
        ];
    
        if (!isset($dias[$dia])) {
            die(json_encode(['status' => 'error', 'message' => 'Día de la semana inválido.']));
        }
    
        $dia_numero = $dias[$dia];
        while ($fecha_inicio->format("w") != $dia_numero) {
            $fecha_inicio->modify("+1 day");
        }
    
        $fechas_generadas = [];
    
        $contador = 0; 
        while ($fecha_inicio <= $fecha_fin) {
            $fecha_str = $fecha_inicio->format("Y-m-d");

                $contador++; 
    
            // Validación 4: Docente disponible
            if (!docenteDisponible($docente, $fecha_str, $hora_inicio, $hora_salida, $conn)) {
                die(json_encode(['status' => 'error', 'message' => "El docente no está disponible el {$fecha_str} de {$hora_inicio} a {$hora_salida}"]));
            }
    
            // Validación 5: Salón disponible
            if (!salonDisponible($salon, $fecha_str, $hora_inicio, $hora_salida, $conn)) {
                die(json_encode(['status' => 'error', 'message' => "El salón no está disponible el {$fecha_str} de {$hora_inicio} a {$hora_salida}"]));
            }
    
            $fechas_generadas[] = $fecha_str;
            $fecha_inicio->modify("+7 days");
        }
    
        if (empty($fechas_generadas)) {
            die(json_encode(['status' => 'error', 'message' => 'No hay fechas válidas para programar (todas son festivos o no hay disponibilidad)']));
        }
    
        $sql = "INSERT INTO programador (fecha, hora_inicio, hora_salida, id_salon, numero_documento, id_modulo, id_periodo, estado, modalidad) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
    
        if (!$stmt) {
            die(json_encode(['status' => 'error', 'message' => 'Error en la preparación de la consulta: ' . $conn->error]));
        }
    
        foreach ($fechas_generadas as $fecha) {
            $stmt->bind_param('sssiiiiss', $fecha, $hora_inicio, $hora_salida, $salon, $docente, $modulo, $periodo, $estado, $modalidad);
    
            if (!$stmt->execute()) {
                die(json_encode(['status' => 'error', 'message' => 'Error al insertar: ' . $stmt->error]));
            }
        }
    
        echo json_encode(['status' => 'success', 'message' => 'Se programaron ' . count($fechas_generadas) . ' clases.']);
    
        $stmt->close();
        break;

    case 'buscarMateriaPorPrograma':
        $programa = $_POST['id_programa'] ?? null;
    
        if ($programa !== null) {
            $sql_modulo = "SELECT id_modulo AS id, nombre FROM modulos WHERE id_programa = ? AND estado = 1";
            $stmt = $conn->prepare($sql_modulo);
        
            if ($stmt) {
                $stmt->bind_param("i", $programa);
                $stmt->execute();
                $result = $stmt->get_result();
        
                $modulos = [];
                while ($row = $result->fetch_assoc()) {
                    $modulos[] = [
                        'id' => $row['id'],
                        'nombre' => $row['nombre']
                    ];
                }
        
                echo json_encode([
                    'status' => 'success',
                    'modulos' => $modulos
                ]);
                
                $stmt->close();
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al preparar la consulta: ' . $conn->error
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID del programa no proporcionado.'
            ]);
        }
        break;  
    case 'buscarClasesProgramadasPorPeriodo':
        $data = json_decode(file_get_contents("php://input"), true);
        $periodo = $data['id_periodo'] ?? null;

        if ($periodo !== null) {
            $sql = "SELECT 
                        p.id_programador AS id,
                        p.fecha,
                        TIME_FORMAT(p.hora_inicio, '%H:%i:%s') as hora_inicio,
                        TIME_FORMAT(p.hora_salida, '%H:%i:%s') as hora_salida,
                        m.nombre,
                        d.nombres,
                        d.apellidos
                    FROM programador p
                    JOIN modulos m ON p.id_modulo = m.id_modulo 
                    JOIN docentes d ON p.numero_documento = d.numero_documento
                    WHERE p.id_periodo = ?";

            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("i", $periodo);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    echo json_encode([
                        'status' => 'success',
                        'eventos' => [],
                        'debug' => 'No hay clases para ese periodo o estado != 1'
                    ]);
                    exit;
                }


                $eventos = [];
                while ($row = $result->fetch_assoc()) {
                    $eventos[] = [
                        "title" => $row['nombre'] . " - " . $row['nombres'] . " " . $row['apellidos'],
                        "start" => $row['fecha'] . "T" . $row['hora_inicio'],
                        "end" => $row['fecha'] . "T" . $row['hora_salida']
                    ];
                }

                echo json_encode([
                    'status' => 'success',
                    'eventos' => $eventos
                ]);

                $stmt->close();
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al preparar la consulta: ' . $conn->error
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID del periodo no proporcionado.'
            ]);
        }
    break;

    case 'reprogramar':        
        // Recibir datos del formulario
        $fecha = $_POST['nueva_fecha'] ?? null;
        $nueva_hora_inicio = $_POST['nueva_hora_inicio'] ?? null;
        $nueva_hora_salida = $_POST['nueva_hora_salida'] ?? null;
        $id_salon = $_POST['id_salon'] ?? null;
        $numero_documento = $_POST['numero_documento'] ?? null;
        $id_modulo = $_POST['id_modulo'] ?? null;
        $id_periodo = $_POST['id_periodo'] ?? null;
        $modalidad = "virtual";
        $estado = "Pendiente";
        $clase_original_id = $_POST['id_programador'] ?? null;
        
        // Validación de datos obligatorios
        if (!$clase_original_id || !$fecha || !$nueva_hora_inicio || !$nueva_hora_salida || !$id_salon || !$numero_documento || !$id_modulo || !$id_periodo || !$modalidad) {
            echo json_encode(["error" => "Todos los campos son obligatorios."]);
            exit;
        }
        
        // Iniciar transacción para evitar inconsistencias
        $conn->begin_transaction();
        
        try {
            // Insertar nueva clase reprogramada
            $sql_insert = "INSERT INTO programador (fecha, hora_inicio, hora_salida, id_salon, numero_documento, id_modulo, id_periodo, modalidad, estado, clase_original_id) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql_insert);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $conn->error);
            }
            
            // Enlazar parámetros
            $stmt->bind_param("ssssiiisss", $fecha, $nueva_hora_inicio, $nueva_hora_salida, $id_salon, $numero_documento, $id_modulo, $id_periodo, $modalidad, $estado, $clase_original_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al reprogramar la clase: " . $stmt->error);
            }
        
            // Actualizar estado de la clase original a "Reprogramada"
            $sql_update = "UPDATE programador SET estado = 'Reprogramada' WHERE id_programador = ?";
            $stmt_update = $conn->prepare($sql_update);
            
            if (!$stmt_update) {
                throw new Exception("Error en la preparación del UPDATE: " . $conn->error);
            }
        
            $stmt_update->bind_param("i", $clase_original_id);
            
            if (!$stmt_update->execute()) {
                throw new Exception("Error al actualizar el estado de la clase original: " . $stmt_update->error);
            }
        
            // Confirmar transacción
            $conn->commit();
            
            echo json_encode(["success" => "Clase reprogramada con éxito"]);
        
        } catch (Exception $e) {
            $conn->rollback(); // Revertir cambios si hay un error
            echo json_encode(["error" => $e->getMessage()]);
        }
    
        $stmt->close();
        $stmt_update->close();
        break;

    case 'editar':
        $id_programador = $_POST['id_programador'] ?? null;
        $fecha = $_POST['fecha'] ?? null;
        $hora_inicio = $_POST['hora_inicio'] ?? null;
        $hora_salida = $_POST['hora_salida'] ?? null;
        $salon = $_POST['id_salon'] ?? null;
        $docente = $_POST['numero_documento'] ?? null;
        $modulo = $_POST['id_modulo'] ?? null;
        $modalidad = $_POST['modalidad'] ?? null;
    
        // Validación de datos obligatorios
        if (!$id_programador || !$fecha || !$hora_inicio || !$hora_salida || !$salon || !$docente || !$modulo || !$modalidad) {
            echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
            exit;
        }
    
        $sql = "UPDATE programador 
                SET fecha=?, hora_inicio=?, hora_salida=?, id_salon=?, numero_documento=?, id_modulo=?, modalidad=? 
                WHERE id_programador=?";
    
        if (!$stmt = $conn->prepare($sql)) {
            echo json_encode(['status' => 'error', 'message' => 'Error en la preparación de la consulta: ' . $conn->error]);
            exit;
        }
    
        $stmt->bind_param('sssssssi', $fecha, $hora_inicio, $hora_salida, $salon, $docente, $modulo, $modalidad, $id_programador);
    
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Programador actualizado con éxito.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el programador: ' . $stmt->error]);
        }
    
        $stmt->close();
        break;
    
    case 'BusquedaPorId':
        $id_programador = $_POST['id_programador'] ?? null;

        if (!$id_programador) {
            echo json_encode(['error' => 'ID no proporcionado']);
            exit;
        }

        $sql = "SELECT 
                programador.*, 
                periodos.id_periodo
            FROM programador
            JOIN periodos ON programador.id_periodo = periodos.id_periodo
            WHERE programador.id_programador = ?";
    
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die(json_encode(['error' => 'Error en la preparación de la consulta: ' . $conn->error]));
        }

        $stmt->bind_param('i', $id_programador);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['data' => $data]);
        } else {
            echo json_encode(['error' => 'Registro no encontrado']);
        }

        $stmt->close();
        break;

        case 'contarClasesEstado':
            $sql = "SELECT 
                    SUM(estado = 'Pendiente') AS pendiente,
                    SUM(estado = 'Reprogramada') AS reprogramada,
                    SUM(estado = 'Perdida') AS perdida,
                    SUM(estado = 'Vista') AS vista
                FROM programador";
        
            $result = $conn->query($sql);
            
            if ($result) {
                $data = $result->fetch_assoc();
                foreach ($data as $key => $value) {
                    if (is_null($value)) {
                        $data[$key] = 0;
                    }
                }
                echo json_encode([
                    "pendiente" => $data['pendiente'],
                    "reprogramada" => $data['reprogramada'],
                    "perdida" => $data['perdida'],
                    "vista" => $data['vista']  // Añadido el estado "Vista"
                ]);
            } else {
                echo json_encode([
                    "error" => "Error al contar clases"
                ]);
            }
            break;

    default:
        $conn->query("SET lc_time_names = 'es_ES'");

        $sql = "SELECT
            id_programador,
            p.fecha,
            TIME_FORMAT(p.hora_inicio, '%H:%i:%s') as hora_inicio,
            TIME_FORMAT(p.hora_salida, '%H:%i:%s') as hora_salida,
            d.nombres,
            d.apellidos,
            p.estado,
            m.nombre AS nombre_modulo
        FROM programador p
        JOIN docentes d ON p.numero_documento = d.numero_documento
        LEFT JOIN modulos m ON p.id_modulo = m.id_modulo";

        $result = $conn->query($sql);

        $eventos = [];

        while ($row = $result->fetch_assoc()) {
            // Asegúrate de que p.fecha esté en formato YYYY-MM-DD
            $start = $row['fecha'] . "T" . $row['hora_inicio'];
            $end = $row['fecha'] . "T" . $row['hora_salida'];
            
            $eventos[] = [
                "id_programador" => $row['id_programador'],
                "title" => $row['nombre_modulo'] . " - " . $row['nombres'] . " " . $row['apellidos'],
                "start" => $start,
                "end" => $end,
                "estado" => $row['estado']
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($eventos);
                
        break;
}

$conn->close();

function docenteDisponible($docente_id, $fecha, $hora_inicio, $hora_fin, $conn, $excluir_id = null) {
    $sql = "SELECT id_programador 
            FROM programador 
            WHERE numero_documento = ? 
            AND fecha = ? 
            AND (
                (? < hora_salida AND ? > hora_inicio) OR  -- Solapamiento parcial
                (hora_inicio = ? AND hora_salida = ?)     -- Mismo horario exacto
            )";
    
    if ($excluir_id) {
        $sql .= " AND id_programador != ?";
    }

    $stmt = $conn->prepare($sql);
    $params = [$docente_id, $fecha, $hora_inicio, $hora_fin, $hora_inicio, $hora_fin];
    if ($excluir_id) $params[] = $excluir_id;
    
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $stmt->store_result();

    return ($stmt->num_rows === 0); // True si está disponible
}

function salonDisponible($salon_id, $fecha, $hora_inicio, $hora_fin, $conn, $excluir_id = null) {
    $sql = "SELECT id_programador 
            FROM programador 
            WHERE id_salon = ? 
            AND fecha = ? 
            AND (
                (? < hora_salida AND ? > hora_inicio) OR
                (hora_inicio = ? AND hora_salida = ?)
            )";
    
    if ($excluir_id) {
        $sql .= " AND id_programador != ?";
    }

    $stmt = $conn->prepare($sql);
    $params = [$salon_id, $fecha, $hora_inicio, $hora_fin, $hora_inicio, $hora_fin];
    if ($excluir_id) $params[] = $excluir_id;
    
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $stmt->store_result();

    return ($stmt->num_rows === 0); // True si está disponible
}

function horarioLaboralValido($hora_inicio, $hora_fin) {
    $hora_min = '07:00:00';
    $hora_max = '22:00:00';
    return ($hora_inicio >= $hora_min && $hora_fin <= $hora_max);
}

function validarHorasEntradaSalida($hora_inicio, $hora_fin) {
    $inicio = strtotime($hora_inicio);
    $fin = strtotime($hora_fin);

    return $fin > $inicio && ($fin - $inicio) >= 3600;
}

function docentePuedeDictarModulo($conn, $docente, $modulo) {
    $sql = "SELECT COUNT(*) AS total FROM docente_modulo WHERE numero_documento = ? AND id_modulo = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta de validación: " . $conn->error);
    }

    $stmt->bind_param("ii", $docente, $modulo);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['total'] > 0;
}
?>  