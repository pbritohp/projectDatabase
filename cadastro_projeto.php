<?php
include('loginCheck1.php');
include('DBconnect.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($logado) {
    if ($tipoUsuario === 'usuario') {
    } elseif ($tipoUsuario === 'Laboratory') {
        $queryLaboratory = "SELECT * FROM Laboratory WHERE idLab = '$tipoUsuario'";
    }
}

$queryCategory = "SELECT * FROM Category";
$queryTRL = "SELECT * FROM TRL";
$queryLaboratory = "SELECT * FROM Laboratory";

if ($logado) {
    if ($tipoUsuario === 'usuario') {
    } elseif ($tipoUsuario === 'Laboratory') {
        $queryLaboratory .= " WHERE idLab = '$tipoUsuario'";
    }
}

$resultCategory = mysqli_query($link, $queryCategory);
$resultTRL = mysqli_query($link, $queryTRL);
if (!empty($queryLaboratory)) {
    $resultLaboratory = mysqli_query($link, $queryLaboratory);
}

if(isset($_POST['submit'])){
    $name = $_POST['pName'];
    $category = $_POST['category'];
    $TRL = $_POST['TRL'];
    
    if ($tipoUsuario === 'Laboratory') {
        $Laboratory = $_SESSION['idLab'];
    } else {
        $Laboratory = $_POST['Laboratory'];
    }

    $link_ref = $_POST['link_ref'];
    $resp = $_POST['resp'];
    $email_resp = $_POST['email_resp'];
    $descr = $_POST['descr'];

    $query = "INSERT INTO Project (pName, id_category, id_TRL, id_Laboratory, descr, link_ref, email, resp, date_in) 
          VALUES ('$name', '$category', '$TRL','$Laboratory','$descr','$link_ref','$email_resp','$resp', NOW())";

    $result = mysqli_query($link, $query);
    if ($result) {
        $projectID = mysqli_insert_id($link);
        header("Location: edit_demo.php?id=$projectID");
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Project | SC2C</title>
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
    .save-submit{
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
    .save-submit:hover{
        background-image: linear-gradient(to right,deepskyblue,deepskyblue);
    }
    

</style>

<header class="header_nav">
        <?php include('nav.php') ?>
</header>


<body>
    <div class="box">
        <form method='POST'>
            <fieldset>
                <legend><b>Register Project</b></legend>
                <br>
                <div class="inputBox"> 
                    <input type="text" name="pName" id='pName' class='inputUser' required>
                    <label for="pName" class="labelInput">Project Name</label> 
                </div>
                <br>
                <label for="category">Reshearch Field</label> 
                <br>
                <select name="category" class='select'>
                <option value="">Select</option>
                    <?php
                        $Category = mysqli_query($link, "SELECT * FROM Category");
                        while ($c = mysqli_fetch_array($Category)){
                    ?>
                    <option value="<?php echo $c['idCat']?>"><?php echo $c['Category']?></option>
                    <?php } ?>
                </select>
                <br><br>
                <label for="TRL">TRL</label> 
                <br>
                <select name="TRL" class='select'>
                <option value="">Select</option>
                    <?php
                        $TRL = mysqli_query($link, "SELECT * FROM TRL");
                        while ($d = mysqli_fetch_array($TRL)){
                    ?>
                    <option value="<?php echo $d['idTRL']?>"><?php echo $d['TRL']?></option> 
                    <?php } ?>
                </select>
                <br><br>
                <?php
                    if ($logado) {
                        if ($tipoUsuario === 'usuario') {
                            echo "<label for='Laboratory'>Laboratory</label>";
                            echo "<br>";
                            echo "<select name='Laboratory' class='select'>";
                            echo "<option value=''>Select</option>";

                            $LaboratoryQuery = mysqli_query($link, "SELECT * FROM Laboratory");
                            while ($l = mysqli_fetch_array($LaboratoryQuery)) {
                                echo '<option value="' . $l['idLab'] . '" ' . ($Laboratory == $l['idLab'] ? 'selected' : '') . '>' . $l['abb'] . '</option>';
                            }
                            echo "</select>";
                            echo "<br>";
                        }
                    }
                    ?>
                    <br>
                <div class="inputBox"> 
                    <input type="text" name="link_ref" id='link_ref' class='inputUser' required>
                    <label for="link_ref" class="labelInput">Reference Link</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="resp" id='resp' class='inputUser' required>
                    <label for="resp" class="labelInput">Name of the Project Responsable</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="email_resp" id='email_resp' class='inputUser' required>
                    <label for="remail_respesp" class="labelInput">Responsable Email</label> 
                </div>
                <br>
                <div class="inputBox">
                    <label for="descr">Project Description</label>
                    <br> 
                    <textarea id="descr" name="descr" rows="5" cols="40" class='inputUserdescr'></textarea>
                </div>
                <br><br>
                <button class="save-submit" type="submit" name="submit">Register</button>
            </fieldset>
        </form>
    </div>  
</body>
<?php include('footer.php') ?>

</html>
