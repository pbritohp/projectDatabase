<?php
    include('DBconnect.php');
    include('loginCheck1.php');
    $idSituation = [3];
    $idSituationString = implode(', ', $idSituation);

    $idLab = $_SESSION['idLab'];

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


    $searchQuery = "";
    if (!empty($_GET['search'])) {
        $searchQuery = $_GET['search'];
        $sql = "SELECT * FROM Project WHERE 
                (idProj LIKE '%$searchQuery%' OR
                pName LIKE '%$searchQuery%' OR
                id_category IN (SELECT idCat FROM Category WHERE Category LIKE '%$searchQuery%') OR
                id_TRL IN (SELECT idTRL FROM TRL WHERE TRL LIKE '%$searchQuery%') OR
                resp LIKE '%$searchQuery%') AND 
                (sit_Project_id = '$idSituationString'  AND
                id_Laboratory = '$idLab')
                ORDER BY pName DESC";


        $stmt = $link->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT * FROM Project WHERE sit_Project_id = '$idSituationString' AND
        id_Laboratory = '$idLab' ORDER BY idProj DESC";
        $stmt = $link->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    include('paginationMP.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>Sistema | SC2C</title>

</head>

<header class='header_nav'>
        <?php include('nav.php') ?>
</header>
<?php include('footer.php') ?>


<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background-image: linear-gradient(90deg,dodgerblue,dodgerblue, yellow);
    }

    .table-container {
        position: relative;
        margin-top: 150px;
        margin-bottom: 10%;
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
        margin-top: .5%;
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
        <button onclick="searchData()" class="btn btn-primary btn-sm">
            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-search' viewBox='0 0 16 16'>
                <path d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/>
            </svg>
        </button>
    </div>

    <div class="table-container">
    <h2 class = table-title>Pending Projects</h2>
        <table class='table table-bg'>
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">pName</th>
                    <th scope="col">Categoria</th>
                    <th scope="col">TRL</th>
                    <th scope="col">Responsavel</th>
                    <th scope="col">Link</th>
                    <th scope="col">Actions</th>
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
                        echo "<td>".$user_data['link_ref']."</td>";

                        echo "<td>  
                        <a title = 'Review project' class='btn btn-success btn-sm' href='reviewProject?id=" . $user_data['idProj'] . "'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-check-square-fill' viewBox='0 0 16 16'>
                                <path d='M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z'/>
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
                $maxVisiblePages = 3;
                $startPage = max($currentPage - floor($maxVisiblePages / 2), 1);
                $endPage = min($startPage + $maxVisiblePages - 1, $totalPages);

                if ($startPage > 1) {
                    $url = 'penddingProjects.php?page=1';
                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    echo '<li><a href="' . $url . '">&laquo;</a></li>';
                }

                for ($page = $startPage; $page <= $endPage; $page++) {
                    $url = 'penddingProjects.php?page=' . $page;
                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    $activeClass = ($page == $currentPage) ? 'active' : '';
                    echo '<li class="' . $activeClass . '"><a href="' . $url . '">' . $page . '</a></li>';
                }

                if ($endPage < $totalPages) {
                    $url = 'penddingProjects.php?page=' . $totalPages;
                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    echo '<li><a href="' . $url . '">&raquo;</a></li>';
                }
            ?>
        </ul>
    </div>
</body>

<script>
    var search = document.getElementById('searchBD');

    search.addEventListener("keydown", function(event) { 
        if (event.key === "Enter") {
            searchData();
        }
    });

    function searchData() {
        var pageNumber = getParameterByName('page');
        if (!pageNumber) pageNumber = 1;
        var url = 'penddingProjects.php?search=' + encodeURIComponent(search.value) + '&page=' + pageNumber;
        window.location = url;
    }

    function getParameterByName(name) {
        var url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }
</script>
</html>