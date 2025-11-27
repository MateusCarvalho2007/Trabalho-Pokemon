<!-- 
<?php
// require_once _DIR_ . "/classes/Documento.php";

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $idEstagio = intval($_POST['idEstagio']);
//     $nome = trim($_POST['nome']);
//     $tipo = trim($_POST['tipo']);
//     $prazo = $_POST['prazo'];

//     if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === 0) {
//         $nomeArquivo = basename($_FILES['arquivo']['name']);
//         $ext = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
//         $destino = _DIR_ . "/uploads/" . $nomeArquivo;
//         if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $destino)) {
//             $documento = new Documento($idEstagio, $nome, $tipo, $nomeArquivo, Documento::STATUS_ENVIADO, $prazo);
//             if ($documento->salvar()) {
//                 echo "Documento anexado com sucesso!";
//             } else {
//                 echo "Erro ao salvar no banco.";
//             }
//         } else {
//             echo "Erro ao mover o arquivo.";
//         }
//     } else {
//         echo "Nenhum arquivo enviado.";
//     }
// }
// $idEstagio = isset($_GET['idEstagio']) ? intval($_GET['idEstagio']) : 1; // exemplo fixo
// $documentos = Documento::listarPorEstagio($idEstagio);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="formCadDocumento.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="idEstagio" value="1">
    <label>Nome do Documento:</label><br>
    <input type="text" name="nome" required><br><br>

    <label>Tipo:</label><br>
    <input type="text" name="tipo" required><br><br>

    <label>Prazo:</label><br>
    <input type="date" name="prazo"><br><br>

    <label>Arquivo PDF:</label><br>
    <input type="file" name="arquivo" accept="application/pdf" required><br><br>

    <button type="submit">Enviar</button>
    
<h2>Lista de Documentos do Estágio</h2>

<?php //if (empty($documentos)): ?>
    <p>Nenhum documento encontrado.</p>
<?php //else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th>Nome</th>
            <th>Tipo</th>
            <th>Status</th>
            <th>Data Envio</th>
            <th>Prazo</th>
            <th>Arquivo</th>
        </tr>

        <?php// foreach ($documentos as $doc): ?>
    </table>

</form>
</body>
</html> -->