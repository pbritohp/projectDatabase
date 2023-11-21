<?php
    include('DBconnect.php');
    include('loginCheck.php');
    $idSituation = 2; //Mostra apenas os Ativos

    
    function getlNameLaboratory($idLab) {
        global $link;
        $sql = "SELECT idLab, abb, lName, coord FROM Laboratory WHERE idLab = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $idLab);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return "ID: " . $row['idLab'] . ", abb: " . $row['abb'] . ", lName: " . $row['lName'] . ", coord: " . $row['coord'];
        } else {
            return 'Laboratório desconhecido';
        }
    }


    $searchQuery = "";
    if (!empty($_GET['search'])) {
        $searchQuery = $_GET['search'];
        $sql = "SELECT * FROM Laboratory WHERE 
                (idLab LIKE '%$searchQuery%' OR
                abb LIKE '%$searchQuery%' OR
                lName LIKE '%$searchQuery%' OR
                coord LIKE '%$searchQuery%') AND
                sit_Laboratory_id = '$idSituation'
                ORDER BY lName DESC";

        $stmt = $link->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT * FROM Laboratory WHERE sit_Laboratory_id = '$idSituation' ORDER BY idLab DESC";
        $stmt = $link->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    include('paginationLab.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>Sistema | SC2C</title>
    <style>
        body {
        font-family: Arial, Helvetica, sans-serif;
        background-image: linear-gradient(90deg,dodgerblue,dodgerblue, yellow);
    }



    .table-container {
        position: relative;
        margin-top: 75px;
        margin-bottom: 3%;
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
        width:40%;
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
</head>

<header class='header_nav'>
        <?php include('nav.php') ?>
</header>


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
    <h2 class = table-title>Laboratory Database</h2>
        <table class='table table-bg'>
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Abb</th>
                    <th scope="col">Name</th>
                    <th scope="col">Coordinator</th>
                    <th scope="col">...</th>
                </tr>
            </thead>       
            <tbody>
                <?php
                    while($user_data = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$user_data['idLab']."</td>";
                        echo "<td>".$user_data['abb']."</td>";
                        echo "<td>".$user_data['lName']."</td>";
                        echo "<td>".$user_data['coord']."</td>";
                        if ($logado) {
                            if ($tipoUsuario === 'usuario') {
                        echo "<td>  
                            <a class='btn btn-primary btn-sm' href='viewLab.php?id=" . $user_data['idLab'] . "'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-book-half' viewBox='0 0 16 16'>
                                    <path d='M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z'/>
                                </svg>
                            </a>
                        </td>";
                        } elseif ($tipoUsuario === 'Laboratory') {
                            echo "<td>                          
                            <a class='btn btn-primary btn-sm' href='viewLab.php?id=" . $user_data['idLab'] . "'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-book-half' viewBox='0 0 16 16'>
                                    <path d='M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z'/>
                                </svg>
                            </a>
                        </td>";
                        }
                        }
                        else{
                            echo "<td>                          
                            <a class='btn btn-primary btn-sm' href='viewLab.php?id=" . $user_data['idLab'] . "'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-book-half' viewBox='0 0 16 16'>
                                    <path d='M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z'/>
                                </svg>
                            </a>
                        </td>";
                        echo "</tr>";
                            }
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
                    $url = 'sistemaLab.php?page=1';
                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    echo '<li><a href="' . $url . '">&laquo;</a></li>';
                }

                for ($page = $startPage; $page <= $endPage; $page++) {
                    $url = 'sistemaLab.php?page=' . $page;
                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    $activeClass = ($page == $currentPage) ? 'active' : '';
                    echo '<li class="' . $activeClass . '"><a href="' . $url . '">' . $page . '</a></li>';
                }

                if ($endPage < $totalPages) {
                    $url = 'sistemaLab.php?page=' . $totalPages;
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
        var url = 'sistemaLab.php?search=' + encodeURIComponent(search.value) + '&page=' + pageNumber;
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

<?php include('footer.php') ?>

</html>