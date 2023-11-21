<?php
include('DBconnect.php');
include('loginCheck1.php');

$idSituation = [1, 2, 3, 4, 5];
$idSituationString = implode(', ', $idSituation);

$idLab = $_SESSION['idLab'];

$queryCategory = "SELECT * FROM Category";
$queryTRL = "SELECT * FROM TRL";
$queryLaboratory = "SELECT * FROM Laboratory";

function getNameCategory($idCat) {
    global $link;
    $sql = "SELECT Category FROM Category WHERE idCat = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $idCat);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['Category'];
    } else {
        return 'Categoria desconhecida';
    }
}

function getNameTrl($idTRL) {
    global $link;
    $sql = "SELECT TRL FROM TRL WHERE idTRL = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $idTRL);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['TRL'];
    } else {
        return 'TRL desconhecido';
    }
}

$searchQuery = isset($_GET['search']) ? $_GET['search'] : "";
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : "";
$trlFilter = isset($_GET['trl']) ? $_GET['trl'] : "";

$filterClauses = [];

if (!empty($searchQuery)) {
    $filterClauses[] = "(idProj LIKE '%$searchQuery%' OR
                        pName LIKE '%$searchQuery%' OR
                        id_category IN (SELECT idCat FROM Category WHERE Category LIKE '%$searchQuery%') OR
                        id_TRL IN (SELECT idTRL FROM TRL WHERE TRL LIKE '%$searchQuery%') OR
                        resp LIKE '%$searchQuery%')";
}

if (!empty($categoryFilter)) {
    $filterClauses[] = "id_category = '$categoryFilter'";
}

if (!empty($trlFilter)) {
    $filterClauses[] = "id_TRL = '$trlFilter'";
}

// Combine all filter clauses using the OR operation
$filterClause = "";
if (!empty($filterClauses)) {
    $filterClause = "AND (" . implode(" AND ", $filterClauses) . ")";
}

// SQL query to count records
$countSql = "SELECT COUNT(*) as total FROM Project WHERE 
             sit_Project_id IN ($idSituationString) AND
             id_Laboratory = ? $filterClause";

$stmt = $link->prepare($countSql);
$stmt->bind_param("s", $idLab);
$stmt->execute();
$countResult = $stmt->get_result();
$totalRecords = $countResult->fetch_assoc()['total'];

$recordsPerPage = 3; // Define records per page here
$totalPages = ceil($totalRecords / $recordsPerPage);

// SQL main query with pagination
$sql = "SELECT * FROM Project WHERE 
        sit_Project_id IN ($idSituationString) AND
        id_Laboratory = ? $filterClause
        ORDER BY pName DESC LIMIT ?, ?";

$stmt = $link->prepare($sql);
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $recordsPerPage;
$stmt->bind_param("sii", $idLab, $offset, $recordsPerPage);
$stmt->execute();
$result = $stmt->get_result();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Sistema | SC2C</title>

</head>

<header class='header_nav'>
        <?php include('nav.php') ?>
</header>


<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background-image: linear-gradient(90deg,dodgerblue,dodgerblue, yellow);
    }

    .table-container {
        position: relative;
        margin-top: 75px;
    }   

    .table-bg{
        color: white;
        background: rgba(0,0,0,0.6);
        margin-left:5%;
        width: 90%;
        padding: 30px;
        border-radius: 15px;       
        
    }

    .pagination {
        position: relative;
        margin-left: 5%;
    }

    .form-control{
        width:300px;
    }

    .box-search{
        justify-content: center;
        margin-top: 5%;
        display: flex;
        gap: .1%;
    }

    .table-title{
        margin-left:6.5%;
        color: white;
    }
        
</style>

