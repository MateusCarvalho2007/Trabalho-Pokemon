<?php
session_start();
if(!isset($_SESSION['idTreinador'])){
    header("location:index.php");
}
if(isset($_GET['id'])){
    require_once __DIR__."/classes/pokemon.php";
    $pokemon = Pokemon::find($_GET['id']);
}
if(isset($_POST['botao'])){
    require_once __DIR__."/classes/pokemon.php";
    $pokemon = new Pokemon($_POST['nome'],$_POST['tipo'],$_POST['descricao']);
    $pokemon->setIdPokemon($_POST['id']);
    $pokemon->save();
    header("location: restrita.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edita Contato</title>
</head>
<body>
    <form action='formEditPokemon.php' method='POST'>
        <?php
            echo "Nome: <input name='nome' value='{$pokemon->getNome()}' type='text' required>";
        ?>
        <br>
        <?php
            echo "Tipo: <input name='tipo' value='{$pokemon->getTipo()}' type='text' required>";
        ?>
        <br>
        <?php
            echo "Descrição: <input name='descricao' value='{$pokemon->getDescricao()}' type='text' required>";
            echo "<input name='id' value='{$pokemon->getIdPokemon()}' type='hidden'>";
        ?>
        <br>
        <input type='submit' name='botao'>
    </form>
    <a href='restrita.php'>Voltar</a> | 
    <a href='sair.php'>Sair</a>
</body>
</html>

