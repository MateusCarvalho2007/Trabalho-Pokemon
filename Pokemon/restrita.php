<?php
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:loginForm.php");
}
if(isset($_GET['idVaga'])){
    require_once __DIR__."/classes/Vaga.php";
    Vaga::mudaStatus($_GET['idVaga']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mural de vagas de estágio</title>
</head>
<body>
    <a href='sair.php'>Sair</a>  
</body>
</html>