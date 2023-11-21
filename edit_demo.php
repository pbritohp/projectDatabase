<?php
include('loginCheck.php');
include('DBconnect.php');

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $sqlSelect = "SELECT * FROM Project WHERE idProj=$id";
    $result = $link->query($sqlSelect);
    if ($result->num_rows > 0) {
        while ($user_data = mysqli_fetch_assoc($result)) {
            $name = $user_data['pName'];
        }
    } else {
        header('Location: sistema.php');
        exit();
    }
} else {
    header('Location: sistema.php');
    exit();
}

$images = [];
$imageQuery = mysqli_query($link, "SELECT * FROM Images WHERE idObj=$id");
while ($imageRow = mysqli_fetch_assoc($imageQuery)) {
    $images[] = $imageRow;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Demonstrators | SC2C</title>
</head>

<header class="header_nav">
    <?php include('nav.php') ?>
</header>

<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background-image: linear-gradient(90deg, dodgerblue, dodgerblue, yellow);
    }

    .project-container {
        color: white;
        margin-left: 2%;
        margin-top: 2%;
        width: 80%;
        position: relative;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;
    }

    .legend-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
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
        margin-top: 3%;
        margin-left: 2%;
        width: 80%;
        position: relative;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;
        margin-bottom:5%;
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

    img {
        max-width: 100%;
        height: auto;
        margin-right: 10px;
        border-radius: 15px;
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

    .edit-button {
        background-color: dodgerblue;
        width: 100%;
        color:white;
        border-color: solid black;
        padding:15px;
        font-size:15px;
        cursor: pointer;
        border-radius: 10px;
        text-align: center;
        margin-top:3px;
        transition: all 0.5s ease 0s;
    }
    .edit-button:hover{
        background-color: deepskyblue;
    }

    .remove-button {
        background-color: dodgerblue;
        width: 100%;
        color:white;
        border-color: solid black;
        padding:15px;
        font-size:15px;
        cursor: pointer;
        border-radius: 10px;
        text-align: center;
        margin-top:3px;
        transition: all 0.5s ease 0s;
    }
    .remove-button:hover{
        background-color: rgb(156, 52, 52);
        color: black;
    }

    .add-button {
        position: relative;
        background-color: dodgerblue;
        width: 30%;
        color:white;
        border-color: solid black;
        padding:15px;
        font-size:15px;
        cursor: pointer;
        border-radius: 10px;
        text-align: center;
        margin-top:3px;
        transition: all 0.5s ease 0s;
        margin-top:1%;
        margin-left:2% ;
        margin-bottom:2%;
    }
    .add-button:hover{
        background-color: 	rgb(79, 121, 66);
        color: black;
    }

    .modal-content{
        color: white;
        position: absolute;
        top: 50%;
        left: 50%;
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        width:50%;
        background-color: rgba(0, 0, 0, 0.9);
        padding: 15px;
        border-radius: 15px;

    }

    .modal-title{
        border-bottom: 4px solid white;
    }

    .inputUserDesc{
        background: white;
        border: color: white;
        border-radius: 10px;
        outline: none;
        resize: none;
        font-size: 15px;
        letter-spacing:1px;
        width: 100%;
        height: 150px;
    }

    .inputUserLabel{
        background: white;
        border: color: white;
        border-radius: 10px;
        outline: none;
        font-size: 15px;
        letter-spacing:1px;
    }

    .modal-submit{
        background-image: linear-gradient(to right,dodgerblue,dodgerblue);
        width: 100%;
        color:white;
        border: none;
        padding:15px;
        font-size:15px;
        cursor: pointer;
        border-radius: 10px;
        text-align: center; 
    }
    .modal-submit:hover{
        background-image: linear-gradient(to right,deepskyblue,deepskyblue);
    }
    .action-container{
        gap:10px;
    }
</style>

<body>

    
        <div class="demonstrator-container">
        <div class="legend-container">
                <div class="name-box"><b><?php echo $name; ?></b><b> - Demonstrators</b></div>
            </div>
            <button class="add-button" id="addImageBtn">Add New Demonstrator</button>
        <?php if (!empty($images)) : ?>
         
            <br>
            <?php foreach ($images as $index => $image) : ?>
                <div class="label-box"><?php echo $image['label']; ?></div>
                <br>
                <div class="image-container">
                    <br>
                    <div class="action-container">
                        <img src="dbimages/Images/<?php echo $image['img_path']; ?>">
                        <button class="edit-button" data-index="<?php echo $index; ?>">Edit</button>
                        <form action="remove_demo.php" method="post" class="remove-form">
                            <input type="hidden" name="imageId" value="<?php echo $image['idImg']; ?>">
                            <button class="remove-button" type="submit" class="remove-button">Remove</button>
                        </form>
                    </div>
                    <div class="description-box"><?php echo $image['Descr']; ?></div>
                    <br>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>


    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2 class="modal-title">Editar Demonstrador</h2>
            <br>
            <form id="editForm" action="update_demo.php" method="post" enctype="multipart/form-data">
                <input type="hidden" id="modalImageId" name="idImg">
                <label for="modalImageFile">Nova Imagem:</label>
                <input type="file" id="modalImageFile" name="modalImageFile">
                <br><br>
                <label for="modalImageLabel">Label da Imagem:</label>
                <input class="inputUserLabel" type="text" id="modalImageLabel" name="modalImageLabel" required>
                <br><br>
                <label for="modalImageDescr">Descrição da Imagem:</label>
                <br>
                <textarea class="inputUserDesc" id="modalImageDescr" name="modalImageDescr" required></textarea>
                <br><br>
                <button class="modal-submit" type="button" id="modalSaveButton">Salvar</button>
            </form>
        </div>
    </div>

    <div id="addImageModal" class="modal" style="display:none ;">
        <div class="modal-content">
            <span class="close" id="closeAddImageModal">&times;</span>
            <h2 class="modal-title">Register New Image</h2>
            <br>
            <form id="addImageForm" action="add_demo.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="idObj" value="<?php echo $id; ?>">
                <label for="addImageFile">New Image:</label>
                <input type="file" id="addImageFile" name="addImageFile" required>
                <br><br>
                <label for="addImageLabel">Image Label:</label>
                <input class="inputUserLabel" type="text" id="addImageLabel" name="addImageLabel" required>
                <br><br>
                <label for="addImageDescr">Description:</label>
                <br><br>
                <textarea class="inputUserDesc"  id="addImageDescr" name="addImageDescr" required></textarea>
                <br><br>
                <button class="modal-submit" type="submit" id="addImageButton">Save</button>
            </form>
        </div>
    </div>

</body>

<script>
    const editModal = document.getElementById('editModal');
    const closeModal = document.getElementById('closeModal');
    const modalForm = document.getElementById('editForm');
    const modalFileInput = document.getElementById('modalLabel');
    const modalImageLabelInput = document.getElementById('modalImageLabel');
    const modalImageDescrTextarea = document.getElementById('modalImageDescr');
    const modalImageIdInput = document.getElementById('modalImageId');
    const modalSaveButton = document.getElementById('modalSaveButton');

    closeModal.addEventListener('click', function () {
        editModal.style.display = 'none';
    });

    function openEditModal(imageId, label, description) {
        editModal.style.display = 'block';
        modalImageIdInput.value = imageId;
        modalImageLabelInput.value = label;
        modalImageDescrTextarea.value = description;
    }

    const editButtons = document.querySelectorAll('.edit-button');
    editButtons.forEach(function (button, index) {
    button.addEventListener('click', function () {
            const imageIndex = button.getAttribute('data-index');
            const demonstratorData = <?php echo json_encode($images); ?>[imageIndex];
            if (demonstratorData) {
                openEditModal(demonstratorData.idImg, demonstratorData.label, demonstratorData.Descr);
            }
        });
    });


    modalSaveButton.addEventListener('click', function () {
        modalForm.submit();
    });

    const addImageBtn = document.getElementById('addImageBtn');
    const addImageModal = document.getElementById('addImageModal');
    const closeAddImageModal = document.getElementById('closeAddImageModal');
    const addImageButton = document.getElementById('addImageButton');

    addImageBtn.addEventListener('click', function () {
        addImageModal.style.display = 'block';
    });

    closeAddImageModal.addEventListener('click', function () {
        addImageModal.style.display = 'none';
    });

    addImageButton.addEventListener('click', function () {
        const addImageForm = document.getElementById('addImageForm');
        addImageForm.submit();
    });

    

</script>
<?php include('footer.php')?>

</html>

