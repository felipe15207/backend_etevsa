<?php 
$user = "root";
$password = "";

try {
    $connectbd = new PDO('mysql:host=localhost;dbname=site_etebd', $user, $password); // BANCO DE DADOS:host=CAMINHO BANCO;dbname=NOME BASE
    //echo "Conexão estabelecida!";
} catch(PDOException $e) {
    echo "Falha na conexão: " . $e->getMessage(); // getMessage() mostra o erro da conexão
    die();
}

?>