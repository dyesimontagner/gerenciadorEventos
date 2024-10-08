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
    echo "Usuário não encontrado.";
    exit();
}

// Obtém as compras do usuário com informações do evento e categoria
$sql_compras = "
    SELECT c.ID_COMPRA, e.NOME AS nome_evento, e.IMAGEM AS imagem_evento, cat.NOME AS categoria, c.VALOR_TOTAL, c.DATA_COMPRA, c.QUANT
    FROM compra c
    INNER JOIN ingressos i ON i.ID_INGRESSO = c.LKINGRESSO
    INNER JOIN evento e ON e.ID_EVENTO = i.LKEVENTO
    INNER JOIN categorias cat ON i.LKCATEGORIA = cat.ID_CATEGORIA
    WHERE c.LKUSUARIO = ?
";
$stmt_compras = $conexao->prepare($sql_compras);
$stmt_compras->bind_param('i', $user_id);
$stmt_compras->execute();
$compras_result = $stmt_compras->get_result();

// Fecha a conexão
$stmt->close();
$stmt_compras->close();
$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>
    <link rel="stylesheet" href="perfil.css">
</head>
<body>
    <div class="perfil-container">
        <!-- Seção da Foto de Perfil -->
        <div class="profile-photo">
            <img src="foto_perfil.jpg" alt="Foto de Perfil" class="profile-img">
        </div>
        <form action="ExcluirUsuario.php" method="post">
        <h2>Perfil do Usuário</h2>
        <div class="user-info">
            <h3>Informações Pessoais</h3>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($user['NOME']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['EMAIL']); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($user['TELEFONE']); ?></p>
            <p><strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($user['DATA_NASCIMENTO']); ?></p>
            <p><strong>Tipo:</strong> <?php echo htmlspecialchars($user['TIPO']); ?></p>
            <p><strong>CPF:</strong> <?php echo htmlspecialchars($user['CPF_CNPJ']); ?></p>
        </div>

        <div class="user-actions">
            <form action="ExcluirUsuario.php" method="post">
                <button type="button" onclick="window.location.href='CampoEditar.php'">Editar Informações</button>
                <button type="submit" name="deletar" style="background-color: red; color: white;">Excluir Conta</button>
            </form>
        </div>

        <div class="user-purchases">
            <h3>Compras Recentes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Imagem do Evento</th>
                        <th>Nome do Evento</th>
                        <th>Categoria</th>
                        <th>Valor Total</th>
                        <th>Data da Compra</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($compra = $compras_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo "<img src='data:image/jpeg;base64," . base64_encode($compra['imagem_evento']) . "' alt='Imagem do evento' class='event-img'>";?></td>
                            <td><?php echo htmlspecialchars($compra['nome_evento']); ?></td>
                            <td><?php echo htmlspecialchars($compra['categoria']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($compra['VALOR_TOTAL'], 2, ',', '.')); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($compra['DATA_COMPRA']))); ?></td>
                            <td><?php echo htmlspecialchars($compra['QUANT']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Botão Comprar Evento -->
        <div class="comprar-evento">
            <button type="button" onclick="window.location.href='todosOsEventos.html'">Comprar Evento</button>
        </div>
    </div>
</body>
</html>