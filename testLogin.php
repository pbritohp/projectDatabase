<?php
session_start();

if (isset($_SESSION['login_error'])) {
    // Exibe a mensagem de erro na tela de login
    $login_error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
} else if (isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    // Acesso ao DB
    include_once('DBconnect.php');
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Procura no DB na tabela Usuario
    $sqlUsuario = "SELECT * FROM Usuario WHERE email = ?";
    $stmtUsuario = $link->prepare($sqlUsuario);
    $stmtUsuario->bind_param("s", $email);
    $stmtUsuario->execute();
    $resultUsuario = $stmtUsuario->get_result();

    // Procura no DB na tabela Laboratory
    $sqlLaboratory = "SELECT * FROM Laboratory WHERE email = ?";
    $stmtLaboratory = $link->prepare($sqlLaboratory);
    $stmtLaboratory->bind_param("s", $email);
    $stmtLaboratory->execute();
    $resultLaboratory = $stmtLaboratory->get_result();

    if ($resultUsuario->num_rows > 0) {
        $user = $resultUsuario->fetch_assoc();
        // Verificar a senha usando password_verify()
        if (password_verify($senha, $user['senha_hash'])) {
            // Verificar o status do usuário
            if ($user['sit_usuario_id'] == 2) {
                // Definir a chave 'tipo_usuario' na sessão
                $_SESSION['tipo_usuario'] = 'usuario';
                // ACESSO AO SISTEMA como Usuário
                $_SESSION['email'] = $email;
                $_SESSION['senha'] = $senha;
                $_SESSION['logado'] = 'usuario';
                header('Location: sistema.php');
                exit();
            } else {
                // Usuário não tem status ativo
                $_SESSION['login_error'] = 'Usuário inativo ou aguardando confirmação';
                header('Location: login.php');
                exit();
            }
        } else {
            // Senha incorreta
            $_SESSION['login_error'] = 'Senha incorreta';
            header('Location: login.php');
            exit();
        }
    } elseif ($resultLaboratory->num_rows > 0) {
        $lab = $resultLaboratory->fetch_assoc();
        // Verificar a senha usando password_verify()
        if (password_verify($senha, $lab['senha_hash'])) {
            if ($lab['sit_Laboratory_id'] == 2) {
                $_SESSION['tipo_usuario'] = 'Laboratory';
                $_SESSION['email'] = $email;
                $_SESSION['senha'] = $senha;
                $_SESSION['idLab'] = $lab['idLab'];
                
                $_SESSION['logado'] = 'Laboratory';
                header('Location: sistema.php');
                exit();
            } else {
                $_SESSION['login_error'] = 'Laboratório inativo ou aguardando confirmação';
                header('Location: login.php');
                exit();
            }
        } else {
            $_SESSION['login_error'] = 'Senha incorreta';
            header('Location: login.php');
            exit();
        }
    }
}  
?>
