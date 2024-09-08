<?php
session_start(); // Inicia a sessão

// Inclui o arquivo de conexão com o banco de dados
include('conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para o login
    header('Location: login.php');
    exit();
}

// Obtém o ID do usuário da sessão
$user_id = $_SESSION['user_id'];

// Inicializa a variável $user como um array vazio
$user = [];

// Prepara e executa a consulta para pegar as informações do usuário
$sql = "SELECT NOME, EMAIL, TELEFONE, DATA_NASCIMENTO, TIPO, CPF_CNPJ FROM usuario WHERE ID_USUARIO = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se o usuário foi encontrado
if ($result->num_rows > 0) {
    // Pega os dados do usuário
    $user = $result->fetch_assoc();
} else {
    // Usuário não encontrado, redireciona ou exibe uma mensagem
    echo "Usuário não encontrado.";
    exit();
}

// Fecha a conexão
$stmt->close();
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Informações</title>
    <link rel="stylesheet" href="EditarUsuario.css">
</head>
<body>
    <div class="alterar-container">
        <h2>Alterar Informações</h2>
        <form action="EditarUsuario.php" method="post">
            <div class="form-group">
                <label for="foto_perfil">Foto de Perfil:</label>
                <div class="photo-upload">
                    <img src="imagem_perfil_atual.jpg" alt="Foto do Perfil" class="profile-img">
                    <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
                </div>
            </div>
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($user['NOME'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['EMAIL'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($user['TELEFONE'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($user['DATA_NASCIMENTO'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($user['CPF_CNPJ'] ?? ''); ?>" required>
            </div>

            <div class="form-actions">
                <button type="submit">Salvar Alterações</button>
                <button type="button" onclick="window.location.href='PerfilUsu.php'" style="background-color: #ccc;">Cancelar</button>
            </div>
        </form>
    </div>
</body>
</html>
