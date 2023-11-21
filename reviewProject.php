<?php
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'lib/vendor/autoload.php';
    include('loginCheck.php');
    include('DBconnect.php');

    if ($tipoUsuario != 'usuario') {
        echo header('Location: login.php');
        exit();
    }
    
    if (!empty($_GET['id'])) {
        $id = $_GET['id'];
        $sqlSelect = "SELECT * FROM Project WHERE idProj=$id";
        $result = $link->query($sqlSelect);
        if ($result->num_rows > 0) {
            while ($project_data = mysqli_fetch_assoc($result)) {
                $name = $project_data['pName'];
                $category = $project_data['id_category'];
                $TRL = $project_data['id_TRL'];
                $Laboratory = $project_data['id_Laboratory'];
                $link_ref = $project_data['link_ref'];
                $resp = $project_data['resp'];
                $email_resp = $project_data['email'];
                $descr = $project_data['descr'];
                $date_in = $project_data['date_in'];


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
                    $lababb = $labRow['abb'];
                    $lablogo = $labRow['logo_path'];
                    $mailLab = $labRow['email'];

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

if (isset($_POST['submitR'])) {
    $report = $_POST['report'];

    $labName = $labRow['lName'];
    $lababb = $labRow['abb'];
    $lablogo = $labRow['logo_path'];
    $mailLab = $labRow['email'];

    $updateSitProjectId = mysqli_query($link, "UPDATE Project SET sit_project_id = 5 WHERE idProj = $id");
    if ($updateSitProjectId) {
        $successReport = "Report sent successfully!";
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = 'b5ad514f1a13a1';
            $mail->Password = '4209a152752acd';

            $mail->setFrom('sc2c@sc2c.com', 'SC2C');
            $mail->addAddress($mailLab, $lababb);
            $mail->isHTML(true);
            $mail->Subject = 'Project review';
            $mail->Body = $lababb . ", revisamos o projeto " . $name . " inserido em " . $date_in . " e foi constatado que a(s) seguinte(s) alteração(ões):<br><br>" . $report;
            $mail->AltBody = $lababb . ", revisamos o projeto " . $name . " inserido em " . $date_in . " e foi constatado que a(s) seguinte(s) alteração(ões):\n\n" . $report;
            $mail->send();
            $successLab .= " Report sent successfully!";

            header("Location: approveProjects.php.php");
            exit();
        } catch (Exception $e) {
            $errorReport = "Error to report project: " . $mail->ErrorInfo;
        }
    } else {
        $errorReport = "Error to report project.";
    }
}


if (isset($_POST['submitA'])) {

    $labName = $labRow['lName'];
    $lababb = $labRow['abb'];
    $lablogo = $labRow['logo_path'];
    $mailLab = $labRow['email'];

    $updateSitProjectId = mysqli_query($link, "UPDATE Project SET sit_project_id = 2 WHERE idProj = $id");
    if ($updateSitProjectId) {
        $successLab = "Approval sent successfully!";
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = 'b5ad514f1a13a1';
            $mail->Password = '4209a152752acd';

            $mail->setFrom('sc2c@sc2c.com', 'SC2C');
            $mail->addAddress($mailLab, $lababb);
            $mail->isHTML(true);
            $mail->Subject = 'Project review';
            $mail->Body = $lababb . ", revisamos o projeto " . $name . " inserido em " . $date_in . " e já está publicado";
            $mail->AltBody = $lababb . ", revisamos o projeto " . $name . " inserido em " . $date_in . " e e já está publicado";
            $mail->send();
            $successLab .= " Approval sent successfully!";

            header("Location: approveProjects.php.php");
            exit();
        } catch (Exception $e) {
            $errorReport = "Error to approve project: " . $mail->ErrorInfo;
        }
    } else {
        $errorReport = "Error to approve project.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>review Project | SC2C</title>
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
        margin-bottom:2%;

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
                    <label for="category">Linha de pesquisa:  </label> 
                    <div class='readonly-field'><?php echo $categoryName; ?></div>
                    <br><br>
                    <label for="Description">Project Descripition:</label>
                    <br>
                    <div class='description-box'><?php echo $descr; ?></div>
                    <br><br>
                </div>                       
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
        <div class= "content-align">
        <div class="report-container">
            <form method='POST'>
                        <div class="name-box"><b>Report Project</b></div>
                        <br>
                            <label class="legend-container">Report Descriptrion</label>
                            <br> 
                            <textarea id="report" name="report" rows="5" cols="40" class="inputUserDesc" required></textarea>
                            <br><br>
                            <button type='submit' class='save-submit' name="submitR" id='submitR'>Report</button>
                        </div>
            </form>
                </div>

        <div class="report-container">
            <form method='POST'>
                        <div class="name-box"><b>Approve Project</b></div>
                            <br>
                            <button type='submit' class='save-submit-ap' name="submitA" id='submitA'>Approve</button>
                        </div>
            </form>
                </div>
        </div>
</body>

<?php include('footer.php') ?>

</html>
