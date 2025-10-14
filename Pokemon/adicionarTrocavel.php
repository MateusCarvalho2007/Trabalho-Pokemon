<?php
session_start();
if (!isset($_SESSION['idTreinador']) || !isset($_POST['idPokemon'])) {
    header("location:index.php");
    exit;
}
require_once __DIR__ . "/vendor/autoload.php";
$Pokemon = pokemon::find($_POST['idPokemon']);


if ($Pokemon) {
    $Trocavel = new pokemonTrocavel($_SESSION['idTreinador'], $Pokemon->getidPokemon());
    $Trocavel->save();
}
header("location:restrita.php");
exit;