<?php
    include('DBconnect.php');
    include('loginCheck1.php');

    if ($tipoUsuario != 'usuario') {
        echo header('Location: login.php');
        exit();
    }

    $idSituation = [3];
    $idSituationString = implode(', ', $idSituation);

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

    function getNameLab($idLab) {
        global $link;
        $sql = "SELECT abb FROM Laboratory WHERE idLab = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $idLab);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['abb'];
        } else {
            return 'Laboratório desconhecido';
        }
    }


    $searchQuery = isset($_GET['search']) ? $_GET['search'] : "";
    $laboratoryFilter = isset($_GET['laboratory']) ? $_GET['laboratory'] : "";
    $categoryFilter = isset($_GET['category']) ? $_GET['category'] : "";
    $trlFilter = isset($_GET['trl']) ? $_GET['trl'] : "";

    $filterClauses = [];

    if (!empty($searchQuery)) {
        $filterClauses[] = "(idProj LIKE '%$searchQuery%' AND
                            pName LIKE '%$searchQuery%' AND
                            id_category IN (SELECT idCat FROM Category WHERE Category LIKE '%$searchQuery%') AND
                            id_TRL IN (SELECT idTRL FROM TRL WHERE TRL LIKE '%$searchQuery%') AND
                            resp LIKE '%$searchQuery%')";
    }

    if (!empty($laboratoryFilter)) {
        $filterClauses[] = "id_Laboratory = '$laboratoryFilter'";
    }

    if (!empty($categoryFilter)) {
        $filterClauses[] = "id_category = '$categoryFilter'";
    }

    if (!empty($trlFilter)) {
        $filterClauses[] = "id_TRL = '$trlFilter'";
    }

    // Combina todas as cláusulas de filtro usando a operação AND
    $filterClause = "";
    if (!empty($filterClauses)) {
        $filterClause = "AND (" . implode(" AND ", $filterClauses) . ")";
    }

    // Consulta SQL para contar registros
    $countSql = "SELECT COUNT(*) as total FROM Project WHERE 
                sit_Project_id IN ($idSituationString) $filterClause";

    $stmt = $link->prepare($countSql);
    $stmt->execute();
    $countResult = $stmt->get_result();
    $totalRecords = $countResult->fetch_assoc()['total'];

    $recordsPerPage = 4; // Define records per page aqui
    $totalPages = ceil($totalRecords / $recordsPerPage);

    // Consulta SQL principal com paginação
    $sql = "SELECT * FROM Project WHERE 
            sit_Project_id IN ($idSituationString) $filterClause
            ORDER BY pName DESC LIMIT ?, ?";

    $stmt = $link->prepare($sql);
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($currentPage - 1) * $recordsPerPage;
    $stmt->bind_param("ii", $offset, $recordsPerPage);
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
    <title>Approve Projects | SC2C</title>

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
        width:175px;
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
        <select class="form-control" id="filterLab">
            <option value="">Select Laboratory</option>
            <?php
                        $Laboratory = mysqli_query($link, "SELECT * FROM Laboratory");
                        while ($l = mysqli_fetch_array($Laboratory)){
                    ?>
                    <option value="<?php echo $l['idLab']?>"><?php echo $l['abb']?></option>
                    <?php } ?>
        </select>
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
    <h2 class = table-title>Pending projects</h2>
        <table class='table table-bg'>
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Research Field</th>
                    <th scope="col">TRL</th>
                    <th scope="col">Laboratory</th>
                    <th scope="col">Responsable</th>
                    <th scope="col">Insert Date</th>
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
                        echo "<td>" . getNameLab($user_data['id_Laboratory']) . "</td>";
                        echo "<td>".$user_data['resp']."</td>";
                        echo "<td>".$user_data['date_in']."</td>";
                        echo "<td>  
                                <a title = 'Review project' class='btn btn-success btn-sm' href='reviewProject.php?id=" . $user_data['idProj'] . "'>
                                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-check-square-fill' viewBox='0 0 16 16'>
                                        <path d='M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z'/>
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
                    $url = 'approveProjects.php?page=1';
                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    echo '<li><a href="' . $url . '">&laquo;</a></li>';
                }

                for ($page = $startPage; $page <= $endPage; $page++) {
                    $url = 'approveProjects.php?page=' . $page;
                    
                    // Adicione os filtros à URL se estiverem definidos
                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    if (!empty($laboratoryFilter)) {
                        $url .= '&laboratory=' . urlencode($laboratoryFilter);
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
                    $url = 'approveProjects.php?page=' . $totalPages;
                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    echo '<li><a href="' . $url . '">&raquo;</a></li>';
                }
            }
            ?>
        </ul>
    </div>

    <script>
    var search = document.getElementById('searchBD');
    var currentPage = <?php echo $currentPage; ?>; // Obtém a página atual do PHP

    search.addEventListener("keydown", function(event) { 
        if (event.key === "Enter") {
            searchData();
        }
    });

    function searchData() {
        var url = 'approveProjects.php';
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


</body>

<script>
    var search = document.getElementById('searchBD');

    search.addEventListener("keydown", function(event) { 
        if (event.key === "Enter") {
            searchData();
        }
    });

    function getParameterByName(name) {
        var url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

    function searchData() {
    var searchQuery = encodeURIComponent(document.getElementById('searchBD').value);
    var pageNumber = getParameterByName('page');
    if (!pageNumber) pageNumber = 1;
    var url = 'approveProjects.php?page=' + pageNumber;

    if (searchQuery) {
        url += '&search=' + searchQuery;
    }

    window.location = url;
}


function applyFilters() {
    var searchQuery = encodeURIComponent(document.getElementById('searchBD').value);
    var laboratoryFilter = document.getElementById('filterLab').value;
    var categoryFilter = document.getElementById('filterCategory').value;
    var trlFilter = document.getElementById('filterTRL').value;

    // Obtenha o valor da página atual da URL
    var pageNumber = getParameterByName('page');
    if (!pageNumber) pageNumber = 1;

    // Construa a URL com base nos filtros selecionados
    var url = 'approveProjects.php?page=' + pageNumber;

    if (searchQuery) {
        url += '&search=' + searchQuery;
    }

    if (laboratoryFilter) {
        url += '&laboratory=' + laboratoryFilter;
    }

    if (categoryFilter) {
        url += '&category=' + categoryFilter;
    }

    if (trlFilter) {
        url += '&trl=' + trlFilter;
    }

    window.location.href = url;
    }

</script>
<?php include('footer.php') ?>

</html>