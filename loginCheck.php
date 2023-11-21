<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['senha']) || !isset($_SESSION['tipo_usuario'])) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    unset($_SESSION['tipo_usuario']);
    $logado = false;
    $tipoUsuario = null;
    
} else {
    $logado = true;
    $tipoUsuario = $_SESSION['tipo_usuario'];
}

if ($logado) {
   //echo "Usuário logado: " . $_SESSION['email'];
} else {
    //echo "Usuário não logado.";
}
?>
