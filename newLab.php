<?php

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'lib/vendor/autoload.php';
include('DBconnect.php');
include('loginCheck.php');

if ($tipoUsuario != 'usuario') {
    echo header('Location: login.php');
    exit();
}

if (isset($_POST['submit'])) {
    $mailLab = $_POST['mailLab'];
    $abb = $_POST['abb'];

    $tempKey = generateRandomKey();

    $query = mysqli_prepare($link, "INSERT INTO Laboratory (abb,email, tempKey) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($query, "sss", $abb, $mailLab, $tempKey);

    if (mysqli_stmt_execute($query)) {
        $successLab = "Cadastro realizado com sucesso!";
        
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = 'b5ad514f1a13a1';
            $mail->Password = '4209a152752acd';

            $mail->setFrom('sc2c@sc2c.com', 'SC2C');
            $mail->addAddress($mailLab, $abb);
            $mail->isHTML(true);
            $mail->Subject = 'Novo cadastro';
            $mail->Body = $abb . ", segue o link de convite para se cadastrar na plataforma de banco de dados da SC2C <br><br>"
            . "Convite: <a href='http://localhost/vsxampp/sitecadastro/cadastro_lab.php?tempKey=" . $tempKey . "'>Clique aqui</a>";
            $mail->AltBody = $abb . ", segue o link de convite para se cadastrar na plataforma de banco de dados da SC2C \n\n"
            . "Convite: http://localhost/vsxampp/sitecadastro/cadastro_lab.php?tempKey=" . $tempKey;

            $mail->send();
            $successLab .= " Email enviado com sucesso!";
        } catch (Exception $e) {
            $errorLab = "Erro ao enviar o email: " . $mail->ErrorInfo;
        }

        mysqli_stmt_close($query);
    } else {
        $errorLab = "Erro ao cadastrar. Email já cadastrado ou aguardando cadastro.";
    }
}

function generateRandomKey($length = 12) {
    $randomBytes = random_bytes($length);
    $key = bin2hex($randomBytes);
    return $key;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>New Laboratory | SC2C</title>
</head>


<style>

    body{
            font-family: Arial, Helvetica, sans-serif;
            background-image: linear-gradient(90deg,cyan,white);
        }

    .box{
        position: relative;
        color: white;
        margin-top:5%;
        margin-left:5%;
        margin-bottom:10%;
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
    <div class="box">
        <form method='POST'>
            <fieldset>
                <legend class="title"><b>New Laboratory</b></legend>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="abb" id='abb' class='inputUser' required >
                    <label class="labelInput">abb</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="mailLab" id='mailLab' class='inputUser' required >
                    <label class="labelInput">Email</label> 
                </div>
                <br><br>
                <button type='submit' class='submit-c' name="submit" id='submit'>Cadastrar</button>
            </fieldset>
        </form>
    </div> 
</body>

<?php include('footer.php') ?>

</html>