<?php
    // Verifica si la sesión está iniciada y tiene el valor
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    } // Esto debe estar al inicio del script
    $docente = $_SESSION['numero_documento'] ?? null;

    if (!$docente) {
        echo json_encode(["error" => "No se encontró número de documento en sesión"]);
        exit;
    }
