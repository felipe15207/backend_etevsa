<?php 
$servidor = 'localhost';
$username = 'root';
$senha = '';
$database = 'site_etebd';

$conexao = new mysqli($servidor, $username, $senha, $database);

if($conexao->error) { // função que verificar se a conexão foi estabelecida
    die("FALHA NA CONEXÃO! " . $conexao->error);
} else {
    //echo "Conexão estabelecida com sucesso!";
}



?>