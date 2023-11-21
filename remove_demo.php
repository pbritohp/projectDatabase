<?php
include('DBconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageId = $_POST['imageId'];

    $imageQuery = mysqli_query($link, "SELECT img_path FROM Images WHERE idImg=$imageId");
    if ($imageQuery && $imageRow = mysqli_fetch_assoc($imageQuery)) {
        // Exibe um alerta de confirmação em PHP
        echo '<script>
            if (confirm("Tem certeza de que deseja remover este registro de imagem?")) {
                window.location.href = "remove_demo.php?confirmed=true&imageId=' . $imageId . '";
            } else {
                window.history.back();
            }
        </script>';
    } else {
        echo 'Imagem não encontrada.';
    }
} elseif ($_GET['confirmed'] === 'true' && !empty($_GET['imageId'])) {
    $imageId = $_GET['imageId'];

    $deleteQuery = "DELETE FROM Images WHERE idImg=?";
    $stmt = mysqli_prepare($link, $deleteQuery);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $imageId);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>window.history.back();</script>";
            exit();

        } else {
            echo 'Erro ao remover o registro da imagem do banco de dados: ' . mysqli_error($link);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo 'Erro na preparação da consulta: ' . mysqli_error($link);
    }
} else {
    echo 'Requisição inválida.';
}
?>
