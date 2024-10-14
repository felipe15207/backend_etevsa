<?php
// api/disciplinas.php
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require 'conexaobd.php'; // Inclua seu arquivo de conexão

// Suponha que você tenha o ID do professor na sessão ou passe via GET/POST
$professorId = 1; // Exemplo fixo, altere para buscar da sessão ou request

// Query para buscar as disciplinas que o professor ensina
$sql = "SELECT DISTINCT d.id_disciplina, d.nome_disciplina 
FROM professor_leciona_turma AS plt 
JOIN disciplina AS d ON plt.id_disciplina_fk = d.id_disciplina 
WHERE plt.id_professor_fk = ?;
";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $professorId);
$stmt->execute();
$result = $stmt->get_result();

$disciplinas = [];
while ($row = $result->fetch_assoc()) {
    $disciplinas[] = $row;
}

echo json_encode($disciplinas);

// Fechar conexão
$stmt->close();
?>
