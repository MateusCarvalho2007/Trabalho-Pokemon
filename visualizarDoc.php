<?php
require_once _DIR_ . "/../bd/MySQL.php";
require_once _DIR_ . "/../classes/Documento.php";

if (!isset($_GET["idDocumento"])) {
    die("Documento inválido.");
}

$idDocumento = (int) $_GET["idDocumento"];

$doc = Documento::find($idDocumento);
if (!$doc) {
    die("Documento não encontrado.");
}

$conn = new MySQL();
$sql = "SELECT * FROM comentario_documento WHERE idDocumento = {$idDocumento} ORDER BY dataHora ASC";
$comentarios = $conn->consulta($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Visualizar Documento</title>
</head>

<body>

<h2>Visualizar Documento</h2>

<p><strong>Título:</strong> <?= $doc->getTitulo(); ?></p>
<p><strong>Descrição:</strong> <?= $doc->getDescricao(); ?></p>
<p><strong>Prazo:</strong> <?= $doc->getPrazo() ? date('d/m/Y', strtotime($doc->getPrazo())) : '--'; ?></p>
<?php
$statusText = [
    0 => 'Em Análise',
    1 => 'Entregue',
    2 => 'Concluído',
    3 => 'Atrasado'
];
$arquivo = $doc->getArquivo();
$prazo = $doc->getPrazo();
$dataEnvio = $doc->getDataEnvio();
$desiredStatus = intval($doc->getStatus());
if (empty($arquivo)) {
    if (!empty($prazo) && strtotime($prazo) < time()) {
        $desiredStatus = 3; // Atrasado
    } else {
        $desiredStatus = 0; // Em Análise
    }
} else {
    if (!empty($dataEnvio)) {
        $desiredStatus = 1; // Entregue
    } else if (!empty($prazo) && strtotime($prazo) < time()) {
        $desiredStatus = 3; // Atrasado
    } else {
        $desiredStatus = 1; // Entregue
    }
}
?>
<p><strong>Status:</strong> <?= $statusText[$desiredStatus] ?? 'Desconhecido'; ?></p>

<?php if ($doc->getArquivo() != ""): ?>
    <a href="<?= $doc->getArquivo(); ?>" target="_blank">Visualizar PDF</a>
<?php else: ?>
    <p>Documento ainda não enviado.</p>
<?php endif; ?>

<hr>

<h3>Comentários</h3>

<?php if (count($comentarios) == 0): ?>
    <p>Nenhum comentário ainda.</p>
<?php else: ?>
    <?php foreach ($comentarios as $c): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <p><?= $c["comentario"]; ?></p>
            <p><small><?= !empty($c["dataHora"]) ? date('d/m/Y H:i', strtotime($c["dataHora"])) : ''; ?></small></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<hr>

<h3>Adicionar Comentário</h3>

<form action="comentar.php" method="POST">
    <input type="hidden" name="idDocumento" value="<?= $doc->getIdDocumento(); ?>">
    <textarea name="comentario" rows="4" cols="40"></textarea><br><br>
    <button type="submit">Enviar</button>
</form>

<hr>

<a href="listagem.php">Voltar</a>

</body>
</html>