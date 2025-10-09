<?php
session_start();
if(!isset($_SESSION['idTreinador'])){
    header("location:loginForm.php");
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
<a href='equipe.php'>Minha Equipe</a>      
<a href='sair.php'>Sair</a>  
</body>
</html>