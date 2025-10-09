<?php
session_start();
if(!isset($_SESSION['idTreinador'])){
    header("location:index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="styleRestrita.css">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha equipe</title>
</head>
<body>
    <div class="container">
        <table>
            <br>
            <tr><h1>Não Trocar</h1></tr>
            <br>
        </table>
        <a href='restrita.php'>Voltar</a>
    </div>
</body>
</html>