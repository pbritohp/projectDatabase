<?php
include('DBconnect.php');

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $sqlSelect = "SELECT * FROM Project WHERE idProj=$id";
    $result = $link->query($sqlSelect);

    if ($result->num_rows > 0) {
        if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
            $getImagesQuery = "SELECT img_path FROM Images WHERE idObj=$id";
            $resultGetImages = $link->query($getImagesQuery);

            if ($resultGetImages !== FALSE) {
                while ($imageRow = $resultGetImages->fetch_assoc()) {
                    $imagePath = "/Applications/XAMPP/xamppfiles/htdocs/vsxampp/sitecadastro/dbImages/Images/" . $imageRow['img_path'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                $deleteImagesQuery = "DELETE FROM Images WHERE idObj=$id";
                $resultDeleteImages = $link->query($deleteImagesQuery);

                if ($resultDeleteImages !== FALSE) {
                    $sqlDelete = "DELETE FROM Project WHERE idProj=$id";
                    $resultDelete = $link->query($sqlDelete);

                    if ($resultDelete !== FALSE) {
                        echo "<script>window.history.back();</script>";
                        exit();
                    } else {
                        echo "Error deleting record: " . $link->error;
                    }
                } else {
                    echo "Error deleting image records: " . $link->error;
                }
            } else {
                echo "Error getting image records: " . $link->error;
            }
        } else {
            echo '<script>
                var confirmed = confirm("Tem certeza que deseja deletar este registro?");
                if (confirmed) {
                    window.location.href = "deleteProj.php?id='.$id.'&confirm=true";
                } else {
                    window.history.back();
                }
            </script>';
        }
    } else {
        echo "<script>window.history.back();</script>";
        exit();
    }
} else {
    echo "<script>window.history.back();</script>";
    exit();
}
?>
