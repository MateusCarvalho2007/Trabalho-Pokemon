<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['idUsuario']) || $_SESSION['tipo'] != 'professor') {
    $_SESSION['erro'] = "Você precisa fazer login como professor para cadastrar um estágio!";
    header("Location: index.php");
    exit;
}

// processamento do formulário de cadastro de estágio
if(isset($_POST['botao'])){
    require_once __DIR__ . "/classes/Estagio.php";
    require_once __DIR__ . "/classes/Usuario.php";

    try {
        $idProfessor = $_SESSION['idUsuario'];
        $professorNome = $_SESSION['nome']; // Usa diretamente da sessão
        
        // Buscar aluno
        $alunoEncontrado = Usuario::acharUsuarioPeloNome(trim($_POST['nomeAluno']));
        if (!$alunoEncontrado) {
            throw new Exception("Aluno não encontrado!");
        }
        
        // Criar estágio com método mais seguro
        $e = new Estagio();
        $e->setName(trim($_POST['nomeAluno']));
        $e->setDataInicio($_POST['dataInicio']);
        $e->setDataFim($_POST['dataFim']);
        $e->setEmpresa(trim($_POST['nomeEmpresa']));
        $e->setSetorEmpresa(trim($_POST['setor']));
        $e->setVinculoTrabalhista(intval($_POST['vinculo']));
        $e->setObrigatorio(intval($_POST['estagioTipo']));
        $e->setNameSupervisor(trim($_POST['nomeSupervisor']));
        $e->setEmailSupervisor(trim($_POST['emailSupervisor']));
        $e->setProfessor($professorNome);
        $e->setIdAluno($alunoEncontrado->getIdUsuario());
        $e->setIdProfessor($idProfessor);
        $e->setStatus(1);

        if ($e->save()) {
            $_SESSION['sucesso'] = "Estágio cadastrado com sucesso!";
            header("Location: listagem.php");
            exit;
        } else {
            throw new Exception("Erro ao salvar no banco de dados.");
        }
        
    } catch (Exception $e) {
        $_SESSION['erro'] = $e->getMessage();
    }
}

require_once __DIR__ . "/classes/Usuario.php";
$alunos = Usuario::listarAlunos();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Estágio</title>
    <link rel="stylesheet" href="styles/cadastro.css">
    <style>
        .mensagem {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .erro {
            color: red;
            background: #ffe6e6;
            border: 1px solid red;
        }
        .sucesso {
            color: green;
            background: #e6ffe6;
            border: 1px solid green;
        }
    </style>
</head>
<body>
    <h1>Cadastrar Estágio</h1>

    <form action="cadastro.php" method="post">
        <label for="nomeAluno">Nome do Aluno:</label>
        <input type="text" name="nomeAluno" id="nomeAluno" list="listaAlunos" required value="<?php echo isset($_POST['nomeAluno']) ? htmlspecialchars($_POST['nomeAluno']) : ''; ?>">
        
        <datalist id="listaAlunos">
            <?php foreach($alunos as $aluno): ?>
                <option value="<?php echo htmlspecialchars($aluno->getNome()); ?>">
            <?php endforeach; ?>
        </datalist>

        <label for="nomeEmpresa">Nome da Empresa:</label>
        <input type="text" name="nomeEmpresa" id="nomeEmpresa" required value="<?php echo isset($_POST['nomeEmpresa']) ? htmlspecialchars($_POST['nomeEmpresa']) : ''; ?>">

        <label for="setor">Setor Atuante:</label>
        <input type="text" name="setor" id="setor" required value="<?php echo isset($_POST['setor']) ? htmlspecialchars($_POST['setor']) : ''; ?>">

        <label for="nomeSupervisor">Nome do Supervisor:</label>
        <input type="text" name="nomeSupervisor" id="nomeSupervisor" required value="<?php echo isset($_POST['nomeSupervisor']) ? htmlspecialchars($_POST['nomeSupervisor']) : ''; ?>">
        
        <label for="emailSupervisor">E-mail do Supervisor:</label>
        <input type="email" name="emailSupervisor" id="emailSupervisor" required value="<?php echo isset($_POST['emailSupervisor']) ? htmlspecialchars($_POST['emailSupervisor']) : ''; ?>">

        <label for="dataInicio">Data de Início:</label>
        <input type="date" name="dataInicio" id="dataInicio" required value="<?php echo isset($_POST['dataInicio']) ? htmlspecialchars($_POST['dataInicio']) : ''; ?>">

        <label for="dataFim">Data de Fim:</label>
        <input type="date" name="dataFim" id="dataFim" required value="<?php echo isset($_POST['dataFim']) ? htmlspecialchars($_POST['dataFim']) : ''; ?>">

        <div>
            <label>Tipo de Estágio:</label>
            <label><input type="radio" name="estagioTipo" value="1" required <?php echo (isset($_POST['estagioTipo']) && $_POST['estagioTipo'] == 1) ? 'checked' : ''; ?>> Obrigatório</label>
            <label><input type="radio" name="estagioTipo" value="0" <?php echo (isset($_POST['estagioTipo']) && $_POST['estagioTipo'] == 0) ? 'checked' : ''; ?>> Não Obrigatório</label>
        </div>

        <div>
            <label>Vínculo Trabalhista:</label>
            <label><input type="radio" name="vinculo" value="1" required <?php echo (isset($_POST['vinculo']) && $_POST['vinculo'] == 1) ? 'checked' : ''; ?>> Carteira Assinada</label>
            <label><input type="radio" name="vinculo" value="0" <?php echo (isset($_POST['vinculo']) && $_POST['vinculo'] == 0) ? 'checked' : ''; ?>> Sem Carteira Assinada</label>
        </div>

        <button type="submit" name="botao" value="cadastrar">Cadastrar</button>
        <button type="button" onclick="location.href='listagem.php'">Cancelar</button>
    </form>
</body>
</html>