<?php
include('DBconnect.php');
include('loginCheck.php');

if ($tipoUsuario != 'usuario') {
    echo header('Location: login.php');
    exit();
}

$senhas_coincidem = true; // Inicializa como verdadeiro para não mostrar a mensagem de erro na primeira vez que a página é carregada

if (isset($_POST['submit'])) {
    $nome = $_POST['nome'];
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $ident = $_POST['ident'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Validar e limpar dados de entrada (sanitize) antes de usar
    $nome = mysqli_real_escape_string($link, $nome);
    $usuario = mysqli_real_escape_string($link, $usuario);
    $email = mysqli_real_escape_string($link, $email);
    $ident = mysqli_real_escape_string($link, $ident);

    // Verificar se a senha e a confirmação de senha são iguais
    if ($senha !== $confirmar_senha) {
        $senhas_coincidem = false;
    } else {
        // Hash da senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $query = mysqli_prepare($link, "INSERT INTO Usuario (nome, usuario, email, ident, senha_hash) VALUES (?, ?, ?, ?, ?)");

        mysqli_stmt_bind_param($query, "sssss", $nome, $usuario, $email, $ident, $senhaHash);

        if (mysqli_stmt_execute($query)) {
            echo "<script>alert('DONE')</script>";
        } else {
            echo "<script>alert('ERROR')</script>";
        }

        mysqli_stmt_close($query);
    }


// Erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Cadastro Usuário | SC2C</title>
</head>

<script>
    // Função para limpar os campos de senha
    function limparSenhas() {
        document.getElementById('senha').value = '';
        document.getElementById('confirmar_senha').value = '';
    }

    // Verificar se as senhas não coincidem e limpar os campos se necessário
    <?php if (!$senhas_coincidem): ?>
        alert('As senhas não coincidem. Por favor, tente novamente.');
        limparSenhas();
    <?php endif; ?>
</script>


<style>

    body{
            font-family: Arial, Helvetica, sans-serif;
            background-image: linear-gradient(45deg,cyan,white);
        }

    .box{
        color: white;
        margin-top: 2%;
        margin-left:30%;
        margin-bottom: 2%;
        width:500px;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;

    }

    fieldset{
        border: 3px solid dodgerblue;
    }

    legend{
        border: 1px solid dodgerblue;
        padding: 10px;
        background-color: dodgerblue;
        border-radius: 8px;
        font-size:20px;
    }

    .inputBox{
        position: relative;
    }

    .inputUser{
        background:none;
        border:none;
        border-bottom: 1px solid white;
        color:white;
        outline: none;
        font-size: 15px;
        width: 100%;
        letter-spacing:1px;
    }
    .labelInput{
        position: absolute;
        top:0px;
        left: 0px;
        pointer-events: none;
        transition: .5s;
    }
    .inputUser:focus ~ .labelInput,
    .inputUser:valid ~ .labelInput{
        top: -20px;
        font-size: 12px;
        color: dodgerblue;
    }
    .select{
        border: none;
        padding: 8px;
        border-radius: 10px;
        outline: none;
    }
    #submit{
        background-image: linear-gradient(to right,dodgerblue,dodgerblue);
        width: 100%;
        color:white;
        border: none;
        padding:15px;
        font-size:15px;
        cursor: pointer;
        border-radius: 10px;
        text-align: center;
    }
    #submit:hover{
        background-image: linear-gradient(to right,deepskyblue,deepskyblue);
    }


</style>

<header class="header_nav">
        <?php include('nav.php') ?>
</header>
<body>
    <div class="box">
        <form method='POST'>
            <fieldset>
                <legend  class="title"><b>Cadastro Usuário</b></legend>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="nome" id='nome' class='inputUser' required>
                    <label class="labelInput">Nome Completo</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="usuario" id='usuario' class='inputUser' required>
                    <label class="labelInput">Usuário</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="email" id='email' class='inputUser' required>
                    <label class="labelInput">Email</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="ident" id='ident' class='inputUser' required>
                    <label class="labelInput">Identificação(Matricula,Etc...)</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="password" name="senha" id="senha" class="inputUser" required>
                    <label class="labelInput">Senha</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="password" name="confirmar_senha" id="confirmar_senha" class="inputUser" required>
                    <label class="labelInput">Confirmar Senha</label> 
                </div>
                <br>
                <button type='submit' name="submit" id='submit'>Cadastrar</button>
            </fieldset>
        </form>
    </div>   
</body>

<?php include('footer.php')?>

</html>
