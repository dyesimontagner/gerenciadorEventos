<?php
session_start(); // Inicia a sessão

// Inclui o arquivo de conexão com o banco de dados
include_once('conexao.php');

// Verifica se o formulário foi enviado
if (isset($_POST['login2'])) {
    // Coleta os dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Prepara a consulta SQL usando prepared statements para evitar SQL Injection
    $sql = "SELECT * FROM usuario WHERE EMAIL = ? AND SENHA = ?";
    if ($stmt = $conexao->prepare($sql)) {
        // Vincula os parâmetros à consulta
        $stmt->bind_param('ss', $email, $senha);

        // Executa a consulta
        $stmt->execute();

        // Obtém o resultado da consulta
        $result = $stmt->get_result();

        // Verifica se há algum usuário correspondente
        if ($result->num_rows < 1) {
            // Se não houver usuário correspondente, redireciona para a página de login com uma mensagem de erro
            header('Location: login.html?error=1');
            exit; // Adiciona exit para garantir que o script pare após o redirecionamento
        } else {
            // Obtém os dados do usuário
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['ID_USUARIO']; // Armazena o ID do usuário na sessão
            
            // Verifica o tipo de usuário e redireciona para a página correspondente
            if ($user['TIPO'] == 'cliente') {
                header('Location: PerfilUsu.php');
            } elseif ($user['TIPO'] == 'organizado') {
                header('Location: PerfilOrga.php');
            } else {
                // Caso o tipo não seja conhecido, redireciona para uma página de erro ou login
                header('Location: login.html?error=2');
            }
            exit; // Adiciona exit para garantir que o script pare após o redirecionamento
        }

        // Fecha a instrução preparada
        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta: " . $conexao->error;
    }

    // Fecha a conexão
    $conexao->close();
} else {
    // Redireciona para a página de login se o formulário não foi enviado
    header('Location: login.html');
    exit; // Adiciona exit para garantir que o script pare após o redirecionamento
}
?>
