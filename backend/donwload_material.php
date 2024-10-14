<?php
include 'conexaobd.php'; // Conexão com o banco de dados
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Origin: *");

// Obtém o ID do material da URL
$id_material = $_GET['id'];

// Consulta para buscar o arquivo
$sql = "SELECT arquivo_material FROM material WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('i', $id_material);
$stmt->execute();
$stmt->bind_result($arquivo_material);
$stmt->fetch();
$stmt->close();

// Define o cabeçalho correto para download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="material_' . $id_material . '.pdf"'); // Ajuste o tipo de arquivo conforme necessário

// Exibe o conteúdo binário do arquivo
echo $arquivo_material;
?>
