<?php
// api/turmas.php
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require 'conexaobd.php'; // Inclua seu arquivo de conexão

// ID da disciplina recebido via GET
$disciplinaId = isset($_GET['disciplina']) ? intval($_GET['disciplina']) : 0;

// Suponha que você tenha o ID do professor na sessão ou passe via GET/POST
$professorId = 1; // Exemplo fixo, altere para buscar da sessão ou request

if ($disciplinaId > 0) {
    // Query para buscar as turmas relacionadas à disciplina e ao professor
    $sql = "SELECT DISTINCT t.id_turma, t.nome_turma 
            FROM professor_leciona_turma AS plt 
            JOIN disciplina AS d ON plt.id_disciplina_fk = d.id_disciplina
            JOIN turma AS t ON plt.id_turma_fk = t.id_turma
            WHERE plt.id_professor_fk = ? && d.id_disciplina = ?;";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $professorId, $disciplinaId);
    $stmt->execute();
    $result = $stmt->get_result();

    $turmas = [];
    while ($row = $result->fetch_assoc()) {
        $turmas[] = $row;
    }

    echo json_encode($turmas);
} else {
    echo json_encode(['error' => 'ID da disciplina inválido']);
}

// Fechar conexão
$stmt->close();
?>
