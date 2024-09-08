<?php
session_start();
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $data_nascimento = $_POST['data_nascimento'];
    $cpf = $_POST['cpf'];
    $senha = ($_POST['senha']); // Hash da senha
    $tipo = $_POST['tipo'];
    $empresa = isset($_POST['empresa']) ? $_POST['empresa'] : '';

    // Inicia uma transação
    $conexao->begin_transaction();

    try {
        // Inserir dados na tabela usuario
        $sqlUsuario = "INSERT INTO usuario (NOME, EMAIL, TELEFONE, DATA_NASCIMENTO, CPF_CNPJ, SENHA, TIPO) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtUsuario = $conexao->prepare($sqlUsuario);
        $stmtUsuario->bind_param('sssssss', $nome, $email, $telefone, $data_nascimento, $cpf, $senha, $tipo);
        $stmtUsuario->execute();

        // Obtém o ID do usuário recém-criado
        $user_id = $conexao->insert_id;

        if ($tipo === 'organizador') {
            // Inserir dados na tabela organizador
            $sqlOrganizador = "INSERT INTO organizador (ID_ORGANIZADOR, CNPJ_EMPRESA) VALUES (?, ?)";
            $stmtOrganizador = $conexao->prepare($sqlOrganizador);
            $stmtOrganizador->bind_param('is', $user_id, $empresa);
            $stmtOrganizador->execute();
        }

        // Commit da transação
        $conexao->commit();

        // Redireciona para a tela de login
        header('Location: login.html');
        exit(); // Garante que o script pare após o redirecionamento
    } catch (Exception $e) {
        // Rollback da transação em caso de erro
        $conexao->rollback();
        echo "Erro ao cadastrar usuário: " . $e->getMessage();
    }

    // Fecha as instruções preparadas
    $stmtUsuario->close();
    if (isset($stmtOrganizador)) {
        $stmtOrganizador->close();
    }
    // Fecha a conexão
    $conexao->close();
} else {
    echo "Método de solicitação inválido.";
}
?>
