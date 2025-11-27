<?php

session_start();
require_once __DIR__ . "/classes/Usuario.php";
require_once __DIR__ . "/classes/Estagio.php";
require_once __DIR__ . "/classes/Documento.php";

$estagio = null;
if(isset($_GET['idEstagio'])){
     $idEstagio = intval($_GET['idEstagio']);
     $estagio = Estagio::find($idEstagio);
     $documentos = Documento::findAll($idEstagio);
}

// Mapeamentos de texto e classes (compatível com listagemDoc.php)
$statusText = [
    0 => 'Em Análise',
    1 => 'Entregue',
    2 => 'Concluído',
    3 => 'Atrasado'
];

$statusClass = [
    0 => 'status-pendente',
    1 => 'status-enviado',
    2 => 'status-concluido',
    3 => 'status-atrasado'
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Documentos - AAGIS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #004aad, #007bff);
            min-height: 100vh;
            color: #333;
            line-height: 1.6;

        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
        }

        header {
            background-color: #fff;
            color: #333;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            
        }

        .header-content h1 {
            font-size: 1.8rem;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-logout {
            background-color: #dc3545;
            color: white;
            padding: 8px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
        }

        .page-title {
            margin: 20px 0;
            color: #fff;
            font-size: 2rem;
            text-align: center;
        }

        .estagio-info {
            background-color: white;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #ddd;
            
        }

        .estagio-info h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .estagio-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
            
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .detail-value {
            color: #333;
            font-weight: 500;
            font-size: 1rem;
        }

        .actions-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-secondary:hover{
            background-color: #6c757d;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
            color: #fff;

        }
        

        .documents-container {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .documents-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            background-color: #f8f9fa;
            color: #333;
            padding: 10px 6px;
            font-weight: 600;
            border-bottom: 1px solid #ddd;
            border-radius: 10px;
        }

        .document-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            padding: 10px 6px;
            border-bottom: 1px solid #ddd;
            align-items: center;
            
        }

        .document-item:last-child {
            border-bottom: none;
        }

        .document-name {
            font-weight: 500;
            color: #333;
        }

        .status-badge {
            padding: 4px 8px;
            font-size: 0.8rem;
            font-weight: 500;
            text-align: center;
            display: inline-block;
        }

        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-enviado {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-concluido {
            background-color: #d4edda;
            color: #155724;
        }

        .status-atrasado {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-nao-enviado {
            background-color: #f0f0f0; /* cinza claro */
            color: #6c757d;
        }

        .document-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 6px 12px;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .btn-view {
            color: #007bff;
        }

        .btn-upload {
            color: #007bff;
        }

        .btn-remove {
            color: #dc3545;
        }

        .btn-remove:hover {
            text-decoration: underline;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #adb5bd;
        }

        @media (max-width: 768px) {
            .documents-header, .document-item {
                grid-template-columns: 1fr;
                gap: 10px;
                text-align: center;
            }
            
            .document-actions {
                justify-content: center;
            }
            
            .estagio-details {
                grid-template-columns: 1fr;
            }
            
            .header-content {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Lista de Documentos</h1>
            Bem-vindo, <?= $_SESSION['tipo'] ?> <?= $_SESSION['nome'] ?>, ao AAGIS!
            <div class="user-info">
            <a href="perguntas.php" style="color: #007bff; text-decoration: none; font-weight: 600;">Perguntas Frequentes</a>
                <a href="logout.php" class="btn-logout">Sair</a>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">Listagem de Documentos (<?php echo $estagio->getName() ?> - <?php echo $estagio->getEmpresa() ?>)</h1>
        
        <div class="documents-container">
            <div class="documents-header">
                <div>Documento</div>
                <div>Status</div>
                <div>Data de Envio</div>
                <div>Prazo</div>
                <div>Ações</div>
            </div>
            
            <?php if (empty($documentos)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📄</div>
                    <h3>Nenhum documento encontrado</h3>
                </div>
            <?php else: ?>
                <?php foreach ($documentos as $doc): ?>
                    <div class="document-item">
                        <div class="document-name"><?php echo $doc->getNome() ?></div>
                        <div>
                            <?php
                                // Determinar status desejado conforme regras dinâmicas e persistir quando necessário
                                $arquivo = $doc->getArquivo();
                                $prazo = $doc->getPrazo();
                                $dataEnvio = $doc->getDataEnvio();

                                // iniciar com o status atual
                                $desiredStatus = intval($doc->getStatus());

                                if (empty($arquivo)) {
                                    // sem arquivo: se prazo passou, mostrar Atrasado
                                    if (!empty($prazo) && strtotime($prazo) < time()) {
                                        $desiredStatus = Documento::STATUS_ATRASADO;
                                        echo '<span class="status-badge status-atrasado">Atrasado</span>';
                                    } else {
                                        $desiredStatus = Documento::STATUS_PENDENTE;
                                        echo '<span class="status-badge status-nao-enviado">Não Enviado</span>';
                                    }
                                    // persistir se necessário
                                    if ($desiredStatus !== intval($doc->getStatus())) {
                                        Documento::atualizarStatus($doc->getIdDocumento(), $desiredStatus);
                                        $doc->setStatus($desiredStatus);
                                    }
                                } else {
                                    // existe arquivo -> avaliar prazo
                                    if (!empty($prazo)) {
                                        $prazoTs = strtotime($prazo);
                                        $nowTs = time();
                                        // se já existe data de envio, considerar Entregue independentemente do prazo
                                        if (!empty($dataEnvio)) {
                                            $desiredStatus = Documento::STATUS_ENVIADO;
                                        } else {
                                            if ($prazoTs < $nowTs) {
                                                // prazo passou e sem data de envio -> Atrasado
                                                $desiredStatus = Documento::STATUS_ATRASADO;
                                            } else {
                                                $desiredStatus = Documento::STATUS_ENVIADO; // prazo futuro
                                            }
                                        }
                                    } else {
                                        // sem prazo cadastrado
                                        if (intval($doc->getStatus()) === Documento::STATUS_CONCLUIDO) {
                                            $desiredStatus = Documento::STATUS_CONCLUIDO;
                                        } else {
                                            $desiredStatus = Documento::STATUS_ENVIADO;
                                        }
                                    }

                                    // persistir se necessário
                                    if ($desiredStatus !== intval($doc->getStatus())) {
                                        Documento::atualizarStatus($doc->getIdDocumento(), $desiredStatus);
                                        $doc->setStatus($desiredStatus);
                                    }

                                    // exibir badge baseado no status atualizado
                                    $cls = $statusClass[$desiredStatus] ?? 'status-pendente';
                                    $txt = $statusText[$desiredStatus] ?? 'Desconhecido';
                                    echo '<span class="status-badge ' . $cls . '">' . $txt . '</span>';
                                }
                            ?>
                        </div>
                        <div style="<?php
                            $dataEnvio = $doc->getDataEnvio();
                            $prazo = $doc->getPrazo();
                            
                            if ($dataEnvio && $prazo) {
                                $dataEnvioo = strtotime($dataEnvio);
                                $prazoo = strtotime($prazo);
                                
                                if ($dataEnvioo > $prazoo) {
                                    echo 'color: red; font-weight: bold;';
                                }
                            }
                        ?>">
                            <?php echo ($dataEnvio) ? date('d/m/Y', strtotime($dataEnvio)) : '--'; ?>
                        </div>
                        <div><?php echo $doc->getPrazo() ? date('d/m/Y', strtotime($doc->getPrazo())) : '--'; ?></div>
                        <div class="document-actions">
                            <?php if ($doc->getArquivo()): ?>
                                <a href="uploads/<?php echo $doc->getArquivo(); ?>" target="_blank" class="btn-action btn-view">Visualizar</a>
                                <a href="removerEnvioDoc.php?idDocumento=<?php echo $doc->getIdDocumento(); ?>&idEstagio=<?php echo $idEstagio; ?>" class="btn-action btn-remove" onclick="return confirm('Tem certeza que deseja remover o envio deste documento?')">Remover Envio</a>
                            <?php endif; ?>
                                <a href="AnexarDoc.php?idEstagio=<?php echo $idEstagio; ?>&idDocumento=<?php echo $doc->getIdDocumento(); ?>" class="btn-action btn-upload">Anexar Documento</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <br>
        <div class="actions-bar">
            <a href="listagem.php" class="btn-secondary">
                Voltar para Listagem de Estágios
            </a>
        </div>
    </div>
</body>
</html>