<body>
    <div class="box-search">
        <input type="search" class="form-control" placeholder="Search" id="searchBD">
        <select class="form-control" id="filterCategory">
            <option value="">Select Category</option>
            <?php
                        $Category = mysqli_query($link, "SELECT * FROM Category");
                        while ($c = mysqli_fetch_array($Category)){
                    ?>
                    <option value="<?php echo $c['idCat']?>"><?php echo $c['Category']?></option>
                    <?php } ?>
        </select>
        <select class="form-control" id="filterTRL">
            <option value="">Select TRL</option>
            <?php
                        $TRL = mysqli_query($link, "SELECT * FROM TRL");
                        while ($d = mysqli_fetch_array($TRL)){
                    ?>
                    <option value="<?php echo $d['idTRL']?>"><?php echo $d['TRL']?></option> 
                    <?php } ?>
        </select>
        <button onclick="applyFilters()" class="btn btn-primary btn-sm">Apply Filters</button>
    </div>


    <div class="table-container">
    <h2 class = table-title>My Projects</h2>
        <table class='table table-bg'>
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Research Field</th>
                    <th scope="col">TRL</th>
                    <th scope="col">Responsable</th>
                    <th scope="col">Insert Date</th>
                    <th scope="col">Situation</th>
                    <th scope="col">...</th>
                </tr>
            </thead>       
            <tbody>
                <?php
                    while($user_data = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$user_data['idProj']."</td>";
                        echo "<td>".$user_data['pName']."</td>";
                        echo "<td>" . getNameCategory($user_data['id_category']) . "</td>";
                        echo "<td>" . getNameTrl($user_data['id_TRL']) . "</td>";
                        echo "<td>".$user_data['resp']."</td>";
                        echo "<td>".$user_data['date_in']."</td>";
                        //echo "<td>".$user_data['sit_project_id']."</td>";
                        if($user_data['sit_project_id'] == 1){
                            echo "<td>
                                Inactive
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-exclamation-circle-fill' viewBox='0 0 16 16'>
                                    <path d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z'/>
                                </svg>
                            <td>";
                        }
                        elseif($user_data['sit_project_id'] == 2){
                            echo "<td>
                                Active
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-check-circle-fill' viewBox='0 0 16 16'>
                                    <path d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z'/>
                                </svg>
                            <td>";
                        }elseif($user_data['sit_project_id'] == 3){
                            echo "<td>
                                Waiting Confirmation
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-clock-fill' viewBox='0 0 16 16'>
                                    <path d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z'/>
                                </svg>
                            <td>";
                        }elseif($user_data['sit_project_id'] == 4){
                            echo "<td>
                                Invisible
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' 'height='16' fill='currentColor' class='bi bi-eye-slash-fill' viewBox='0 0 16 16'>
                                    <path d='m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z'/>
                                    <path d='M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z'/>
                                </svg>
                            <td>";
                        }elseif($user_data['sit_project_id'] == 5){
                        echo "<td>
                            Waiting Correction
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' 'height='16' fill='currentColor' class='bi bi-eye-slash-fill' viewBox='0 0 16 16'>
                                <path d='m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z'/>
                                <path d='M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z'/>
                            </svg>
                        <td>";
                    }

                        echo "<td class='actions-cell'>  
                        <a title = 'Preview project' class='btn btn-primary btn-sm' href='viewProject.php?id=" . $user_data['idProj'] . "'>
                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-book-half' viewBox='0 0 16 16'>
                            <path d='M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z'/>
                        </svg>
                        </a>
                        <a title = 'Edit project' class='btn btn-primary btn-sm' href='edit_Project.php?id=" . $user_data['idProj'] . "'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                                <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z'/>
                            </svg>
                        </a>

                        <a title = 'Edit Demonstrators' class='btn btn-primary btn-sm' href='edit_Demo.php?id=" . $user_data['idProj'] . "'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-file-earmark-image' viewBox='0 0 16 16'>
                                <path d='M6.502 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z'/> <path d='M14 14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5V14zM4 1a1 1 0 0 0-1 1v10l2.224-2.224a.5.5 0 0 1 .61-.075L8 11l2.157-3.02a.5.5 0 0 1 .76-.063L13 10V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4z'/>
                        </svg>
                        </a>
                        
                        <a title = 'Delete project' class='btn btn-danger btn-sm' href='deleteProj.php?id=" . $user_data['idProj'] . "'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 16 16'>
                                <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z'/>
                            </svg>
                        </a>

                        </td>";
                
                        echo "</tr>";
                    }
                        
                ?>
            </tbody>
        </table>
        <ul class="pagination">
            <?php
            if ($totalPages > 1) {
                $maxVisiblePages = 3;
                $startPage = max($currentPage - floor($maxVisiblePages / 2), 1);
                $endPage = min($startPage + $maxVisiblePages - 1, $totalPages);

                if ($startPage > 1) {
                    $url = 'myProjects.php?page=1';
                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    echo '<li><a href="' . $url . '">&laquo;</a></li>';
                }

                for ($page = $startPage; $page <= $endPage; $page++) {
                    $url = 'myProjects.php?page=' . $page;
                    
                    // Adicione os filtros à URL se estiverem definidos
                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    if (!empty($categoryFilter)) {
                        $url .= '&category=' . urlencode($categoryFilter);
                    }
                    if (!empty($trlFilter)) {
                        $url .= '&trl=' . urlencode($trlFilter);
                    }
                    
                    $activeClass = ($page == $currentPage) ? 'active' : '';
                    echo '<li class="' . $activeClass . '"><a href="' . $url . '">' . $page . '</a></li>';
                }

                if ($endPage < $totalPages) {
                    $url = 'myProjects.php?page=' . $totalPages;
                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    echo '<li><a href="' . $url . '">&raquo;</a></li>';
                }
            }
            ?>
        </ul>
    </div>

</body>

<script>
    var search = document.getElementById('searchBD');
    var filterCategory = document.getElementById('filterCategory');
    var filterTRL = document.getElementById('filterTRL');
    var applyFiltersButton = document.getElementById('applyFiltersButton');

    search.addEventListener("keydown", function(event) { 
        if (event.key === "Enter") {
            applyFilters();
        }
    });

    applyFiltersButton.addEventListener("click", applyFilters);

    function applyFilters() {
        var searchQuery = encodeURIComponent(search.value);
        var categoryFilter = encodeURIComponent(filterCategory.value);
        var trlFilter = encodeURIComponent(filterTRL.value);

        var url = "myProjects.php?page=1";

        if (searchQuery !== "") {
            url += '&search=' + searchQuery;
        }
        if (categoryFilter !== "") {
            url += '&category=' + categoryFilter;
        }
        if (trlFilter !== "") {
            url += '&trl=' + trlFilter;
        }

        window.location.href = url;
    }

    var currentPage = <?php echo $currentPage; ?>; // Obtém a página atual do PHP

    search.addEventListener("keydown", function(event) { 
        if (event.key === "Enter") {
            searchData();
        }
    });

    function searchData() {
        var url = 'myProjects.php';
        var searchQuery = encodeURIComponent(search.value);

        if (currentPage) {
            url += '?page=' + currentPage;
        }

        if (searchQuery) {
            url += '&search=' + searchQuery;
        }

        window.location = url;
    }
</script>


<?php include('footer.php')?>

</html>