<?php
session_start();
require_once __DIR__ . "/classes/Usuario.php";

if (isset($_SESSION['idUsuario'])) {
    header("Location: listagem.php");
    exit;
}

if (isset($_POST['login'])) {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

    if (Usuario::autenticar($email, $senha)) {
        header("Location: listagem.php");
        exit;
    } else {
        $_SESSION['erro'] = "E-mail ou senha incorretos.";
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Estágios</title>
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
    <h1>Login</h1>
    
    <?php if (isset($_SESSION['erro'])): ?>
        <div class="error">
            <?php 
                echo $_SESSION['erro'];
                unset($_SESSION['erro']);
            ?>
        </div>
    
    <?php endif; ?>

    <form action="index.php" method="post">
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
        </div>

        <button type="submit" name="login">Entrar</button>
    </form>
</body>
</html>