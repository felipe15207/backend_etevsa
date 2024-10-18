<?php

header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

require "conexaobd.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['id_cliente'])) {
    $id_cliente = $_SESSION['id_cliente'];
    $disciplinaId = isset($_GET['disciplina']) ? intval($_GET['disciplina']) : 0;

    try {
        $consulta_turmas = $connectbd->prepare("
            SELECT t.id_turma, t.nome_turma FROM turma AS t
            JOIN professor_leciona_turma AS plt ON t.id_turma = plt.id_turma_fk
            JOIN professor AS p ON plt.id_professor_fk = p.id_professor
            JOIN disciplina AS d ON plt.id_disciplina_fk = d.id_disciplina
            WHERE p.id_professor = ? AND d.id_disciplina = ?
        ");

        $consulta_turmas->bindParam(1, $id_cliente);
        $consulta_turmas->bindParam(2, $disciplinaId);
        $consulta_turmas->execute();
        $turma = $consulta_turmas->fetchAll(PDO::FETCH_OBJ);

        if ($turma) {
            echo json_encode($turma);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Nenhuma turma encontrada para este professor."
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Erro ao consultar turmas: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Id do professor não encontrado ou sessão expirada."
    ]);
}
?>
