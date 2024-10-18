<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");

if (isset($_SESSION['id_usuario'])) {
    echo json_encode([
        "status" => "success",
        "id_usuario" => $_SESSION['id_usuario'],
        "id_cliente" => $_SESSION['id_cliente']
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Sessão não encontrada"
    ]);
}
?>
