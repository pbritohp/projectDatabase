<?php

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include('DBconnect.php');
include('loginCheck.php');

if (isset($_POST['submitUser'])) {
    $mailUser = $_POST['mailUser'];
    $nome = $_POST['nome'];
    $tempKey = generateRandomKey();

    $hashedTempKey = password_hash($tempKey, PASSWORD_DEFAULT);

    $query = mysqli_prepare($link, "INSERT INTO Usuario (nome ,email, chave) VALUES (?, ?)");
    mysqli_stmt_bind_param($query, "ss", $mailUser, $hashedTempKey);


    if (mysqli_stmt_execute($query)) {
        $successUser = "Cadastro realizado com sucesso!";
    } else {
        $errorUser = "Erro ao cadastrar. Email já cadastrado ou aguardando cadastro.";
    }
    if (mysqli_stmt_execute($query)) {
        $successUser= "Cadastro realizado com sucesso!";
        $mail = new PHPMailer(TRUE);
        
        try {
            // Configurações do servidor de email aqui...
    
            $mail->addAddress($user_data['email'], $user_data['sigla']);
            $mail->isHTML(true);
            $mail->Subject = 'Novo cadastro';
            $mail->Body = $user_data['sigla'] . ", segue o link de convite para se cadastrar a plataforma de banco de dados da SC2C <br><br>"
            . "Convite: <a href='http://localhost/vsxampp/sitecadastro/cadastro_lab.php?chave=" . $tempKey . "'>Clique aqui</a>";
            $mail->AltBody = $user_data['sigla'] . ", segue o link de convite para se cadastrar a plataforma de banco de dados da SC2C \n\n"
            . "Convite: http://localhost/vsxampp/sitecadastro/cadastro_lab.php?chave=" . $tempKey;
    
            $mail->send();
        } catch (Exception $e) {
            // Tratamento de erro do envio de email...
        }
    } else {
        $errorUser = "Erro ao cadastrar. Email já cadastrado ou aguardando cadastro.";
    }
    
    mysqli_stmt_close($query);

    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    
}

function generateRandomKey($length = 12) {
    $randomBytes = random_bytes($length);

    $key = base64_encode($randomBytes);

    return $key;
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


<style>

    body{
            font-family: Arial, Helvetica, sans-serif;
            background-image: linear-gradient(90deg,cyan,white);
        }

    .box{
        color: white;
        margin-top:5%;
        margin-left:5%;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;
        width:400px;
    }

    fieldset{
        border: 3px solid dodgerblue;
        border-radius: 5px;
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
    .submit-c{
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
    .submit-c:hover{
        background-image: linear-gradient(to right,deepskyblue,deepskyblue);
    }


</style>

<header class="header_nav">
        <?php include('nav.php') ?>
</header>

<body>
    <div class= "box">
        <form method='POST'>
            <fieldset>
                <legend  class="title"><b>Cadastro Usuário</b></legend>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="nome" id='nome' class='inputUser' required >
                    <label class="labelInput">Nome do novo Usuario</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="mailUser" id='mailUser' class='inputUser' required >
                    <label class="labelInput">Email</label> 
                </div>
                <br><br>
                <button type='submit' class = 'submit-c' name="submitUser" id='submitUser'>Cadastrar</button>
            </fieldset>
        </form>
    </div>   
</body>
<?php include('footer.php') ?>
</html>