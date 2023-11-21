<?php
    $recordsPerPage = 4;
    $totalRecords = $result->num_rows;
    $totalPages = ceil($totalRecords / $recordsPerPage);

    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($currentPage - 1) * $recordsPerPage;

    if (!empty($searchQuery)) {
        $sql = "SELECT * FROM Laboratory WHERE 
                (idLab LIKE '%$searchQuery%' OR
                abb LIKE '%$searchQuery%' OR
                lName LIKE '%$searchQuery%' OR
                coord LIKE '%$searchQuery%')
                AND sit_Laboratory_id = '$idSituation'
                ORDER BY lName DESC LIMIT ?, ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("ii", $offset, $recordsPerPage);
    } else {
        $sql = "SELECT * FROM Laboratory WHERE sit_Laboratory_id = '$idSituation' ORDER BY idLab DESC LIMIT ?, ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("ii", $offset, $recordsPerPage);
    }

    $stmt->execute();
    $result = $stmt->get_result();
?>
