<?php
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'lib/vendor/autoload.php';
include('DBconnect.php');

// Define a função generateRandomKey no escopo global
function generateRandomKey($length = 12) {
    $randomBytes = random_bytes($length);
    $key = bin2hex($randomBytes);
    return $key;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];
    $sqlSelect = "SELECT * FROM Laboratory WHERE email='$email'";
    $result = $link->query($sqlSelect);

    if ($result->num_rows > 0) {
        $lab_data = mysqli_fetch_assoc($result);

        $lName = $lab_data['lName'];
        $abb = $lab_data['abb'];
        $mailLab = $lab_data['email'];
    } else {
        header('Location: sistemaLab.php');
        exit();
    }

    $tempKey = generateRandomKey();

    $query = mysqli_prepare($link, "UPDATE Laboratory SET tempKey=? WHERE idLab=?");
    mysqli_stmt_bind_param($query, "si", $tempKey, $lab_data['idLab']);


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
            $mail->Body = $abb . ", segue o link para a troca de senha da plataforma de banco de dados da SC2C <br><br>"
                . "Convite: <a href='http://localhost/vsxampp/sitecadastro/changePassword.php?tempKey=" . $tempKey . "'>Clique aqui</a>";
            $mail->AltBody = $abb . ", segue o link para a troca de senha da plataforma de banco de dados da SC2C \n\n"
                . "Convite: http://localhost/vsxampp/sitecadastro/changePassword.php?tempKey=" . $tempKey;

            $mail->send();
            $successLab .= " Email enviado com sucesso!";

            header("Location: login.php");
            exit();
        } catch (Exception $e) {
            $errorLab = "Erro ao enviar o email: " . $mail->ErrorInfo;
        }

        mysqli_stmt_close($query);
    } else {
        $errorLab = "Erro ao cadastrar. Email já cadastrado ou aguardando cadastro.";
    }
}
?>
