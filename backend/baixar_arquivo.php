<?php
// Inicia a sessão e define o cabeçalho de controle de acesso
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");

session_start();

// Diretório onde os arquivos estão armazenados
$diretorio = 'arqmaterial/'; // Pasta onde estão os arquivos

// Verifica se o arquivo foi passado como parâmetro
if (isset($_GET['file'])) {
    $file = basename($_GET['file']); // Remove qualquer caminho

    $filePath = $diretorio . $file; // Monta o caminho completo do arquivo

    // Verifica se o arquivo existe
    if (file_exists($filePath)) {
        // Configura os headers para download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        flush(); // Limpa o buffer do sistema
        readfile($filePath); // Lê o arquivo e envia para o output
        exit;
    } else {
        echo json_encode(["error" => "Arquivo não encontrado."]);
    }
} else {
    echo json_encode(["error" => "Nenhum arquivo especificado."]);
}
?>
