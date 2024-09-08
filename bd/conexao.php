<?php
    // Correção no nome da variável
    $dbHost = 'localhost';
    $dbUsername = 'root'; // corrigido
    $dbPassword = '';
    $dbName = 'projetobd'; // corrigido o nome da variável para consistência

    // Conexão usando MySQLi orientado a objetos
    $conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    // Verificar se houve erro na conexão
    if ($conexao->connect_error) {
        die("Houve um erro na conexão: " . $conexao->connect_error);
    } else {
        echo "Conexão bem-sucedida!";
    }
?>