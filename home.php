<?php
include('loginCheck.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site | SC2C </title>
</head>

<style>
    body{
        font-family: Arial, Helvetica,sans-serif;
        background: linear-gradient(to right,cyan,white);
        text-align: center;
        color: white;

    }
    .box{
        position: absolute;
        top:50%;
        left:50%;
        transform: translate(-50%,-50%);
        background-color: rgba(0,0,0,0.8);
        border-radius: 15px;
        padding: 30px;
    }
    .a_home{
        text-decoration: none;
        color: white;
        border: 3px solid dodgerblue;
        border-radius: 15px;
        padding: 10px;
    }
    .a_home:hover{
        background-color: dodgerblue;
    }

    .header_nav{
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 2%;
        top: 0;
        left: 0;
        right: 0;
        background-color: #24252a;
        height: 80px;

    }

</style>

<header class="header_nav">
        <?php include('nav.php') ?>
</header>

<body>
    <div class="box">
        <a class="a_home" href="sistema.php">Projects</a>
        <a class="a_home" href="sistemaLab.php">Laboratories</a>
    </div>
</body>
<?php include('footer.php') ?>

</html>