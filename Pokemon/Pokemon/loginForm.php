<?php
    if(isset($_POST['botao'])){
        require_once __DIR__."/classes/treinador.php";
        $treinador = new Treinador($_POST['nome'],$_POST['email'],$_POST['senha']);
        if($treinador->authenticate()){
            header("location: restrita.php");
        }else{
            header("location: index.php");
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Do Sistema de Trocas Pokémon</title>
</head>
<body>
    <form method='post' action='loginForm.php'>
        <label for='nome'> Nome:</label>
        <input type='nome' name='nome' id='nome' required>
        <label for='email'> E-mail:</label>
        <input type='email' name='email' id='email' required>
        <label for='senha'>Senha:</label>
        <input type='password' name='senha' id='senha' required>
        <input type='submit' name='botao' value="Acessar">
    </form>
</body>
</html>