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
                $currentSituation = $user_data['sit_project_id'];
            }
        } else {
            header('Location: sistema.php');
            exit();
        }
    } else {
        header('Location: sistema.php');
        exit();
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Project | SC2C</title>
        <script>
        function confirmAndSubmit() {
            var confirmed = confirm("Tem certeza que deseja salvar as alterações?");
            if (confirmed) {
                var confirmInput = document.createElement("input");
                confirmInput.type = "hidden";
                confirmInput.name = "confirm";
                confirmInput.value = "true";
                document.getElementById("updateForm").appendChild(confirmInput);
                return true; // O formulário será enviado
            } else {
                return false; // O formulário não será enviado
            }
        }
    </script>
</head>

<header class='header_nav'>
        <?php include('nav.php') ?>
</header>

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
    #update{
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
    #update:hover{
        background-image: linear-gradient(to right,deepskyblue,deepskyblue);
    }

</style>


<body>
    <div class="box">
        <form method='POST' action='saveEditProj.php' onsubmit="return confirmAndSubmit()">            <fieldset>
                <legend><b>Editar Project</b></legend>
                <br>
                <div class="inputBox"> 
                    <input type="text" name="pName_Project" id='pName_Project' class='inputUser' value="<?php echo $name ?>" required>
                    <label for="pName_Project" class="labelInput">pName Project</label> 
                </div>
                <br>
                <label for="category">Linha de pesquisa</label> 
                <br>
                <select name="category" class='select'>
                    <option value="">Selecionar</option>
                    <?php
                        $Category = mysqli_query($link, "SELECT * FROM Category");
                        while ($c = mysqli_fetch_array($Category)){
                            echo '<option value="' . $c['idCat'] . '" ' . ($category == $c['idCat'] ? 'selected' : '') . '>' . $c['Category'] . '</option>';
                        }
                    ?>
                </select>
                <br><br>
                <label for="TRL">TRL</label>
                <br>
                <select name="TRL" class='select'>
                    <option value="">Selecionar</option>
                    <?php
                    $TRLQuery = mysqli_query($link, "SELECT * FROM TRL");
                    while ($d = mysqli_fetch_array($TRLQuery)) {
                        echo '<option value="' . $d['idTRL'] . '" ' . ($TRL == $d['idTRL'] ? 'selected' : '') . '>' . $d['TRL'] . '</option>';
                    }
                    ?>
                </select>
                <br><br>
                <?php
                    if ($logado) {
                        if ($tipoUsuario === 'usuario') {
                            echo "<label for='Laboratory'>Laboratory</label>";
                            echo "<br>";
                            echo "<select name='Laboratory' class='select'>";
                            echo "<option value=''>Selecionar</option>";

                            $LaboratoryQuery = mysqli_query($link, "SELECT * FROM Laboratory");
                            while ($l = mysqli_fetch_array($LaboratoryQuery)) {
                                echo '<option value="' . $l['idLab'] . '" ' . ($Laboratory == $l['idLab'] ? 'selected' : '') . '>' . $l['abb'] . '</option>';
                            }
                            echo "</select>";
                        }
                    }
                    ?>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="link_ref" id='link_ref' class='inputUser' value="<?php echo $link_ref; ?>" required>
                    <label for="link_ref" class="labelInput">Link de referência</label> 
                </div>
                <br>
                <div class="inputBox"> 
                    <input type="text" name="resp" id='resp' class='inputUser' value="<?php echo $resp; ?>" required>
                    <label for="resp" class="labelInput">pName do responsável do Project</label> 
                </div>
                <br>
                <div class="inputBox"> 
                    <input type="text" name="email_resp" id='email_resp' class='inputUser' value="<?php echo $email_resp; ?>" required>
                    <label for="remail_respesp" class="labelInput">Email de contato do responsável</label> 
                </div>
                <br>
                <div class="inputBox">
                    <label for="descr">descrrição do Project</label>
                    <br> 
                    <textarea id="descr" name="descr" rows="5" cols="40" class='inputUserdescr'><?php echo $descr; ?></textarea>
                </div>
                <br>
                <input type="hidden" name="id" value="<?php echo $id?>">
                <?php
                    if (in_array($currentSituation, [1, 2, 4])){
                    echo    "<label for='situation'>Situation</label>";
                    echo    "<br>";
                    echo    "<select name='situation' class='select'>"; 
                        $situations = mysqli_query($link, "SELECT * FROM Situation WHERE id IN (4, 2) ORDER BY id");
                        while ($sit = mysqli_fetch_array($situations)) {
                            echo '<option value="' . $sit['id'] . '" ' . ($currentSituation == $sit['id'] ? 'selected' : '') . '>' . $sit['sName'] . '</option>';
                        }
                    echo    "</select>";
                            }
                    echo "<br>";
                ?>
                <br>
                <button type='submit' name="update" id='update'>Salvar</button>
            </fieldset>
        </form>
    </div>   
</body>

<script>
    function confirmAndSubmit() {
        var confirmed = confirm("Tem certeza que deseja salvar as alterações?");
        if (confirmed) {
            return true; 
        } else {
            return false; 
        }
    }
</script>

<?php include('footer.php')?>


</html>