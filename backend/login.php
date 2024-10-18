<?php 

header("Access-Control-Allow-Origin: http://localhost"); 
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

require "conexaobd.php"; 

$email_login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_EMAIL);
$senha_login = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

if(!isset($_SESSION)) {
    session_start([
        'cookie_lifetime' => 24000,
        'cookie_secure' => false,
        'cookie_samesite' => 'Lax'
    ]);
}

try {
    $consulta_sql = $connectbd->prepare("SELECT id_usuario, email_usuario, senha_usuario, tipo_usuario FROM usuario WHERE email_usuario = ?");
    $consulta_sql->bindParam(1, $email_login);
    $consulta_sql->execute();
    $usuario = $consulta_sql->fetch(PDO::FETCH_OBJ);

    // LEMBRA DE TIRA QUANDO O HASH DE SENHA ESTIVE IMPLEMENTADO!!!!!
    if ($usuario && $usuario->senha_usuario === $senha_login) { // Verifica a senha antiga
        // Re-hash a senha usando password_hash() e atualize no banco de dados
        $senha_hash = password_hash($senha_login, PASSWORD_DEFAULT);
        $update_sql = $connectbd->prepare("UPDATE usuario SET senha_usuario = ? WHERE id_usuario = ?");
        $update_sql->bindParam(1, $senha_hash);
        $update_sql->bindParam(2, $usuario->id_usuario);
        $update_sql->execute();
    
        // Continua o processo de login
        // Código para redirecionamento e sessão...
    } else {
        // Tentativa de login falhou
        $response = [
            "status" => "error",
            "message" => 'Login ou senha incorretos.'
        ];
    }   
    // LEMBRA DE TIRA QUANDO O HASH DE SENHA ESTIVE IMPLEMENTADO!!!!! 

    if ($usuario && password_verify($senha_login, $usuario->senha_usuario)) {
        switch ($usuario->tipo_usuario) {
            case "aluno":
                $sql_cliente = $connectbd->prepare("SELECT id_aluno FROM aluno WHERE id_usuario_fk = ?");
                break;
            case "professor":
                $sql_cliente = $connectbd->prepare("SELECT id_professor FROM professor WHERE id_usuario_fk = ?");
                break;
            case "coordenador":
                $sql_cliente = $connectbd->prepare("SELECT id_coordenador FROM coordenador WHERE id_usuario_fk = ?");
                break;
            case "responsavel":
                $sql_cliente = $connectbd->prepare("SELECT id_responsavel FROM responsavel WHERE id_usuario_fk = ?");
                break;
            default:
                throw new Exception('Tipo de usuário não reconhecido.');
        }

        $sql_cliente->bindParam(1, $usuario->id_usuario);
        $sql_cliente->execute();
        $cliente = $sql_cliente->fetch(PDO::FETCH_OBJ);
        $_SESSION['id_cliente'] = $cliente->{'id_' . $usuario->tipo_usuario};
        $_SESSION['id_usuario'] = $usuario->id_usuario;

        $response = [
            "status" => "success",
            "tipo_usuario" => "dashboard_" . $usuario->tipo_usuario . ".html"
        ];
    } else {
        $response = [
            "status" => "error",
            "message" => 'Login ou senha incorretos.'
        ];
    }
} catch (Exception $e) {
    $response = [
        "status" => "error",
        "message" => "Erro ao processar a solicitação: " . $e->getMessage()
    ];
}

echo json_encode($response);
?>
