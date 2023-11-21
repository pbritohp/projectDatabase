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
            $category = $user_data['id_category'];
            $TRL = $user_data['id_TRL'];
            $Laboratory = $user_data['id_Laboratory'];
            $link_ref = $user_data['link_ref'];
            $resp = $user_data['resp'];
            $email_resp = $user_data['email'];
            $descr = $user_data['descr'];

            $categoryQuery = mysqli_query($link, "SELECT * FROM Category WHERE idCat=$category");
            if ($categoryRow = mysqli_fetch_assoc($categoryQuery)) {
                $categoryName = $categoryRow['Category'];
            }

            $trlQuery = mysqli_query($link, "SELECT * FROM TRL WHERE idTRL=$TRL");
            if ($trlRow = mysqli_fetch_assoc($trlQuery)) {
                $trlName = $trlRow['TRL'];
            }
            $labQuery = mysqli_query($link, "SELECT * FROM Laboratory WHERE idLab=$Laboratory");
            if ($labRow = mysqli_fetch_assoc($labQuery)) {
                $labName = $labRow['lName'];
            }
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submitRequest'])) {
        $cEmail = $_POST['cEmail'];
        $projectId = $id;

        $sqlInsert = "INSERT INTO ContactRequests (cEmail, projectId) VALUES ('$cEmail', $projectId)";
        if ($link->query($sqlInsert) === FALSE) {
            echo "Error: " . $sqlInsert . "<br>" . $link->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <title>View Project | SC2C</title>
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
        margin-top:2%;
        width: 80%;
        position: relative;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;
        margin-bottom:1%;

    }
    .info-container {
        color: white;
        margin-left:2%;
        margin-top:2%;
        width: 20%;
        position: relative;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;
        margin-bottom:1%;

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
        margin-left:2%;
        width: 80%;
        position: relative;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;
        margin-bottom:2%;
    }

    .image-container {
    display: flex;
    align-items: start;
    margin-bottom: 2%;
    }

    .image-description {
        display: flex;
        align-items: center;
    }

    img {
    max-width: 100%;
    height: auto;
    margin-right: 10px;
    border-radius:15px; 
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
    .report-container {
        color: white;
        margin-left:2%;
        position: relative;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;
        width:30%;
        margin-bottom:2%;
    }
    .label-box {
        border: 2px solid white;
        padding: 10px;
        background-color: black;
        border-radius: 8px;
        font-size: 20px;
        font-weight: bold;
    }
    fieldset{
        border: 3px solid red;
        width:100%
    }
    legend{
        border: 1px solid red;
        padding: 10px;
        background-color: red;
        border-radius: 8px;
        font-size:20px;
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
    }

    .save-submit{
        background-color: dodgerblue;
        width: 100%;
        transition: all 0.5s ease 0s;
        color:white;
        border: none;
        padding:15px;
        font-size:15px;
        cursor: pointer;
        border-radius: 10px;
        text-align: center;
        margin-bottom:10px;
    }
    .save-submit:hover{
        background-color: rgb(156, 52, 52);
        color: black;
    }

    .save-submit-ap{
        background-color: dodgerblue;
        width: 100%;
        transition: all 0.5s ease 0s;
        color:white;
        border: none;
        padding:15px;
        font-size:15px;
        cursor: pointer;
        border-radius: 10px;
        text-align: center;
        margin-bottom:10px;
    }
    .save-submit-ap:hover{
        background-color: green;
        color: black;
    }

    .content-align{
        align:space-between;
        
    }

    .modal{
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

        .subbuttom{
        background-color: dodgerblue;
        width: 100%;
        transition: all 0.5s ease 0s;
        color:white;
        border: none;
        padding:15px;
        font-size:15px;
        cursor: pointer;
        border-radius: 10px;
        text-align: center;
        margin-bottom:10px;
    }
    .subbuttom:hover{
        background-color: green;
        color: black;
    }
</style>


<body>
    <div class="project-container">
            <div class="legend-container">
                <div class="name-box"><b><?php echo $name ?></b></div>
                <div class="trl-box"><?php echo $trlName ?></div>
            </div>
            <br>
            <div class="contet-container">               
                <label for="resp">Project Responsable:</label>
                <br>
                <div class='readonly-field'><?php echo $resp; ?></div>
                <br><br>
                <label for="Laboratory">Laboratory:</label>
                <br>
                <div class='readonly-field'><?php echo $labName; ?></div>
                <br><br>
                <label for="category">Research Field:  </label> 
                <div class='readonly-field'><?php echo $categoryName; ?></div>
                <br><br>
                <label for="Description">Project Descripition:</label>
                <br>
                <div class='description-box'><?php echo $descr; ?></div>
                <br><br>
            </div>                       
    </div>

    <div class="info-container">
        <div class="legend-container">
            <div class="name-box"><b>Contact Info</b></div>
        </div>
        <form method="post" id="contactForm" onsubmit="return validateForm();">
        <h2><label for="cEmail">Enter your email:</label></h2>
            <input type="email" name="cEmail" id="cEmail" required>
            <br>
            <input type="hidden" name="projectId" value="<?php echo $id; ?>">
            <h3><label for="requestContact">Request contact info via email:</label></h3>
            <input type="checkbox" name="requestContact">
            <br>
            <button class ="subbuttom" type="submit" name="submitRequest">Request contact Info</button>
        </form>
    </div>


    <?php if (!empty($images)) : ?>
        
        <div class="demonstrator-container">
            <div class="legend-container">
                <div class="name-box"><b>Demosntrators</b></div>
            </div>
            <br>
                <?php foreach ($images as $image) : ?>
                    <div class="label-box"><?php echo $image['label']; ?></div>
                    <br>
                    <div class="image-container">
                        <br>
                        <img src="dbimages/Images/<?php echo $image['img_path']; ?>">
                        <div class= "description-box"><?php echo $image['Descr']; ?></div>
                        <br>
                    </div>
                <?php endforeach; ?>
    <?php endif; ?>
        </div>

        <div id="contactModal" class="modal" style="display:none ;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Contact Information</h2>
            <p><strong>Project Name:</strong> <?php echo $name; ?></p>
            <p><strong>Link:</strong> <?php echo $link_ref; ?></p>
            <p><strong>Email:</strong> <?php echo $email_resp; ?></p>
            <p><strong>Responsible:</strong> <?php echo $resp; ?></p>
        </div>
    </div>

    </div>
</div>

<script>
    function openModal() {
        var modal = document.getElementById('contactModal');
        modal.style.display = 'block';
    }

    function closeModal() {
        var modal = document.getElementById('contactModal');
        modal.style.display = 'none';
    }

    window.onclick = function (event) {
        var modal = document.getElementById('contactModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };

    function validateForm() {
    var email = document.getElementById('cEmail').value;
    if (email.trim() === '') {
        alert('Please enter your email before submitting.');
        return false;
    }
    return true;
}

</script>


<?php  include('footer.php')?>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function () {
    $('#contactForm').submit(function (e) {
        e.preventDefault();

        var formData = $(this).serialize();
        formData += '&projectId=' + <?php echo $id; ?>;

        console.log("Form Data:", formData);

            $.ajax({
            type: 'POST',
            url: 'process_request.php',
            data: {
                submitRequest: true,
                cEmail: $('#cEmail').val(),
                projectId: <?php echo $id; ?>,
            },
            success: function (response) {
                console.log("Response:", response);
                alert('Request submitted successfully!');
                openModal();
            },
            error: function (xhr, status, error) {
                console.error("Error:", xhr.responseText);
                alert('Error submitting request. Please try again.');
            }
        });
    });
});

</script>

</body>

</html>