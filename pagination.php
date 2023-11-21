<?php
    $recordsPerPage = 3;
    $totalRecords = $result->num_rows;
    $totalPages = ceil($totalRecords / $recordsPerPage);

    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($currentPage - 1) * $recordsPerPage;


    if (!empty($searchQuery)) {
        $sql = "SELECT * FROM Project WHERE 
                (idProj LIKE '%$searchQuery%' OR
                pName LIKE '%$searchQuery%' OR
                id_category IN (SELECT idCat FROM Category WHERE Category LIKE '%$searchQuery%') OR
                id_TRL IN (SELECT idTRL FROM TRL WHERE TRL LIKE '%$searchQuery%') OR
                id_Laboratory IN (SELECT idLab FROM Laboratory WHERE abb LIKE '%$searchQuery%') OR
                resp LIKE '%$searchQuery%')AND
                sit_Project_id = $idSituation
                ORDER BY pName DESC LIMIT ?, ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("ii", $offset, $recordsPerPage);
    } else {
        $sql = "SELECT * FROM Project WHERE sit_Project_id = '$idSituation'ORDER BY idProj DESC LIMIT ?, ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("ii", $offset, $recordsPerPage);
    }

    

    $stmt->execute();
    $result = $stmt->get_result();
?>
