<?php
// Exibir erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Conectar ao banco de dados
$conexao = new mysqli("localhost", "root", "", "seu_banco_de_dados");

if ($conexao->connect_error) {
    die(json_encode(['error' => 'Falha na conexão com o banco de dados: ' . $conexao->connect_error]));
}

// Consulta SQL ajustada
$sql = "SELECT m.id_material, m.nome_material, 
               SUBSTRING_INDEX(u.nome_usuario, ' ', 2) AS nome_professor, 
               d.nome_disciplina 
        FROM material m
        JOIN professor p ON m.id_professor_fk = p.id_professor
        JOIN usuario u ON p.id_usuario_fk = u.nome_usuario
        JOIN disciplina d ON m.id_disciplina_fk = d.id_disciplina";

$resultado = $conexao->query($sql);

if ($resultado === false) {
    die(json_encode(['error' => 'Erro na consulta: ' . $conexao->error]));
}

if ($resultado->num_rows > 0) {
    $materiais = [];

    while ($linha = $resultado->fetch_assoc()) {
        $materiais[] = [
            'id_material' => $linha['id_material'],
            'nome_material' => $linha['nome_material'],
            'nome_professor' => $linha['nome_usuario'],
            'disciplina' => $linha['nome_disciplina']
        ];
    }

    // Retornar os dados em formato JSON
    echo json_encode($materiais);
} else {
    echo json_encode(['error' => 'Nenhum material encontrado']);
}

$conexao->close();
?>
