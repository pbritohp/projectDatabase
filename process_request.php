<?php
include('DBconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submitRequest'])) {
        // Certifique-se de verificar se 'projectId' está definido antes de acessá-lo
        $projectId = isset($_POST['projectId']) ? $_POST['projectId'] : null;
        $cEmail = $_POST['cEmail'];

        if ($projectId !== null) {
            // Evite SQL Injection usando Prepared Statements
            $sqlInsert = "INSERT INTO ContactRequests (cEmail, projectId) VALUES (?, ?)";
            $stmt = $link->prepare($sqlInsert);
            $stmt->bind_param("si", $cEmail, $projectId);

            if ($stmt->execute()) {
                $stmt->close();
                // Adicione um log de sucesso
                error_log("Request submitted successfully!");
                echo "Request submitted successfully!";
            } else {
                // Adicione um log de erro
                error_log("Error submitting request. MySQL error: " . $stmt->error);
                echo "Error submitting request. Please try again.";
            }
        } else {
            // Adicione um log de erro para 'projectId' não definido
            error_log("Error submitting request. 'projectId' is not defined.");
            echo "Error submitting request. Please try again.";
        }
    }
} else {
    echo "Invalid request.";
}
?>
