<?php
include('loginCheck1.php');
include('DBconnect.php');

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

if(isset($_GET['projectID'])) {
    $projectID = $_GET['projectID'];

    if(isset($_POST['submit'])){
        if (isset($_FILES['image']) && is_array($_FILES['image'])) {
            $allowedFileTypes = array('jpg', 'jpeg', 'png', 'gif');
            $maxFileSize = 5 * 1024 * 1024;

            for ($i = 0; $i < count($_FILES['image']['name']); $i++) {
                if ($_FILES['image']['error'][$i] === UPLOAD_ERR_OK) {
                    $imgName = $_FILES['image']['name'][$i];
                    $imgTmpName = $_FILES['image']['tmp_name'][$i];
                    $imgPath = "/Applications/XAMPP/xamppfiles/htdocs/vsxampp/sitecadastro/dbImages/Images/" . $imgName;

                    $fileExtension = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
                    if (in_array($fileExtension, $allowedFileTypes) && $_FILES['image']['size'][$i] <= $maxFileSize) {
                        move_uploaded_file($imgTmpName, $imgPath);

                        $imgLabel = $_POST['imageLabel'][$i];
                        $imgDescr = $_POST['imageDescr'][$i];

                        $queryImg = "INSERT INTO Images (idObj, img_path, label, Descr) VALUES ('$projectID', '$imgName', '$imgLabel', '$imgDescr')";
                        mysqli_query($link, $queryImg);
                    } else {
                        echo "Erro: O arquivo '$imgName' não é válido (formato ou tamanho incorreto).";
                    }
                }
            }
            echo '<script>alert("Done"); window.location.href = "sistema.php";</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Demonstrators| SC2C</title>
</head>

<style>

    body{
            font-family: Arial, Helvetica, sans-serif;
            background-image: linear-gradient(90deg,cyan,white);
        }

    .box{
        color: white;
        margin-top: 2%;
        margin-left:30%;
        margin-bottom: 2%;
        width:500px;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;

    }

    fieldset{
        border: 3px solid dodgerblue;
        width:100%
    }

    legend{
        border: 1px solid dodgerblue;
        padding: 10px;
        background-color: dodgerblue;
        border-radius: 8px;
        font-size:20px;
    }

    .inputBox{
        position: relative;
    }

    .inputUser{
        background:none;
        border:none;
        border-bottom: 1px solid white;
        color:white;
        outline: none;
        font-size: 15px;
        width: 100%;
        letter-spacing:1px;
    }
    .labelInput{
        position: absolute;
        top:0px;
        left: 0px;
        pointer-events: none;
        transition: .5s;
    }
    .inputUser:focus ~ .labelInput,
    .inputUser:valid ~ .labelInput{
        top: -20px;
        font-size: 12px;
        color: dodgerblue;
    }
    .select{
        border: none;
        padding: 8px;
        border-radius: 10px;
        outline: none;
    }
    .inputUserdescr{
        background: white;
        border: color: white;
        border-radius: 10px;
        outline: none;
        resize: none;
        font-size: 15px;
        letter-spacing:1px;
    }
    #submit{
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
    #submit:hover{
        background-image: linear-gradient(to right,deepskyblue,deepskyblue);
    }
    

</style>

<header class="header_nav">
        <?php include('nav.php') ?>
</header>



<body>
    <div class="box">
        <form method='POST' enctype="multipart/form-data">
            <fieldset>
                <legend><b>Add Demonstrators</b></legend>
                <br><br>
                <div id="imageContainer">
                    <div class="imageSet">
                        <label for="image">Imagens:</label>
                        <input type="file" name="image[]" accept="image/*, .jpg, .jpeg, .png" multiple>
                        <br><br>
                        <input type="text" name="imageLabel[]" class="inputUser" placeholder="Label da Imagem">
                        <br><br>
                        <label for="descr">Descrição da Imagem:</label>
                        <textarea name="imageDescr[]" class="inputUserdescr" rows="4" required></textarea>
                        <br><br>
                        <button type="button" class="removeImageBtn">Remover Imagem</button>
                        <br><br>
                    </div>
                </div>
                <button type="button" class="addImageBtn">Adicionar Mais Imagens</button>
                <br><br>
                <button type="submit" name="submit">Cadastrar</button>
            </fieldset>
        </form>
    </div>  
    
    <script>
document.addEventListener("DOMContentLoaded", function() {
    const addImageBtn = document.querySelector(".addImageBtn");
    const imageContainer = document.querySelector("#imageContainer");
    let imageCount = 1;

    addImageBtn.addEventListener("click", function() {
        if (imageCount < 5) {
            const newImageSet = createImageSet();
            imageContainer.appendChild(newImageSet);
            imageCount++;
        } else {
            alert("Você atingiu o número máximo de imagens 5.");
        }
    });

    function createImageSet() {
        const imageSet = document.createElement("div");
        imageSet.classList.add("imageSet");

        const label = document.createElement("label");
        label.htmlFor = "image";
        label.textContent = "Imagens:";
        imageSet.appendChild(label);

        const imageInput = document.createElement("input");
        imageInput.type = "file";
        imageInput.name = "image[]";
        imageInput.accept = "image/*";
        imageSet.appendChild(imageInput);
        imageSet.appendChild(document.createElement("br"));

        const labelInput = document.createElement("input");
        labelInput.type = "text";
        labelInput.name = "imageLabel[]";
        labelInput.classList.add("inputUser");
        labelInput.placeholder = "Label da Imagem";
        imageSet.appendChild(labelInput);
        imageSet.appendChild(document.createElement("br"));

        const descrLabel = document.createElement("label");
        descrLabel.htmlFor = "descr";
        descrLabel.textContent = "Descrição da Imagem:";
        imageSet.appendChild(descrLabel);

        const descrTextarea = document.createElement("textarea");
        descrTextarea.name = "imageDescr[]";
        descrTextarea.classList.add("inputUserdescr");
        descrTextarea.rows = "4";
        descrTextarea.required = true;
        imageSet.appendChild(descrTextarea);
        imageSet.appendChild(document.createElement("br"));

        const removeImageBtn = document.createElement("button");
        removeImageBtn.type = "button";
        removeImageBtn.textContent = "Remover Imagem";
        removeImageBtn.classList.add("removeImageBtn");
        removeImageBtn.addEventListener("click", function() {
            imageContainer.removeChild(imageSet);
            imageCount--;
        });
        imageSet.appendChild(removeImageBtn);

        return imageSet;
    }
});
</script>


</body>

<?php include('footer.php') ?>

</html>
