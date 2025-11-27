<?php
require_once __DIR__ . "/classes/Documento.php";

if (!isset($_GET['idDocumento'])) {
    die("Documento inválido.");
}

$idEstagio = isset($_GET['idEstagio']) ? intval($_GET['idEstagio']) : 0;
require_once __DIR__ . '/classes/Estagio.php';

$estagio = Estagio::find($idEstagio);
$idDocumento = intval($_GET['idDocumento']);
$documento = Documento::find($idDocumento);

if (!$documento) {
    die("Documento não encontrado.");
}

// PROCESSAR POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $documento->setNome(trim($_POST['nome']));
    $documento->setPrazo($_POST['prazo']);
    $documento->setStatus($_POST['status']);

    if ($documento->update()) {
        header("Location: listagemDoc.php?idEstagio={$documento->getIdEstagio()}");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/cadastro.css">
    <title>Editar Documento</title>
</head>
<body>

<h1>Editar Documento</h1>

<form action="" method="POST" enctype="multipart/form-data">

    <label>Nome:</label><br>
    <input type="text" name="nome" value="<?= htmlspecialchars($documento->getNome()) ?>" required><br><br>

    <label>Prazo:</label><br>
    <input type="date" name="prazo" value="<?= htmlspecialchars($documento->getPrazo()) ?>"><br><br>

    <label>Status:</label><br>
    <select name="status">
        <option value="0" <?= $documento->getStatus()==0?'selected':'' ?>>Pendente</option>
        <option value="1" <?= $documento->getStatus()==1?'selected':'' ?>>Enviado</option>
        <option value="2" <?= $documento->getStatus()==2?'selected':'' ?>>Concluído</option>
        <option value="3" <?= $documento->getStatus()==3?'selected':'' ?>>Atrasado</option>
    </select>

    <button type="submit">Salvar</button>
    <a href="listagemDoc.php?idEstagio=<?php echo $idEstagio; ?>" style="background-color: #6c757d; color: #fff; padding: 0.75rem 1.5rem; font-weight: bold; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; transition: all 0.3s ease;">Voltar</a>

</form>

</body>
</html>
