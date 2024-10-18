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
    
    try {
        $consulta_disciplina = $connectbd->prepare("
            SELECT DISTINCT d.id_disciplina, d.nome_disciplina 
            FROM professor AS p
            JOIN professor_leciona_turma AS plt ON p.id_professor = plt.id_professor_fk
            JOIN disciplina AS d ON plt.id_disciplina_fk = d.id_disciplina
            WHERE p.id_professor = ?
        ");
        
        $consulta_disciplina->bindParam(1, $id_cliente);
        $consulta_disciplina->execute();
        $disciplina = $consulta_disciplina->fetchAll(PDO::FETCH_OBJ);

        if ($disciplina) {
            echo json_encode($disciplina);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Nenhuma disciplina encontrada para este professor."
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Erro ao consultar disciplinas: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Id do professor não encontrado ou sessão expirada."
    ]);
}
?>
