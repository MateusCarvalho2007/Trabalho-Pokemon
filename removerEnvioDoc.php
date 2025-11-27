<?php
session_start();
require_once __DIR__ . "/classes/Documento.php";
require_once __DIR__ . "/classes/Estagio.php";

if (!isset($_GET['idDocumento']) || !isset($_GET['idEstagio'])) {
    die("Parâmetros inválidos.");
}

$idDocumento = intval($_GET['idDocumento']);
$idEstagio = intval($_GET['idEstagio']);

// Buscar o documento
$documento = Documento::find($idDocumento);
if (!$documento) {
    die("Documento não encontrado.");
}

// Remover o arquivo físico do servidor (se existir)
$arquivo = $documento->getArquivo();
if ($arquivo) {
    $caminhoArquivo = __DIR__ . "/uploads/" . $arquivo;
    if (file_exists($caminhoArquivo)) {
        unlink($caminhoArquivo);
    }
}

// Calcular novo status baseado no prazo
$prazo = $documento->getPrazo();
$novoStatus = Documento::STATUS_PENDENTE; // padrão

if (!empty($prazo)) {
    $prazoTs = strtotime($prazo);
    $nowTs = time();
    if ($prazoTs < $nowTs) {
        $novoStatus = Documento::STATUS_ATRASADO; // Prazo passou sem envio
    }
}

// Atualizar o documento
$documento->setArquivo(null);
$documento->setDataEnvio(null);
$documento->setStatus($novoStatus);

if ($documento->update()) {
    header("Location: listagemDoc.php?idEstagio=" . $idEstagio);
    exit;
} else {
    die("Erro ao remover envio do documento.");
}
?>