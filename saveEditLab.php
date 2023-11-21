<?php
include('DBconnect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update']) && isset($_POST['confirm']) && $_POST['confirm'] === 'true') {
    $id = $_POST['id'];
    $lName = $_POST['lName'];
    $abb = $_POST['abb'];
    $email = $_POST['email'];
    $link_site = $_POST['link_site'];
    $descr = $_POST['descr'];
    $telefone = $_POST['telefone'];
    $address = $_POST['address'];
    $cep = $_POST['cep'];
    $coord = $_POST['coord'];

    $sqlUpdate = "UPDATE Laboratory SET lName=?, abb=?, descr=?, link_site=?, email=?, telefone=?, address=?, cep=?, coord=? WHERE idLab=?";
    $updateQuery = mysqli_prepare($link, $sqlUpdate);
    mysqli_stmt_bind_param($updateQuery, "sssssssssi", $lName, $abb, $descr, $link_site, $email, $telefone, $address, $cep, $coord, $id);

    if (mysqli_stmt_execute($updateQuery)) {
        // Check if a file was uploaded
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logoName = $_FILES['logo']['name'];
            $logoTmpName = $_FILES['logo']['tmp_name'];
            $targetDirectory = '/Applications/XAMPP/xamppfiles/htdocs/vsxampp/sitecadastro/dbImages/Logos/'; // Set your target directory here
            $targetPath = $targetDirectory . $logoName;
        
            // Debugging: Check if the file is being received
            echo "File Name: " . $logoName . "<br>";
            echo "Temp Name: " . $logoTmpName . "<br>";
        
            if (move_uploaded_file($logoTmpName, $targetPath)) {
                // Debugging: Check if the file is being moved
                echo "File moved successfully to: " . $targetPath . "<br>";
        
                $updateLogoQuery = mysqli_prepare($link, "UPDATE Laboratory SET logo_path = ? WHERE idLab = ?");
                mysqli_stmt_bind_param($updateLogoQuery, "si", $logoName, $id);
        
                if (mysqli_stmt_execute($updateLogoQuery)) {
                    echo "File path updated in the database.<br>";
                } else {
                    echo "Error updating file path in the database: " . mysqli_error($link) . "<br>";
                }
        
                mysqli_stmt_close($updateLogoQuery);
            } else {
                // Debugging: Check if there are any errors during file move
                echo "Error moving file: " . $_FILES['logo']['error'] . "<br>";
            }
        } else {
            // Debugging: Check if there are any errors during file upload
            echo "File Upload Error: " . $_FILES['logo']['error'] . "<br>";
        }

        header('Location: sistemaLab.php');
        exit();
    } else {
        echo "Erro de atualização: " . mysqli_error($link);
    }
} else {
    echo "Erro ao processar o formulário.";
}
?>
