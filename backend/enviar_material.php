<?php

header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

require "conexaobd.php";

$nome_material = $_POST['nome_material'] ?? '';
$descricao_material = $_POST['descricao'] ?? '';
$disciplinas_id = intval($_POST['disciplinas'] ?? 0);
$turmas = $_POST['turmas'] ?? [];

// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se a sessão 'id_cliente' existe
if (isset($_SESSION['id_cliente']) && isset($_SESSION['id_usuario'])) {
    $id_cliente = $_SESSION['id_cliente'];
    $id_usuario = $_SESSION['id_usuario'];

    $requiredFields = [$nome_material, $descricao_material, $disciplinas_id, count($turmas)];
    if (in_array('', $requiredFields, true)) {
        die("Preencha todos os campos obrigatórios.");
    }

    $data_material = date('d/m/Y');

    try {
        $insert_material = $connectbd->prepare("INSERT INTO material (id_professor_fk, id_disciplina_fk, nome_material, descricao_material, data_material) VALUES (?, ?, ?, ?, ?)");
        $insert_material->bindValue(1, $id_cliente, PDO::PARAM_INT); // id_cliente sendo usado como id_professor_fk
        $insert_material->bindValue(2, $disciplinas_id, PDO::PARAM_INT);
        $insert_material->bindValue(3, $nome_material, PDO::PARAM_STR);
        $insert_material->bindValue(4, $descricao_material, PDO::PARAM_STR);
        $insert_material->bindValue(5, $data_material, PDO::PARAM_STR);

        if ($insert_material->execute()) {
            $material_id = $connectbd->lastInsertId(); // Obtém o último ID inserido

            // Relaciona o material às turmas selecionadas
            foreach ($turmas as $turma_id) {
                $stmtTurma = $connectbd->prepare("INSERT INTO turma_estuda_material (id_material_fk, id_turma_fk) VALUES (?, ?)");
                $stmtTurma->bindValue(1, $material_id, PDO::PARAM_INT);
                $stmtTurma->bindValue(2, $turma_id, PDO::PARAM_INT);
                $stmtTurma->execute();
            }

            // Upload dos arquivos, se houver
            if (!empty($_FILES['arquivo']['name'][0])) {
                for ($i = 0; $i < count($_FILES['arquivo']['name']); $i++) {
                    $fileName = basename($_FILES['arquivo']['name'][$i]);
                    $fileName = preg_replace('/\s+/', '_', $fileName);
                    $fileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $fileName);
                    $filePath = "arqmaterial/" . $fileName;

                    // Nome personalizado inserido pelo professor
                    $nomeArquivoEscolhido = isset($_POST['nomearq'][$i]) ? $_POST['nomearq'][$i] : $fileName;

                    if (move_uploaded_file($_FILES['arquivo']['tmp_name'][$i], $filePath)) {
                        $sqlArquivo = "INSERT INTO arquivo_material (id_material_fk, url_arquivo, nome_arquivo) VALUES (?, ?, ?)";
                        $stmtArquivo = $connectbd->prepare($sqlArquivo);
                        $stmtArquivo->bindValue(1, $material_id, PDO::PARAM_INT);
                        $stmtArquivo->bindValue(2, $filePath, PDO::PARAM_STR);
                        $stmtArquivo->bindValue(3, $nomeArquivoEscolhido, PDO::PARAM_STR); // Nome inserido pelo professor
                        $stmtArquivo->execute();
                    } else {
                        echo "Erro ao enviar o arquivo: " . $_FILES['arquivo']['name'][$i];
                    }
                }
            }

            echo json_encode([
                "status" => "success",
                "message" => "Material enviado com sucesso!"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Falha no envio do material."
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Erro: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Usuário não autenticado."
    ]);
}

$connectbd = null; // Fecha a conexão
?>
