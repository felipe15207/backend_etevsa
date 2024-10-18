<?php
// Inicia a sessão e define os headers de controle de acesso
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

session_start();

// Verifica se o id_cliente está na sessão
if (isset($_SESSION['id_cliente'])) {
    $id_aluno = $_SESSION['id_cliente'];

    try {
        // Inclui a conexão com o banco de dados usando PDO
        require("conexaobd.php");

        // 1. Consulta para obter as turmas que o aluno participa
        $sql_turmas = "
            SELECT t.id_turma 
            FROM aluno_participa_turma AS apt
            JOIN turma AS t ON apt.id_turma_fk = t.id_turma
            WHERE apt.id_aluno_fk = :id_aluno
        ";
        $stmt_turmas = $connectbd->prepare($sql_turmas);
        $stmt_turmas->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);
        $stmt_turmas->execute();

        // Coleta todos os IDs de turma em um array
        $turmas = $stmt_turmas->fetchAll(PDO::FETCH_COLUMN);

        if (count($turmas) > 0) {
            // 2. Consulta para obter os materiais das turmas
            $placeholders = implode(',', array_fill(0, count($turmas), '?'));
            $sql_materiais = "
                SELECT m.id_material, m.nome_material, m.descricao_material, m.data_material, u.nome_usuario, d.nome_disciplina 
                FROM material AS m
                JOIN turma_estuda_material AS tem ON m.id_material = tem.id_material_fk
                JOIN turma AS t ON tem.id_turma_fk = t.id_turma
                JOIN disciplina AS d ON m.id_disciplina_fk = d.id_disciplina
                JOIN professor AS p ON m.id_professor_fk = p.id_professor
                JOIN usuario AS u ON p.id_usuario_fk = u.id_usuario
                WHERE t.id_turma IN ($placeholders)
            ";
            $stmt_materiais = $connectbd->prepare($sql_materiais);
            $stmt_materiais->execute($turmas);

            // Cria um array para armazenar os materiais
            $materiais = $stmt_materiais->fetchAll(PDO::FETCH_ASSOC);

            // Para cada material, busca os arquivos relacionados
            foreach ($materiais as &$material) {
                $sql_arquivos = "
                    SELECT ar.nome_arquivo, ar.url_arquivo 
                    FROM arquivo_material AS ar 
                    WHERE ar.id_material_fk = :id_material
                ";
                $stmt_arquivos = $connectbd->prepare($sql_arquivos);
                $stmt_arquivos->bindParam(':id_material', $material['id_material'], PDO::PARAM_INT);
                $stmt_arquivos->execute();

                // Adiciona os arquivos ao material
                $arquivos = $stmt_arquivos->fetchAll(PDO::FETCH_ASSOC);

                // Se não houver arquivos, adiciona um objeto indicando que não há arquivos
                if (empty($arquivos)) {
                    $material['arquivos'] = [
                        'message' => 'Nenhum arquivo disponível para este material.'
                    ];
                } else {
                    $material['arquivos'] = $arquivos;
                }
            }

            // Retorna os materiais em formato JSON
            echo json_encode($materiais);

        } else {
            // Caso o aluno não esteja matriculado em nenhuma turma
            echo json_encode([]);
        }
    } catch (PDOException $e) {
        // Caso ocorra algum erro com o banco de dados
        echo json_encode(["error" => "Erro na conexão com o banco de dados: " . $e->getMessage()]);
    }
} else {
    // Caso o id_cliente não esteja disponível na sessão
    echo json_encode(["error" => "ID de aluno não encontrado na sessão."]);
}
?>
