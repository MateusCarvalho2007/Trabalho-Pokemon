    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    session_start();

    // Verifica se o usuário está logado
    if (!isset($_SESSION['idUsuario'])) {
        header("Location: index.php");
        exit;
    }

    if(isset($_GET['idEstagio'])){
        require_once __DIR__.'/classes/Estagio.php';
        Estagio::mudarStatus($_GET['idEstagio']);
    }

    require_once __DIR__.'/classes/Estagio.php';
    require_once __DIR__.'/classes/Usuario.php';
    $idUsuario = $_SESSION['idUsuario'];
    $usuario = Usuario::acharUsuario($idUsuario);
    $estagios = Estagio::findall($idUsuario);
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Listagem de Estágios</title>
        <link rel="stylesheet" href="styles/listagem.css">
    </head>
    <body>
        <div style="background-color: #f8f9fa; padding: 10px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
                <h1 style="margin: 0;">Lista de Estágios</h1>
                <?php if (isset($_SESSION['nome'])): ?>
        <span style="margin-right: 15px;">
           Bem-vindo, <?= $_SESSION['tipo'] ?> <?= $_SESSION['nome'] ?>, ao AAGIS!
        </span>
        <div class="user-info" style="display: flex; align-items: center; gap: 18px">
               <a href="perguntas.php" style="color: #007bff; text-decoration: none; font-weight: 600;">Perguntas Frequentes</a>
                    <a href="logout.php" class="btn-logout" style="
                    background-color: #dc3545;
                    color: white;
                    padding: 8px 20px;
                    text-decoration: none;
                    border-radius: 5px;
                    font-size: 18px;">Sair</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if($_SESSION['tipo'] == 'professor'): ?>
            <a href="cadastro.php" class="cad">Cadastrar Novo Estágio</a>
        <?php endif; ?>
        <br>
        <br>
        <table border="1" cellpadding="6" cellspacing="0" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <?php if($_SESSION['tipo'] == 'professor'): ?>
                        <th>Aluno</th>
                    <?php endif; ?>
                    <th>Empresa</th>
                    <th>Período</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($estagios as $estagio): ?>
                <?php if($estagio->getSetorEmpresa() == ""): ?>
                    <?php $user = Usuario::acharUsuario($estagio->getIdAluno()); ?>
                    <?php $estagio->setName($user->getNome()) ?>
                    <tr>
                        <?php if($_SESSION['tipo'] == 'professor'): ?>
                            <td style = "color:red;"><?= htmlspecialchars($estagio->getName()) ?></td>
                            <td style = "color:red;"><?= htmlspecialchars($estagio->getEmpresa()) ?></td>
                            <td style = "color:red;">CADASTRO DE ESTÁGIO PENDENTE!!!</td>
                            <td style = "color:red;">CADASTRO DE ESTÁGIO PENDENTE!!!</td>
                            <td><a href="editar.php?idEstagio=<?= $estagio->getIdEstagio() ?>" style="
                                background-color: #6c757d; color: #fff;
                                padding: 8px 12px;
                                text-decoration: none;
                                border-radius: 5px;
                                font-size: 15px;">Completar Cadastro</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php else: ?>
                    <tr>
                        <?php if($_SESSION['tipo'] == 'professor'): ?>
                            <td><?= htmlspecialchars($estagio->getName()) ?></td>
                        <?php endif; ?>
                        <?php $DI = date('d/m/Y', strtotime($estagio->getDataInicio())); ?>
                        <?php $DF = date('d/m/Y', strtotime($estagio->getDataFim())); ?>
                        <td><?= htmlspecialchars($estagio->getEmpresa()) ?></td>
                        <td><?= str_replace('-', '/', htmlspecialchars($DI)) ?> - <?= str_replace('-', '/', htmlspecialchars($DF)) ?></td>
                        <td>
                            <?php
                                $s = $estagio->getStatus();
                                // usa helper para rótulo consistente
                                echo \Estagio::getStatusLabel($s);
                            ?>
                        </td>
                        <td>
                            <?php if($estagio->isConcluido()): ?>
                                <span style="color:gray">Inacessível (Estagio Concluido)</span>
                            <?php else: ?>
                                
                                <a href="editar.php?idEstagio=<?= $estagio->getIdEstagio() ?>">Editar</a>
                                |
                                <a href="visualizacao.php?idEstagio=<?= $estagio->getIdEstagio() ?>">Visualizar Dados</a>
                                |
                                <a href="listagemDoc.php?idEstagio=<?= $estagio->getIdEstagio() ?>">Listagem Documentos</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php if($_SESSION['tipo'] == 'professor'): ?>
            <a href="cadastro.php" class="cad">Cadastrar Novo Estágio</a>
        <?php endif; ?>
    </body>
    </html>