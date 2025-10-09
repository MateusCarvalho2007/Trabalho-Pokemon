<?php
if(isset($_POST['botao'])){
    require_once __DIR__."/classes/pokemon.php";
    $p = new Pokemon($_POST['nome'], $_POST['tipo'], $_POST['descricao']);
    $p->save();
    header("location: restrita.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastra Pokémon</title>
</head>
<body>
    <form action='formCadPokemon.php' method='post'>
        <label for='nome'>Nome:</label>
        <input type='text' name='nome' id='nome' required>
        <label for='tipo'>Tipo:</label>
        <input type='tipo' name='tipo' id='tipo' required>
        <label for='descricao'>Descrição:</label>
        <input type='descricao' name='descricao' id='descricao' required>
        <input type='submit' name='botao' value='Cadastrar'>
    </form>
</body>
</html>

