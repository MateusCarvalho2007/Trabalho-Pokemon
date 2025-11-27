<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['idUsuario'])) {
    header("Location: index.php");
    exit;
}

$idEstagio = isset($_GET['idEstagio']) ? intval($_GET['idEstagio']) : 0;
$idDocumento = isset($_GET['idDocumento']) ? intval($_GET['idDocumento']) : 0;

// processamento do upload e gravação no banco
require_once __DIR__ . '/classes/Documento.php';
require_once __DIR__ . '/classes/Comentario.php';
require_once __DIR__ . '/classes/Estagio.php';

$estagio = Estagio::find($idEstagio);
$documento = documento::find($idDocumento);

$error = '';
$message = '';

// Processar ações de comentários (criar, editar, deletar)
$comentarioMessage = '';
$comentarioError = '';

// Função auxiliar para contar comentários
function contarComentarios($idDoc) {
    if ($idDoc <= 0) return 0;
    $comentarios = Comentario::findByDocumento($idDoc);
    return count($comentarios);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ação de adicionar novo comentário
    if (isset($_POST['acao']) && $_POST['acao'] === 'adicionar_comentario') {
        $textoComentario = trim($_POST['comentario'] ?? '');
        if (empty($textoComentario)) {
            $comentarioError = 'Comentário não pode estar vazio.';
        } else {
            // Garante que exista um documento associado. Se não existir, cria um registro mínimo.
            $targetDocumentoId = $idDocumento;
            $docExiste = false;
            if ($targetDocumentoId > 0) {
                $docExiste = Documento::find($targetDocumentoId) !== null;
            }

            if (!$docExiste) {
                // cria documento mínimo e recupera novo id
                // Preenche 'prazo' com a data atual para evitar erro de NOT NULL
                $prazoDefault = date('Y-m-d');
                $novoDoc = new Documento($idEstagio, 'Anexo', '', Documento::STATUS_PENDENTE, $prazoDefault);
                if ($novoDoc->save()) {
                    $targetDocumentoId = $novoDoc->getIdDocumento();
                    // se o idDocumento original era 0, atualiza a variável para uso posterior
                    $idDocumento = $targetDocumentoId;
                } else {
                    $comentarioError = 'Erro ao criar registro de documento para associar o comentário.';
                }
            }

            if (empty($comentarioError)) {
                $novoComentario = new Comentario($targetDocumentoId, $_SESSION['idUsuario'], $textoComentario);
                if ($novoComentario->save()) {
                    // redireciona para evitar re-submissão e garantir que a lista inclua o novo comentário
                    header('Location: AnexarDoc.php?idEstagio=' . intval($idEstagio) . '&idDocumento=' . intval($targetDocumentoId));
                    exit;
                } else {
                    $comentarioError = 'Erro ao adicionar comentário.';
                }
            }
        }
    }

    // Ação de atualizar comentário
    if (isset($_POST['acao']) && $_POST['acao'] === 'editar_comentario') {
        $idComentario = intval($_POST['idComentario'] ?? 0);
        $textoComentario = trim($_POST['comentario'] ?? '');
        
        if ($idComentario <= 0 || empty($textoComentario)) {
            $comentarioError = 'Dados inválidos para edição.';
        } else {
            $comentarioExistente = Comentario::find($idComentario);
            if (!$comentarioExistente) {
                $comentarioError = 'Comentário não encontrado.';
            } elseif ($comentarioExistente->getIdUsuario() != $_SESSION['idUsuario']) {
                $comentarioError = 'Você só pode editar seus próprios comentários.';
            } else {
                $comentarioExistente->setComentario($textoComentario);
                if ($comentarioExistente->update()) {
                    $comentarioMessage = 'Comentário atualizado com sucesso!';
                } else {
                    $comentarioError = 'Erro ao atualizar comentário.';
                }
            }
        }
    }

    // Ação de deletar comentário
    if (isset($_POST['acao']) && $_POST['acao'] === 'deletar_comentario') {
        $idComentario = intval($_POST['idComentario'] ?? 0);
        
        if ($idComentario <= 0) {
            $comentarioError = 'Comentário inválido.';
        } else {
            $comentarioExistente = Comentario::find($idComentario);
            if (!$comentarioExistente) {
                $comentarioError = 'Comentário não encontrado.';
            } elseif ($comentarioExistente->getIdUsuario() != $_SESSION['idUsuario']) {
                $comentarioError = 'Você só pode deletar seus próprios comentários.';
            } else {
                if (Comentario::delete($idComentario)) {
                    $comentarioMessage = 'Comentário deletado com sucesso!';
                } else {
                    $comentarioError = 'Erro ao deletar comentário.';
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['acao'])) {
    if (!isset($_FILES['documento'])) {
        $error = 'Nenhum arquivo foi enviado.';
    } else {
        $file = $_FILES['documento'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Erro no upload: ' . $file['error'];
        } else {
            // validações
            $allowed = ['pdf','doc','docx','txt','odt','rtf'];
            $maxSize = 10 * 1024 * 1024; // 10MB
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $error = 'Extensão não permitida.';
            } elseif ($file['size'] > $maxSize) {
                $error = 'Arquivo muito grande. Máx 10MB.';
            } else {
                // garante pasta uploads
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $dest = $uploadDir . $newName;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    // atualizar documento no BD
                    if ($idDocumento > 0) {
                        $doc = Documento::find($idDocumento);
                        if ($doc) {
                            $doc->setArquivo($newName);
                            $doc->setStatus(Documento::STATUS_ENVIADO);
                            $doc->setDataEnvio(date('Y-m-d H:i:s'));
                            $documento = Documento::find($documento->getIdDocumento());
                            $dataEnvio = $documento->getDataEnvio();
                            if ($doc->update()) {
                                $message = 'Arquivo enviado e registrado com sucesso.';
                            } else {
                                $error = 'Arquivo salvo, mas erro ao atualizar o banco.';
                            }
                        } else {
                            $error = 'Documento não encontrado.';
                        }
                    } else {
                        // Caso não exista idDocumento, cria um novo registro
                        // Preenche 'prazo' com a data atual para evitar erro de NOT NULL
                        $prazoDefault = date('Y-m-d');
                        $novo = new Documento($idEstagio, 'Anexo', $newName, Documento::STATUS_ENVIADO, $prazoDefault);
                        $novo->setDataEnvio(date('Y-m-d H:i:s'));
                        if ($novo->save()) {
                            $message = 'Arquivo enviado e novo registro criado com sucesso.';
                            // atualiza idDocumento para o novo registro
                            $idDocumento = $novo->getIdDocumento();
                        } else {
                            $error = 'Erro ao salvar registro do documento.';
                        }
                    }
                } else {
                    $error = 'Falha ao mover o arquivo para o diretório de upload.';
                }
            }
        }
    }
}
?>

<?php
// Preparar exibição de status/prazo antes do HTML
$displayStatusText = '—';
$displayStatusClass = '';
$prazoDisplay = '--';
$diasRemainingText = '--';

if (isset($documento) && $documento) {
    $arquivo = $documento->getArquivo();
    $prazo = $documento->getPrazo();
    $dataEnvio = $documento->getDataEnvio();
    $currentStatus = intval($documento->getStatus());

    // Mapas para texto/classe
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

    // calcular desiredStatus: se já houver data de envio, considerar Entregue
    $desiredStatus = $currentStatus;
    if (empty($arquivo)) {
        $desiredStatus = Documento::STATUS_PENDENTE; // Não Enviado
    } else {
        // se já existe data de envio, mostrar como Entregue independentemente do prazo
        if (!empty($dataEnvio)) {
            $desiredStatus = Documento::STATUS_ENVIADO;
        } else {
            if (!empty($prazo)) {
                $prazoTs = strtotime($prazo);
                $nowTs = time();
                if ($prazoTs < $nowTs) {
                    // prazo passou e sem data de envio -> Atrasado
                    $desiredStatus = Documento::STATUS_ATRASADO;
                } else {
                    $desiredStatus = Documento::STATUS_ENVIADO; // prazo futuro
                }
            } else {
                // sem prazo
                if ($currentStatus === Documento::STATUS_CONCLUIDO) {
                    $desiredStatus = Documento::STATUS_CONCLUIDO;
                } else {
                    $desiredStatus = Documento::STATUS_ENVIADO;
                }
            }
        }
    }

    // Persistir status automaticamente se existir documento e o status calculado for diferente
    if (isset($documento) && $documento && intval($documento->getIdDocumento()) > 0) {
        if ($desiredStatus !== $currentStatus) {
            Documento::atualizarStatus($documento->getIdDocumento(), $desiredStatus);
            $documento->setStatus($desiredStatus);
            // atualizar currentStatus para o restante da exibição
            $currentStatus = $desiredStatus;
        }
    }

    // definir textos/classes para exibição
    if (empty($arquivo)) {
        $displayStatusText = 'Não Enviado';
        $displayStatusClass = 'status-nao-enviado';
    } else {
        $displayStatusText = $statusText[$desiredStatus] ?? 'Desconhecido';
        $displayStatusClass = $statusClass[$desiredStatus] ?? '';
    }

    // preparar exibição do prazo
    if (!empty($prazo)) {
        // exibir no formato dd/mm/YYYY H:i quando possível
        $prazoDisplay = date('d/m/Y', strtotime($prazo));

        // calcular dias restantes
        $now = time();
        $prazoTs = strtotime($prazo);
        $diff = $prazoTs - $now;
        if ($diff >= 0) {
            $days = floor($diff / 86400);
            $hours = floor(($diff % 86400) / 3600);
            $minutes = floor(($diff % 3600) / 60);
            $diasRemainingText = sprintf('%s dia(s) %s hora(s) %s minuto(s) restantes', $days, $hours, $minutes);
        } else {
            $diff = abs($diff);
            $days = floor($diff / 86400);
            $hours = floor(($diff % 86400) / 3600);
            $diasRemainingText = sprintf('Vencido há %s dia(s) %s hora(s)', $days, $hours);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Anexar Documento - AAGIS</title>
    <link rel="stylesheet" href="styles/cadastro.css">
    <style>
        /* Estilos específicos para a página de anexar documento */
        .file-upload-container {
            margin-bottom: 1.5rem;
        }
        
        .drop-container {
            border: 2px dashed #007bff;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
            margin-bottom: 1rem;
        }
        
        .drop-container:hover {
            border-color: #0056b3;
            background: #e3f2fd;
        }
        
        .drop-container.drag-active {
            border-color: #0056b3;
            background: #e3f2fd;
            transform: scale(1.02);
        }
        
        .drop-icon {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 1rem;
        }
        
        .drop-text h3 {
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .drop-text p {
            color: #666;
            margin-bottom: 1rem;
            background: none;
            box-shadow: none;
            padding: 0;
        }
        
        .btn-browse {
            background: #007bff;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-browse:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }
        
        input[type="file"] {
            display: none;
        }
        
        .file-info {
            margin-top: 1rem;
            padding: 1rem;
            background: #e7f3ff;
            border-radius: 6px;
            border-left: 4px solid #007bff;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .file-info.hidden {
            display: none;
        }
        
        .file-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .file-size {
            color: #666;
            font-size: 0.9rem;
        }
        
        .file-types {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
            justify-content: center;
        }
        
        .file-type {
            background: #007bff;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .form-actions {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center; 
            justify-content: center;
            align-items: center;
            
        }
        
        .btn-cancel {
            background-color: #6c757d;
            color: #fff;
            font-weight: bold;
            font-size: 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-cancel:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
            color: #fff;
        }
        
        /* Ajustes para mensagens */
        .message-container {
            max-width: 600px;
            margin: 1rem auto;
        }
        
        /* Animações */
        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 rgba(0, 123, 255, 0.4);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
            }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }

        /* Badges de status (compatível com listagem) */
        .status-badge { padding: 4px 8px; font-size: 0.85rem; font-weight: 500; display: inline-block; border-radius: 4px; }
        .status-pendente { background-color: #fff3cd; color: #856404; }
        .status-enviado { background-color: #d1ecf1; color: #0c5460; }
        .status-concluido { background-color: #d4edda; color: #155724; }
        .status-atrasado { background-color: #f8d7da; color: #721c24; }
        .status-nao-enviado { background-color: #f0f0f0; color: #6c757d; }

        /* Ajuste do botão voltar */
        .back-button-container {
            text-align: center;
            margin-top: 1rem;
        }

        .info-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
        }

        .info-value {
            color: #666;
            margin-bottom: 1.5rem;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 3px solid #007bff;
        }

        .ines{
            all: revert;
        }

        .butao{
            margin-right: 10px;
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            font-size: 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            text-align: center;
            justify-content: center;
            align-items: center;
            height: 50px;
            
        }

        .butao:hover{
            background-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
            color: #fff;
        }

        .doc{
            text-shadow: none !important;
        }

        .comment-form{
            display: flex;
            align-items: center;
            flex-direction: column;
        }
        .comments-list{
            weight: 100%;
            width: 100%;
        }

    </style>
</head>
<body>
    <h1>Anexar Documento (<?php echo $estagio->getName() ?> - <?php echo $estagio->getEmpresa() ?>)</h1>

    <form action="AnexarDoc.php?idEstagio=<?php echo $idEstagio; ?>&idDocumento=<?php echo $idDocumento; ?>" method="POST" enctype="multipart/form-data" style="width: 720px;">
    <div class="back-button-container">
</div>   
    <div class="form-group">
            <h1 style="color: black;" class="doc"><?php echo isset($documento) && $documento ? htmlspecialchars($documento->getNome()) : 'Documento';?></h1>
        </div>
        
        <div class="form-group">
            <label class="info-label">Vencimento</label>
            <div class="info-value"><?php echo htmlspecialchars($prazoDisplay); ?></div>
        </div>
        
        <div class="form-group">
            <label class="info-label">Status de Envio</label>
            <div class="info-value"><span class="status-badge <?php echo htmlspecialchars($displayStatusClass); ?>"><?php echo htmlspecialchars($displayStatusText); ?></span></div>
        </div>

        <div class="form-group">
            <label class="info-label">Data de Envio</label>
            <div class="info-value">
                <?php 
                    // Exibir a data de envio formatada ou 'N/A' se não houver
                    if (!empty($dataEnvio)) {
                        $dataDisplay = date('d/m/Y', strtotime($dataEnvio));
                        echo htmlspecialchars($dataDisplay);
                    } else {
                        echo 'N/A';
                    }
                ?>
            </div>
        </div>
        
        <div class="form-group">
            <label class="info-label">Dias Restantes</label>
            <div class="info-value"><?php echo htmlspecialchars($diasRemainingText); ?></div>
        </div>
        
        <div class="form-group file-upload-container">
            <label class="info-label">Documento</label>
            <div class="drop-container" id="dropcontainer">
                <div class="drop-icon">📄</div>
                <div class="drop-text">
                    <h3>Arraste e solte seu arquivo aqui</h3>
                    <p>ou</p>
                </div>
                <button type="button" class="btn-browse pulse" onclick="document.getElementById('documento').click()">
                    Selecione um arquivo
                </button>
                <div class="file-types">
                    <span class="file-type">PDF</span>
                    <span class="file-type">DOC</span>
                    <span class="file-type">DOCX</span>
                    <span class="file-type">TXT</span>
                    <span class="file-type">ODT</span>
                    <span class="file-type">RTF</span>
                </div>
                <input type="file" id="documento" name="documento" accept=".pdf,.doc,.docx,.txt,.odt,.rtf">
            </div>
            <div class="file-info hidden" id="fileInfo">
                <div class="file-name" id="fileName"></div>
                <div class="file-size" id="fileSize"></div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="butao">Anexar Documento</button>
            <!-- Botão de Editar Documento -->
            <?php if($_SESSION['tipo'] == 'professor'):?>
                <a href="editarDocumento.php?idDocumento=<?= $idDocumento ?>&idEstagio=<?= $idEstagio ?>" 
                class="butao" 
                style="background-color:#007bff;">
                Editar Documento
                </a>   
            <?php endif; ?>
            
            <a href="listagemDoc.php?idEstagio=<?php echo $idEstagio; ?>" style="background-color: #6c757d;
             color: #fff; font-weight: bold; border: none; border-radius: 6px; cursor: pointer;
             text-decoration: none; display: inline-block; text-align: center; transition: all 0.3s ease;">Voltar</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message-container"><div class="file-info" style="background:#e6ffed; border-left:4px solid #28a745;"><?php echo htmlspecialchars($message); ?></div></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="message-container"><div class="file-info" style="background:#fff0f0; border-left:4px solid #dc3545; color:#721c24;"><?php echo htmlspecialchars($error); ?></div></div>
        <?php endif; ?>

    </form>

    <!-- Seção de Comentários -->
    <?php if ($idDocumento > 0): ?>
    <div class="comments-section" style="margin-top: 3rem; max-width: 800px; margin-left: auto; margin-right: auto;">
        <h2 style="color: white; border-bottom: 2px solid #007bff; padding-bottom: 1rem;">Comentários</h2>
        
        <!-- Mensagens de feedback -->
        
        
        <!-- Formulário para adicionar novo comentário -->
        <div class="comment-form" style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <h3 style="color: #333; margin-top: 0;">Adicionar Comentário</h3>
            <form action="" method="post" class="ines" style="width: 100%;">
                <input type="hidden" name="acao" value="adicionar_comentario">
                <div class="form-group">
                <div class="info-value">Comentários (<?php echo contarComentarios($idDocumento); ?>)</div>
                </div>
                <div class="form-group">
                    <textarea name="comentario" id="comentario" rows="4" placeholder="Digite seu comentário..." required 
                              style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-family: Arial, sans-serif; resize: vertical; box-sizing: border-box;"></textarea>
                </div>
                <div style="display: flex; gap: 1rem; align-items: center; justify-content: center;">
                    <button type="submit" class="btn-browse" style="padding: 0.65rem 1.5rem; background-color: #28a745;">Enviar Comentário</button>
                    <a href="listagemDoc.php?idEstagio=<?php echo $idEstagio; ?>" style="background-color: #6c757d;
                    color: #fff; font-weight: bold; border: none; border-radius: 6px; cursor: pointer; 
                    text-decoration: none; display: inline-block; text-align: center; transition: all 0.3s ease; height: 51px;
                    align-items: center; justify-content: center;">Voltar</a>
                </div>
            </form>
        </div>
        <div class="comment-form" style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; ">
            <!-- Exibir comentários existentes -->
            <div class="comments-list">
                <?php 
                    $comentarios = Comentario::findByDocumento($idDocumento);
                    if (empty($comentarios)): 
                ?>
                <?php else: ?>
                    <?php foreach ($comentarios as $comentario): ?>
                        <div class="comment-item" style="background: white; border: 1px solid #e0e0e0; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; margin-top: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <div>
                                    <strong style="color: #333; font-size: 1rem;"><?php echo htmlspecialchars($comentario->getNomeUsuario() ?? 'Usuário Desconhecido'); ?></strong>
                                    <span style="color: #999; font-size: 0.85rem; margin-left: 0.5rem;">
                                        <?php 
                                            $data = DateTime::createFromFormat('Y-m-d H:i:s', $comentario->getDataHora());
                                            echo $data ? $data->format('d/m/Y H:i') : htmlspecialchars($comentario->getDataHora());
                                        ?>
                                    </span>
                                </div>
                                <?php if ($comentario->getIdUsuario() == $_SESSION['idUsuario']): ?>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button type="button" class="btn-edit" onclick="editarComentario(<?php echo $comentario->getIdComentario(); ?>, <?php echo htmlspecialchars(json_encode($comentario->getComentario())); ?>)" 
                                                style="background: #17a2b8; color: white; padding: 0.4rem 0.8rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">
                                            Editar
                                        </button>
                                        <button type="button" class="btn-delete" onclick="deletarComentario(<?php echo $comentario->getIdComentario(); ?>)" 
                                                style="background: #dc3545; color: white; padding: 0.4rem 0.8rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">
                                            Deletar
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <p style="color: #666; margin: 1rem 0; line-height: 1.5; word-wrap: break-word;"><?php echo nl2br(htmlspecialchars($comentario->getComentario())); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <a href="listagemDoc.php?idEstagio=<?php echo $idEstagio; ?>" style="background-color: #6c757d;
                    color: #fff; font-weight: bold; border: none; border-radius: 6px; cursor: pointer; 
                    text-decoration: none; display: inline-block; text-align: center; transition: all 0.3s ease; height: 51px;
                    align-items: center; justify-content: center;">Voltar</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Modal para editar comentário -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); width: 90%; max-width: 500px;">
            <h3 style="color: #333; margin-top: 0;">Editar Comentário</h3>
            <form method="POST" action="" class="ines">
                <input type="hidden" name="acao" value="editar_comentario">
                <input type="hidden" name="idComentario" id="editIdComentario">
                <div class="form-group">
                    <textarea name="comentario" id="editComentario" rows="4" required 
                              style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-family: Arial, sans-serif; resize: vertical; box-sizing: border-box;"></textarea>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" onclick="fecharModal()" class="btn-cancel" style="background-color: #6c757d;">Cancelar</button>
                    <button type="submit" class="btn-browse" style="background-color: #28a745;">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para deletar comentário -->
    <div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); width: 90%; max-width: 400px; text-align: center;">
            <h3 style="color: #333; margin-top: 0;">Confirmar Exclusão</h3>
            <p style="color: #666;">Tem certeza que deseja deletar este comentário? Esta ação não pode ser desfeita.</p>
            <form method="POST" action="" class="ines">
                <input type="hidden" name="acao" value="deletar_comentario">
                <input type="hidden" name="idComentario" id="deleteIdComentario">
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button type="button" onclick="fecharModal()" class="btn-cancel" style="background-color: #6c757d;">Cancelar</button>
                    <button type="submit" class="btn-browse" style="background-color: #dc3545;">Deletar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editarComentario(idComentario, textoComentario) {
            document.getElementById('editIdComentario').value = idComentario;
            document.getElementById('editComentario').value = textoComentario;
            document.getElementById('editModal').style.display = 'flex';
        }

        function deletarComentario(idComentario) {
            document.getElementById('deleteIdComentario').value = idComentario;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function fecharModal() {
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Fechar modais ao clicar fora deles
        window.addEventListener('click', function(event) {
            const editModal = document.getElementById('editModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (event.target === editModal) {
                editModal.style.display = 'none';
            }
            if (event.target === deleteModal) {
                deleteModal.style.display = 'none';
            }
        });
    </script>

    <script>
        // Elementos DOM
        const dropContainer = document.getElementById('dropcontainer');
        const fileInput = document.getElementById('documento');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const browseBtn = document.querySelector('.btn-browse');
        
        // Prevenir comportamento padrão de drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropContainer.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Efeitos visuais para drag and drop
        ['dragenter', 'dragover'].forEach(eventName => {
            dropContainer.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropContainer.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            dropContainer.classList.add('drag-active');
            browseBtn.classList.remove('pulse');
        }
        
        function unhighlight() {
            dropContainer.classList.remove('drag-active');
        }
        
        // Manipular arquivos soltos
        dropContainer.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length) {
                fileInput.files = files;
                updateFileInfo(files[0]);
                browseBtn.classList.remove('pulse');
            }
        }
        
        // Manipular seleção de arquivo via botão
        fileInput.addEventListener('change', function() {
            if (this.files.length) {
                updateFileInfo(this.files[0]);
                browseBtn.classList.remove('pulse');
            }
        });
        
        // Atualizar informações do arquivo selecionado
        function updateFileInfo(file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.classList.remove('hidden');
        }
        
        // Formatar tamanho do arquivo
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Validação do formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            const file = fileInput.files[0];
            if (!file) {
                e.preventDefault();
                alert('Por favor, selecione um arquivo para upload.');
                return;
            }
            
            // Validar extensão do arquivo
            const allowedExtensions = ['.pdf', '.doc', '.docx', '.txt', '.odt', '.rtf'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            
            if (!allowedExtensions.includes(fileExtension)) {
                e.preventDefault();
                alert('Tipo de arquivo não permitido. Por favor, selecione um arquivo PDF, DOC, DOCX, TXT, ODT ou RTF.');
                return;
            }
            
            // Validar tamanho do arquivo (máximo 10MB)
            const maxSize = 10 * 1024 * 1024; // 10MB em bytes
            if (file.size > maxSize) {
                e.preventDefault();
                alert('O arquivo é muito grande. O tamanho máximo permitido é 10MB.');
                return;
            }
                // permitir envio real do formulário quando válido
                // não prevenir; o formulário será submetido ao servidor
        });
    </script>
</body>
</html>