<?php
include('loginCheck1.php');
include('DBconnect.php');

if (!empty($_SESSION['idLab'])) {
    $id = $_SESSION['idLab'];
    $sqlSelect = "SELECT * FROM Laboratory WHERE idLab=$id";
    $result = $link->query($sqlSelect);

    if ($result->num_rows > 0) {
        $lab_data = mysqli_fetch_assoc($result);

        $lName = $lab_data['lName'];
        $abb = $lab_data['abb'];
        $email = $lab_data['email'];
        $link_site = $lab_data['link_site'];
        $descr = $lab_data['descr'];
        $telefone = $lab_data['telefone'];
        $address = $lab_data['address'];
        $cep = $lab_data['cep'];
        $coord = $lab_data['coord'];
        $logo = $lab_data['logo_path'];
    } else {
        header('Location: sistemaLab.php');
        exit();
    }
} else {
    header('Location: sistemaLab.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Laboratório| SC2C</title>

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

<header class="header_nav">
        <?php include('nav.php') ?>
</header>

<style>

body{
        font-family: Arial, Helvetica, sans-serif;
        background-image: linear-gradient(90deg,cyan, white);
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

    .inputUserDesc{
        background: white;
        border: color: white;
        border-radius: 10px;
        width:100%;
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

    .logolab {
    background-color: white;
    max-width: 100%;
    height: auto;
    margin-right: 10px;
    border-radius:15px;
    width: 150px;
    padding: 10px;
    }
</style>
<body>
    <div class="box">
    <form method='POST' action='saveEditLab.php' id='updateForm' onsubmit="return confirmAndSubmit();" enctype="multipart/form-data">            <fieldset>
                <legend><b>Editar Laboratório</b></legend>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="lName" id='lName' class="inputUser" value="<?php echo $lName ?>" required>
                    <label class="labelInput">lName Laboratório</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="abb" id='abb' class="inputUser" value="<?php echo $abb ?>"required>
                    <label class="labelInput">abb do Laboratório</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="email" id='email' class="inputUser" value="<?php echo $email ?>" required>
                    <label class="labelInput">Email de contato</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="coord" id='coord' class="inputUser" value="<?php echo $coord ?>" required>
                    <label class="labelInput">coord do Laboratório</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="link_site" id='link_site' class="inputUser" value="<?php echo $link_site ?>" required>
                    <label class="labelInput">Link do site </label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="telefone" id='telefone' class="inputUser" value="<?php echo $telefone ?>"required>
                    <label class="labelInput">Telefone de contato</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="address" id='address' class="inputUser" value="<?php echo $address ?>" required>
                    <label class="labelInput">Endereço</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="int" name="cep" id='cep' class="inputUser" value="<?php echo $cep ?>" required>
                    <label class="labelInput">CEP</label> 
                </div>
                <br><br>
                <div class="inputBox">
                    <label>Descrião do Laboratório</label>
                    <br> 
                    <textarea id="descr" name="descr" rows="5" cols="40" class="inputUserDesc" required><?php echo $descr ?></textarea>
                </div>
                <br>
                <font size="5"><b>Alter Laboratorie's Logo</b></font>
                <br><br>
                <b>Logo Atual:</b>
                <br><br>
                <img class="logolab" src="dbimages/Logos/<?php echo $logo; ?>">
                <br><br>
                <b>Logo Nova:</b>
                <br><br>
                <input type="file" name="logo" id="logo" accept="image/*">
                <br><br>
                <input type="hidden" name="id" value="<?php echo $id?>">
                <button type='submit' name="update" id='update'>Salvar</button>
            </fieldset>
        </form>
    </div>   
</body>

<?php include('footer.php')?>

</html>
