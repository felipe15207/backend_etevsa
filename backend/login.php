<?php

include("conexaobd.php");

header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Origin: *");


if(isset($_POST["login"]) && isset($_POST["senha"])) {
    $email = $conexao->real_escape_string($_POST["login"]);
    $senha = $conexao->real_escape_string($_POST["senha"]);

    $busca_sql = "SELECT email_usuario, senha_usuario, tipo_usuario FROM usuario WHERE email_usuario = '$email' AND senha_usuario = '$senha'";
    $resultado_busca = $conexao->query($busca_sql) or die("BUSCA FALHOU..." . $conexao->error);
    $valida_usuario = $resultado_busca->num_rows; // passa a qtd de linhas se for 1 ele existe
    if($valida_usuario == 1) {

        $usuario = $resultado_busca->fetch_assoc(); 
        if(!isset($_SESSION)) {
            session_start(); // inicia a sessão se ela não existe...
        }

        $_SESSION['usuario-logado'] = $usuario['email_usuario']; //sessao é uma variavel que vai continua valida mesmo quando o usuario troca de página ou periodo de tempo
        
        if($usuario['tipo_usuario'] =="coordenador") {
            $response = [
                "status" => "success",
                "tipo_usuario" => "home-coordenador.html"
            ];
        } else if($usuario['tipo_usuario']== "professor") {
            $response = [
                "status" => "success",
                "tipo_usuario" => "home-professor.html"
            ];
        } else if($usuario['tipo_usuario']== "aluno") {
            $response = [
                "status" => "success",
                "tipo_usuario" => "home-aluno.html"
            ];
        }

        echo json_encode($response);

    } else {
        $response = [
            "status" => "error",
            "message" => "login ou senha incorretos!"
        ];

        echo json_encode($response);
    }
} else {
    $response = [
        "status" => "error",
        "message" => "login e senha inexistente..."
    ];

    echo json_encode($response);
}

?>