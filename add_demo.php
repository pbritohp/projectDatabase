<?php
include('DBconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idObj = $_POST['idObj'];
    $label = $_POST['addImageLabel'];
    $description = $_POST['addImageDescr'];

    $newImgName = $_FILES['addImageFile']['name'];
    $newImgTmpName = $_FILES['addImageFile']['tmp_name'];
    $newImgPath = "/Applications/XAMPP/xamppfiles/htdocs/vsxampp/sitecadastro/dbImages/Images/" . $newImgName;

    $imageCountQuery = "SELECT COUNT(*) AS imageCount FROM Images WHERE idObj = ?";
    $stmt = mysqli_prepare($link, $imageCountQuery);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $idObj);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $imageCount);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        
        if ($imageCount >= 5) {
            echo "<script>alert('O projeto já possui o máximo de 5 imagens permitidas.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo 'Erro na preparação da consulta: ' . mysqli_error($link);
        exit();
    }

    if (isset($_FILES['addImageFile']) && $_FILES['addImageFile']['error'] === UPLOAD_ERR_OK) {
        $allowedFileTypes = array('jpg', 'jpeg', 'png', 'gif');
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        $fileExtension = strtolower(pathinfo($newImgName, PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedFileTypes) && $_FILES['addImageFile']['size'] <= $maxFileSize) {
            move_uploaded_file($newImgTmpName, $newImgPath);

            $insertQuery = "INSERT INTO Images (idObj, img_path, label, Descr) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($link, $insertQuery);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "isss", $idObj, $newImgName, $label, $description);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Imagem adicionada com sucesso.'); window.history.back();</script>";
                    exit();
                } else {
                    echo 'Erro ao adicionar a imagem: ' . mysqli_error($link);
                }

                mysqli_stmt_close($stmt);
            } else {
                echo 'Erro na preparação da consulta: ' . mysqli_error($link);
            }
        } else {
            echo "Erro: O novo arquivo de imagem não é válido (formato ou tamanho incorreto).";
        }
    } else {
        //echo "<script>alert('Nenhuma imagem nova foi enviada.'); window.history.back();</script>";
    }
} else {
    echo 'Requisição inválida.';
}
?>
