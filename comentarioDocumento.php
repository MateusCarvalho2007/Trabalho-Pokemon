<?php
session_start();
require_once __DIR__ . "/bd/MySQL.php";
require_once __DIR__ . "/classes/Documento.php";

if ($_SESSION['tipo'] != "professor") {
    die("Apenas professores podem comentar.");
}

if (!isset($_GET["idDocumento"])) {
    die("Documento inválido.");
}

$idDocumento = (int) $_GET["idDocumento"];
$doc = Documento::find($idDocumento);

if (!$doc) {
    die("Documento não encontrado.");
}

$conn = new MySQL();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comentario = trim($_POST['comentario']);

    if ($comentario === "") {
        $erro = "Preencha o campo de comentário.";
    } else {
        $sql = "INSERT INTO comentario_documento (idDocumento, idProfessor, comentario)
                VALUES ($idDocumento, {$_SESSION['idUsuario']}, '$comentario')";
        $conn->executar($sql);

        header("Location: comentarioDocumento.php?idDocumento=$idDocumento");
        exit;
    }
}


$sql = "SELECT c.comentario, c.dataHora, u.nome
        FROM comentario_documento c 
        JOIN usuario u ON u.idUsuario = c.idProfessor
        WHERE c.idDocumento = $idDocumento
        ORDER BY c.dataHora ASC";

$comentarios = $conn->consulta($sql);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Comentários</title>
</head>
<body>

<h2>Comentários - <?= $doc->getTitulo() ?></h2>

<!-- Formulário para comentar -->
<form method="POST">
    <textarea name="comentario" rows="4" cols="50"></textarea><br><br>
    <button type="submit">Enviar Comentário</button>
</form>

<?php if (isset($erro)): ?>
    <p style="color:red;"><?= $erro ?></p>
<?php endif; ?>

<hr>

<h3>Comentários anteriores:</h3>

<?php if (count($comentarios) == 0): ?>
    <p>Nenhum comentário ainda.</p>
<?php else: ?>
    <?php foreach ($comentarios as $c): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <strong><?= $c["nome"] ?></strong>
            <span style="font-size: 12px; color:gray;">
                (<?= !empty($c["dataHora"]) ? date('d/m/Y H:i', strtotime($c["dataHora"])) : '' ?>)
            </span>
            <p><?= $c["comentario"] ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<br>
<a href="listagemDoc.php?idEstagio=<?= $doc->getIdEstagio() ?>">Voltar</a>

</body>
</html>