<?php
session_start(); // Inicia a sessão

include_once('conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

// Obtém o ID do usuário logado
$user_id = $_SESSION['user_id'];

// Verifica se o botão de exclusão foi pressionado
if (isset($_POST['deletar'])) {
    // Prepara a consulta SQL para excluir o usuário
    $sqlDelete = "DELETE FROM usuario WHERE ID_USUARIO = ?";
    if ($stmt = $conexao->prepare($sqlDelete)) {
        $stmt->bind_param('i', $user_id);

        if ($stmt->execute()) {
            // Se a exclusão foi bem-sucedida, destrói a sessão
            session_destroy();

            // Exibe um alerta de sucesso e redireciona para a página de login
            echo "<script>
                    alert('Conta excluída com sucesso!');
                    window.location.href = 'login.html';
                  </script>";
            exit();
        } else {
            echo "Erro ao excluir o usuário: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta de exclusão.";
    }
} else {
    echo "Nenhuma solicitação de exclusão recebida.";
}

$conexao->close();
?>
