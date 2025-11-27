<?php
// marca um estágio como finalizado (soft delete)
session_start();
if(!isset($_SESSION['idUsuario'])){
    header('Location: cadastro.php');
    exit;
}

if(!isset($_GET['idEstagio'])){
    header('Location: listagem.php');
    exit;
}

require_once __DIR__ . '/classes/Estagio.php';

$id = intval($_GET['idEstagio']);
// verifica existência
try{
    $estagio = Estagio::find($id);
} catch(Exception $e){
    header('Location: listagem.php');
    exit;
}

if($estagio && !$estagio->isFinalizado()){
    Estagio::finalizar($id);
}

header('Location: listagem.php');
exit;
