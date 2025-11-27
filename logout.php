<?php
session_start();

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Destroi o cookie da sessão
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroi a sessão
session_destroy();

// Redireciona para a página de login
header("Location: https://billyorg.com/2025/projeto/grupo3/views/TelaInicial/");
exit;