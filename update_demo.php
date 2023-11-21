<?php
include('DBconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageId = $_POST['idImg'];
    $label = $_POST['modalImageLabel'];
    $description = $_POST['modalImageDescr'];

    // echo "Image ID: $imageId<br>";
    // echo "Label: $label<br>";
    // echo "Description: $description<br>";

    $newImgName = $_FILES['modalImageFile']['name'];
    $newImgTmpName = $_FILES['modalImageFile']['tmp_name'];
    $newImgPath = "/Applications/XAMPP/xamppfiles/htdocs/vsxampp/sitecadastro/dbImages/Images/" . $newImgName;
        
    // echo "newImgName: $newImgName<br>";
    // echo "newImgTmpName: $newImgTmpName<br>";
    // echo "newImgPath: $newImgPath<br>";
    
    if (isset($_FILES['modalImageFile']) && $_FILES['modalImageFile']['error'] === UPLOAD_ERR_OK) {
        
        
        $allowedFileTypes = array('jpg', 'jpeg', 'png', 'gif');
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        
        $fileExtension = strtolower(pathinfo($newImgName, PATHINFO_EXTENSION));
        
        if (in_array($fileExtension, $allowedFileTypes) && $_FILES['modalImageFile']['size'] <= $maxFileSize) {
            move_uploaded_file($newImgTmpName, $newImgPath);
            
            $updateQuery = "UPDATE Images SET img_path = ?, label = ?, Descr = ? WHERE idImg = ?";
            
            $stmt = mysqli_prepare($link, $updateQuery);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssi", $newImgName, $label, $description, $imageId);
                
                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Imagem atualizada com sucesso.'); window.history.back();</script>";
                    exit();
                } else {
                    echo 'Erro ao atualizar a imagem: ' . mysqli_error($link);
                }
                
                mysqli_stmt_close($stmt);
            } else {
                echo 'Erro na preparação da consulta: ' . mysqli_error($link);
            }
        } else {
            echo "Erro: O novo arquivo de imagem não é válido (formato ou tamanho incorreto).";
        }
    } else {
        echo "<script>alert('Nenhuma imagem nova foi enviada.'); window.history.back();</script>";
                
        $updateQuery = "UPDATE Images SET label = ?, Descr = ? WHERE idImg = ?";
        
        $stmt = mysqli_prepare($link, $updateQuery);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssi", $label, $description, $imageId);
            
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Informações atualizadas com sucesso.');window.history.back();</script>";
                exit();
            } else {
                echo 'Erro ao atualizar as informações: ' . mysqli_error($link);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            echo 'Erro na preparação da consulta: ' . mysqli_error($link);
        }
    }
} else {
    echo 'Requisição inválida.';
}
?>
