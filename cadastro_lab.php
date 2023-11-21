<?php
include('loginCheck.php');
include('DBconnect.php');

$senhas_coincidem = true;

if (isset($_GET['tempKey'])) {
    $tempKey = $_GET['tempKey'];

    $query = mysqli_prepare($link, "SELECT * FROM Laboratory WHERE tempKey = ?");
    mysqli_stmt_bind_param($query, "s", $tempKey);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);

    if (mysqli_num_rows($result) === 1) {
        $user_data = mysqli_fetch_assoc($result);
        $id = $user_data['idLab'];
        $lName = $user_data['lName'];
        $abb = $user_data['abb'];
        $email = $user_data['email'];
        $link_site = $user_data['link_site'];
        $descr = $user_data['descr'];
        $telefone = $user_data['telefone'];
        $address = $user_data['address'];
        $cep = $user_data['cep'];
        $coord = $user_data['coord'];

        if (isset($_POST['update'])) {
            $confirmar_senha = isset($_POST['confirmar_senha']) ? $_POST['confirmar_senha'] : '';
            $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
        
            if ($senha !== $confirmar_senha) {
                $senhas_coincidem = false;
            } else {
                $senhahash = password_hash($senha, PASSWORD_DEFAULT);
        
                $updateQuery = mysqli_prepare($link, "UPDATE Laboratory SET lName=?, abb=?, descr=?, link_site=?, email=?, telefone=?, address=?, cep=?, coord=?, senha_hash=? WHERE id=?");
                mysqli_stmt_bind_param($updateQuery, "sssssssssssi", $lName, $abb, $descr, $link_site, $email, $telefone, $address, $cep, $coord, $senhahash, $id);
                $updateResult = mysqli_stmt_execute($updateQuery);
        
                if ($updateResult) {
                    $updatetempKeyQuery = mysqli_prepare($link, "UPDATE Laboratory SET tempKey = NULL, sit_usuario_id = 2 WHERE id = ?");
                    mysqli_stmt_bind_param($updatetempKeyQuery, "i", $id);
                    $updatetempKeyResult = mysqli_stmt_execute($updatetempKeyQuery);
        
                    if ($updatetempKeyResult) {
                        echo "<script>alert('DONE - tempKey and sit_usuario_id updated')</script>";
                    } else {
                        echo "<script>alert('ERROR - Failed to update tempKey and sit_usuario_id')</script>";
                        echo "Error: " . mysqli_error($link);
                    }
                    mysqli_stmt_close($updatetempKeyQuery);
                } else {
                    echo "<script>alert('ERROR - Update query failed')</script>";
                    echo "Error: " . mysqli_error($link);
                }
                mysqli_stmt_close($updateQuery);

            }
        }
    } else {
        echo "Convite inválido!";
        exit;
    }
} else {
    echo "Página não acessível diretamente!";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Laboratório| SC2C</title>

    <script>
        function confirmAndSubmit() {
            var confirmed = confirm("Salvar o pefil do Laboratório?");
            if (confirmed) {
                var confirmInput = document.createElement("input");
                confirmInput.type = "hidden";
                confirmInput.name = "confirm";
                confirmInput.value = "true";
                document.getElementById("updateForm").appendChild(confirmInput);
                return true;
            } else {
                return false;
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
        outline: none;
        resize: none;
        font-size: 15px;
        letter-spacing:1px;
        width: 100%;
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

<body>
    <div class="box">
    <form method='POST' action='saveLab.php' id='updateForm' onsubmit="return confirmAndSubmit();" enctype="multipart/form-data">
            <fieldset>
                <legend><b>Cadastrar Laboratório</b></legend>
                <br><br>
                <div class="inputBox"> 
                    <input type="text" name="lName_lab" id='lName_lab' class="inputUser" value="<?php echo $lName ?>" required>
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

                <font size="5"><b>Definir a senha</b></font>
                <br><br>
                <div class="inputBox"> 
                    <input type="password" name="senha" id="senha" class="inputUser" required>
                    <label class="labelInput">Senha</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="password" name="confirmar_senha" id="confirmar_senha" class="inputUser" required>
                    <label class="labelInput">Confirmar Senha</label> 
                </div>
                <br>
                <font size="5"><b>Add the Laboratory Logo</b></font> (You can do it later)
                <br><br>
                <input type="file" name="logo" id="logo" accept="image/*">
                <br><br>
                <input  type="hidden" name="id" value="<?php echo $id?>">
                <button class= 'save-submit' type='submit' name="update" id='update'>Salvar</button>
            </fieldset>
        </form>
    </div>
</body>

<?php include('footer.php') ?>
</html>
