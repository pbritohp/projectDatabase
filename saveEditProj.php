<?php
include('DBconnect.php');

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $pName = $_POST['pName_Project'];
    $category = $_POST['category'];
    $TRL = $_POST['TRL'];
    $link_ref = $_POST['link_ref'];
    $resp = $_POST['resp'];
    $email_resp = $_POST['email_resp'];
    $descr = $_POST['descr'];
    
    // Verificar se a variável $situation está definida e não vazia
    $situation = isset($_POST['situation']) ? $_POST['situation'] : '';

    // Recupere a situação atual do projeto
    $sqlSelectCurrentSituation = "SELECT sit_project_id FROM Project WHERE idProj = ?";
    $stmtSelect = $link->prepare($sqlSelectCurrentSituation);

    if ($stmtSelect) {
        $stmtSelect->bind_param("i", $id);
        $stmtSelect->execute();
        $stmtSelect->bind_result($currentSituation);
        $stmtSelect->fetch();
        $stmtSelect->close();
    }

    if (empty($situation)) {
        // Se a situação não foi selecionada, use a situação atual
        $situation = $currentSituation;
    }

    if ($currentSituation == 5) {
        $situation = 3;
    }

    // Prepare a SQL statement with placeholders
    $sqlUpdate = "UPDATE Project SET 
        pName = ?, 
        id_category = ?, 
        id_TRL = ?, 
        link_ref = ?, 
        resp = ?, 
        email = ?, 
        descr = ?, 
        sit_project_id = ? 
        WHERE idProj = ?";

    // Prepare the statement
    $stmt = $link->prepare($sqlUpdate);

    if ($stmt) {
        // Bind parameters and execute the statement
        $stmt->bind_param("siississi", $pName, $category, $TRL, $link_ref, $resp, $email_resp, $descr, $situation, $id);

        if ($stmt->execute()) {
            echo "Projeto atualizado com sucesso.";
        } else {
            echo "Erro ao atualizar o projeto: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Erro na preparação da declaração: " . $link->error;
    }
}

// Redirecione para a página principal após a conclusão da atualização
header('Location: sistema.php');
exit();
?>
