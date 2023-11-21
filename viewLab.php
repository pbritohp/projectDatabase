<?php
    include('loginCheck.php');
    include('DBconnect.php');

    if (!empty($_GET['id'])) {
        $id = $_GET['id'];
        $sqlSelect = "SELECT * FROM Laboratory WHERE idLab=?";
        $stmt = $link->prepare($sqlSelect);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

    
        if ($result->num_rows > 0) {
            $lab_data = mysqli_fetch_assoc($result);
    
            $lName = $lab_data['lName'];
            $abb = $lab_data['abb'];
            $email = $lab_data['email'];
            $link_site = $lab_data['link_site'];
            $descr = $lab_data['descr'];
            $telefone = $lab_data['telefone'];
            $address = $lab_data['address'];
            $cep = $lab_data['cep'];
            $coord = $lab_data['coord'];
            $logo = $lab_data['logo_path'];

        } else {
            header('Location: sistemaLab.php');
            exit();
        }
    } else {
        header('Location: sistemaLab.php');
        exit();
    }

$idSituation = [2];
$idSituationString = implode(', ', $idSituation);

$queryCategory = "SELECT * FROM Category";
$queryTRL = "SELECT * FROM TRL";
$queryLaboratory = "SELECT * FROM Laboratory WHERE idLab = $id";

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
$stmt->bind_param("s", $id);
$stmt->execute();
$countResult = $stmt->get_result();
$totalRecords = $countResult->fetch_assoc()['total'];

$recordsPerPage = 2; // Define records per page here
$totalPages = ceil($totalRecords / $recordsPerPage);

// SQL main query with pagination
$sql = "SELECT * FROM Project WHERE 
        sit_Project_id IN ($idSituationString) AND
        id_Laboratory = ? $filterClause
        ORDER BY pName DESC LIMIT ?, ?";

$stmt = $link->prepare($sql);
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $recordsPerPage;
$stmt->bind_param("sii", $id, $offset, $recordsPerPage);
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
    <title>View Lab | SC2C</title>
</head>

<header class='header_nav'>
        <?php include('nav.php') ?>
</header>

<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background-image: linear-gradient(90deg, dodgerblue, dodgerblue, yellow);
    }

    .project-container {
        color: white;
        margin-left:2%;
        margin-top:5%;
        width: 80%;
        position: relative;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;
    }

    .legend-container {
        display: flex;
        justify-content: space-between; /* Horizontally align content */
        align-items: center; /* Vertically align content */
    }

    .name-box {
        border: 2px solid white;
        padding: 10px;
        background-color: black;
        border-radius: 8px;
        font-size: 20px;
    }

    .trl-box {
        border: 2px solid white;
        padding: 10px;
        background-color: black;
        border-radius: 8px;
        font-size: 20px;
    }

    .text-box {
        position: relative;
    }

    .description-box {
        color: black;
        border-radius: 10px;
        background-color: white;
        border: 2px solid black;
        padding: 10px;
    }

    .demonstrator-container {
        color: white;
        margin-top:3%;
        margin-left:2%;
        width: 80%;
        position: relative;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;
    }

    .image-container {
    display: flex;
    align-items: start;
    margin-bottom: 20px;
    }

    .image-description {
        display: flex;
        align-items: center;
    }

    .logolab {
    background-color: white;
    max-width: 100%;
    height: auto;
    margin-right: 10px;
    border-radius:15px;
    width: 150px;
    padding: 10px;
    }

    .description-box {
    color: black;
    border-radius: 10px;
    background-color: white;
    border: 2px solid black;
    padding: 10px;
    width: 100%;
    overflow-y: auto;
    max-height: 3000px;
    word-wrap: break-word;
    font-size: 14px;
    line-height: 1.5;
    }   

    .label-box {
        border: 2px solid white;
        padding: 10px;
        background-color: black;
        border-radius: 8px;
        font-size: 20px;
        font-weight: bold;
    }


    .table-container {
        position: relative;
        margin-top: 5px;
        margin-bottom: 5%;
    }   

    .table-bg{
        color: white;
        background: rgba(0,0,0,0.6);
        padding: 30px;
        border-radius: 15px;       
        
    }

    .pagination {
        position: relative;
    }


    .box-search{
        justify-content: start;
        margin-top: 1%;
        display: flex;
        gap: .1%;
    }

    .table-title{
        color: white;
    }

    .projects{
        margin-left: 2%;
        width:80%;
    }

    

</style>



<body>
    <div class="project-container">
            <div class="legend-container">
                <div class="name-box"><b><?php echo $abb ?> - </b><b><?php echo $lName ?></b></div>
                <img class="logolab" src="dbimages/Logos/<?php echo $logo; ?>">
            </div>
            <br>
            <div class="contet-container">               
                <label for="resp">Laboratory Coordinator:</label>
                <br>
                <div class='readonly-field'><?php echo $coord; ?></div>
                <br><br>
                <label for="Description">Laboratory Descripition:</label>
                <br>
                <div class='description-box'><?php echo $descr; ?></div>
                <br><br>
            </div>                       
    </div>

    <div class="projects">      
        <h2 class = table-title>Registered Projects</h2>
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
            <table class='table table-bg'>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Research Field</th>
                        <th scope="col">TRL</th>
                        <th scope="col">Responsable</th>
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

                            echo "<td class='actions-cell'>  
                            <a title = 'Preview project' class='btn btn-primary btn-sm' href='viewProject.php?id=" . $user_data['idProj'] . "'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-book-half' viewBox='0 0 16 16'>
                                <path d='M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z'/>
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
                        $url = 'viewLab.php?page=1' . '&id=' . $id;
                        if (!empty($searchQuery)) {
                            $url .= '&search=' . urlencode($searchQuery);
                        }
                        echo '<li><a href="' . $url . '">&laquo;</a></li>';
                    }

                    for ($page = $startPage; $page <= $endPage; $page++) {
                        $url = 'viewLab.php?page=' . $page . '&id=' . $id;
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
                        $url = 'viewLab.php?page=' . $totalPages;
                        if (!empty($searchQuery)) {
                            $url .= '&search=' . urlencode($searchQuery);
                        }
                        echo '<li><a href="' . $url . '">&raquo;</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>  


</body>

<script>
    var search = document.getElementById('searchBD');
    var filterCategory = document.getElementById('filterCategory');
    var filterTRL = document.getElementById('filterTRL');

    search.addEventListener("keydown", function(event) { 
        if (event.key === "Enter") {
            applyFilters();
        }
    });

    function applyFilters() {
        var url = 'viewLab.php?id=<?php echo $id; ?>';
        var searchQuery = encodeURIComponent(search.value);
        var categoryFilter = filterCategory.value;
        var trlFilter = filterTRL.value;

        if (searchQuery) {
            url += '&search=' + searchQuery;
        }

        if (categoryFilter) {
            url += '&category=' + categoryFilter;
        }

        if (trlFilter) {
            url += '&trl=' + trlFilter;
        }

        window.location = url;
    }

    var currentPage = <?php echo $currentPage; ?>;

    search.addEventListener("keydown", function(event) { 
        if (event.key === "Enter") {
            searchData();
        }
    });

    function searchData() {
        var url = 'viewLab.php?id=<?php echo $id; ?>';
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

<?php include('footer.php') ?>




</html>