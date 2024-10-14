<?php
// Definir os cabeçalhos para permitir requisições
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Origin: *");

include 'conexaobd.php';

$query = "
    SELECT 
        u.nome_usuario, 
        a.matricula_aluno, 
        t.nome_turma, 
        t.fase_turma 
    FROM aluno a
    JOIN usuario u ON a.id_usuario_fk = u.id_usuario
    JOIN aluno_participa_turma apt ON a.id_aluno = apt.id_aluno_fk
    JOIN turma t ON apt.id_turma_fk = t.id_turma
";

// Executa a consulta
$result = $conexao->query($query);

$alunos = [];

if ($result->num_rows > 0) {
    // Adiciona cada aluno ao array
    while($row = $result->fetch_assoc()) {
        $alunos[] = $row;
    }
}

// Retorna a lista de alunos em formato JSON
header('Content-Type: application/json');
echo json_encode($alunos);
?>
