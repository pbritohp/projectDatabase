<?php
    include('loginCheck.php');
    include('DBconnect.php');

    if (!empty($_GET['id'])) {
        $id = $_GET['id'];
        $sqlSelect = "SELECT * FROM Laboratory WHERE idLab=$id";
        $result = $link->query($sqlSelect);

        if ($result->num_rows > 0) {
            if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
                $sqlDelete = "DELETE FROM Laboratory WHERE idLab=$id";
                $resultDelete = $link->query($sqlDelete);

                if ($resultDelete === TRUE) {
                    header('Location: sistemaLab.php');
                    exit();
                } else {
                    echo "Error deleting record: " . $link->error;
                }
            } else {
                echo '<script>
                    var confirmed = confirm("Tem certeza que deseja deletar este registro?");
                    if (confirmed) {
                        window.location.href = "deleteLab.php?id='.$id.'&confirm=true";
                    } else {
                        window.location.href = "sistemaLab.php";
                    }
                </script>';
            }
        } else {
            header('Location: sistemaLab.php');
            exit();
        }
    } else {
        header('Location: sistemaLab.php');
        exit();
    }
?>
