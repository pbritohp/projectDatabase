

<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap');

    * {
        box-sizing: border-box;
        margin: 0;
    }

    .body_nav{
        font-family: "Roboto", sans-serif;
        background-color: linear-gradient(45deg,cyan,white);
    }

    .li_nav,.a_nav, .button_nav{
        font-family: "Roboto", sans-serif;
        font-weight: 500;
        font-size: 16px;
        color: #edf0f1;

    }

    .header_nav{
        position: relative;
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

    .logo{
        cursor: pointer;

    }

    .nav_links{
        list-style: none;
    }

    .nav_links .li_nav{
        display: inline-block;
        padding: 0px 20px;
    }

    .nav_links .li_nav .a_nav{
        transition: all 0.5s ease 0s;
        text-decoration: none;
    }

    .nav_links .li_nav .a_nav:hover{
        color: dodgerblue;
        text-decoration: none;
    }

    .out{
        margin-left: 20px;
        padding: 9px 25px;
        background-color: dodgerblue;
        cursor: pointer;
        transition: all 0.5s ease 0s;
        border-radius: 50px;
        color: white;
        font-size: 16px;
        font-family: "Roboto", sans-serif;
    }
    .out:hover{
        background-color: rgb(156, 52, 52);
        color: black;
    }
</style>

<a href="https://sc2c.ufsc.br/">
    <img class="logo" src="images/logo-sc2c.png" alt="logo">
</a>
<nav class="body_nav">
    <ul class="nav_links">
        <?php
        if ($logado) {
            echo '<li class="li_nav"><a class="a_nav" href="sistema.php">Projects</a></li>';
            echo '<li class="li_nav"><a class="a_nav" href="sistemaLab.php">Laboratories</a></li>';
            if ($tipoUsuario === 'usuario') {
                echo '<li class="li_nav"><a class="a_nav" href="cadastro_usuario.php">New Admin</a></li>';
                echo '<li class="li_nav"><a class="a_nav" href="newLab.php">Invite Laboratory</a></li>';
                echo '<li class="li_nav"><a class="a_nav" href="approveProjects.php">Aprove Projects</a></li>';
            } elseif ($tipoUsuario === 'Laboratory') {
                echo '<li class="li_nav"><a class="a_nav" href="myProjects.php">My Projects</a></li>';
                echo '<li class="li_nav"><a class="a_nav" href="edit_laboratory.php">Laboratory Info</a></li>';

            }
            echo '<li class="li_nav"><a class="a_nav" href="cadastro_projeto.php">Register Project</a></li>';
        } else {
            echo '<li class="li_nav"><a class="a_nav" href="home.php">Home</a></li>';
            echo '<li class="li_nav"><a class="a_nav" href="Login.php">Login</a></li>';
        }
        ?>
    </ul>
</nav>
<?php
if ($logado) { 
    echo '<a class="a_nav" href="logout.php"><button class="out">Logout</button></a>';
}
?>

