<?php
include('DBconnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update']) && isset($_POST['confirm']) && $_POST['confirm'] === 'true') {
    $id = $_POST['id'];
    $lName = $_POST['lName_lab'];
    $abb = $_POST['abb'];
    $email = $_POST['email'];
    $link_site = $_POST['link_site'];
    $descr = $_POST['descr'];
    $telefone = $_POST['telefone'];
    $address = $_POST['address'];
    $cep = $_POST['cep'];
    $coord = $_POST['coord'];
    
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    if ($senha !== $confirmar_senha) {
        echo "<script>alert('As senhas não coincidem. Por favor, tente novamente.'); window.history.back();</script>";
        exit;
    }
    
    $senhahash = password_hash($senha, PASSWORD_DEFAULT);

    // Prepare and execute the update query for laboratory information
    $sqlUpdate = "UPDATE Laboratory SET lName=?, abb=?, descr=?, link_site=?, email=?, telefone=?, address=?, cep=?, coord=?, senha_hash=?, date_in=NOW() WHERE idLab=?";
    $updateQuery = mysqli_prepare($link, $sqlUpdate);
    mysqli_stmt_bind_param($updateQuery, "ssssssssssi", $lName, $abb, $descr, $link_site, $email, $telefone, $address, $cep, $coord, $senhahash, $id);
    
    $result = mysqli_stmt_execute($updateQuery);

    // Handle logo upload
    if ($_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $logoName = $_FILES['logo']['name'];
        $logoTmpName = $_FILES['logo']['tmp_name'];
        $targetDirectory = '/Applications/XAMPP/xamppfiles/htdocs/vsxampp/sitecadastro/dbImages/Logos/'; //if there is a need, we can put a specific directory
        $targetPath = $targetDirectory . $logoName;
        
        // Validate file type and size
        $allowedFileTypes = array('jpg', 'jpeg', 'png', 'gif');
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        $fileExtension = strtolower(pathinfo($logoName, PATHINFO_EXTENSION));
        
        if (in_array($fileExtension, $allowedFileTypes) && $_FILES['logo']['size'] <= $maxFileSize) {
            if (move_uploaded_file($logoTmpName, $targetPath)) {
                // Update logo path in the database
                $updateLogoQuery = mysqli_prepare($link, "UPDATE Laboratory SET logo_path = ? WHERE idLab = ?");
                mysqli_stmt_bind_param($updateLogoQuery, "si", $logoName, $id);
                mysqli_stmt_execute($updateLogoQuery);
                mysqli_stmt_close($updateLogoQuery);
            }
        }
    }

    // Check if update was successful and handle tempKey and user status update
    if ($result === TRUE) {
        $updatetempKeyQuery = "UPDATE Laboratory SET tempKey = NULL, sit_Laboratory_id = 2 WHERE idLab = '$id'";
        $updatetempKeyResult = $link->query($updatetempKeyQuery);

        if ($updatetempKeyResult === TRUE) {
            header('Location: Login.php');
            exit();
        } else {
            echo "Erro ao atualizar a tempKey e situação do usuário: " . $link->error;
        }
    } else {
        echo "Erro de atualização: " . $link->error;
    }

    // Close the prepared statement
    mysqli_stmt_close($updateQuery);
} else {
    echo "Erro ao processar o formulário.";
}
?>
