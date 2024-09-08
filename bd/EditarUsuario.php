

<?php
session_start();
include('conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para o login
    header('Location: login.php');
    exit();
}

// Obtém o ID do usuário da sessão
$user_id = $_SESSION['user_id'];

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $data_nascimento = $_POST['data_nascimento'];
    $cpf = $_POST['cpf'];
    $empresa = $_POST['empresa'];

    // Atualiza as informações do usuário
    $sql = "UPDATE usuario SET NOME = ?, EMAIL = ?, TELEFONE = ?, DATA_NASCIMENTO = ?, CPF_CNPJ = ? WHERE ID_USUARIO = ?";
    $stmt = $conexao->prepare($sql);
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conexao->error);
    }
    $stmt->bind_param('sssssi', $nome, $email, $telefone, $data_nascimento, $cpf, $user_id);
    if (!$stmt->execute()) {
        echo "Erro ao atualizar informações do usuário: " . $stmt->error;
        exit();
    }

    // Atualiza as informações da empresa se existir
    if (!empty($empresa)) {
        $sql = "UPDATE organizador SET CNPJ_EMPRESA = ? WHERE ID_ORGANIZADOR = ?";
        $stmt = $conexao->prepare($sql);
        if (!$stmt) {
            die("Erro na preparação da consulta: " . $conexao->error);
        }
        $stmt->bind_param('si', $empresa, $user_id);
        if (!$stmt->execute()) {
            echo "Erro ao atualizar informações da empresa: " . $stmt->error;
            exit();
        }
    }

    // Determina o tipo de usuário para redirecionar
    $sql = "SELECT TIPO FROM usuario WHERE ID_USUARIO = ?";
    $stmt = $conexao->prepare($sql);
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conexao->error);
    }
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Exibe uma mensagem de sucesso e redireciona com JavaScript
    echo "<script>
        alert('Alteração realizada com sucesso!');
        window.location.href = '" . ($user['TIPO'] === 'organizador' ? 'PerfilOrga.php' : 'PerfilUsu.php') . "';
    </script>";

    exit();
}

// Fecha a conexão
$conexao->close();
?>
