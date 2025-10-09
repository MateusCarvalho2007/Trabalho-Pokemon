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
    <title>Pokémons Para Troca</title>
</head>
<body>
<a href='formCadPokemon.php'>Cadastrar Pokémon</a>    
<a href='NaoQueroTrocar.php'>Não quero Trocar</a>      
<a href='sair.php'>Sair</a>  
</body>
</html>