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

// Prepara e executa a consulta para pegar as informações do usuário e da empresa
$sql = "SELECT u.NOME, u.EMAIL, u.TELEFONE, u.DATA_NASCIMENTO, u.TIPO, u.CPF_CNPJ, o.CNPJ_EMPRESA 
        FROM usuario u 
        LEFT JOIN organizador o ON u.ID_USUARIO = o.ID_ORGANIZADOR
        WHERE u.ID_USUARIO = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se o usuário foi encontrado
if ($result->num_rows > 0) {
    // Pega os dados do usuário e da empresa
    $user = $result->fetch_assoc();
} else {
    echo "Usuário não encontrado.";
    exit();
}

// Prepara e executa a consulta para pegar os eventos recentes
$sql_events = "SELECT imagem, nome as nome_evento, data_inicio as data_evento, local ,descricao FROM evento WHERE lkorganizador = ? ORDER BY data_evento DESC";
$stmt_events = $conexao->prepare($sql_events);
$stmt_events->bind_param('i', $user_id);
$stmt_events->execute();
$result_events = $stmt_events->get_result();

// Fecha a conexão
$stmt->close();
$stmt_events->close();
$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Organizador</title>
    <link rel="stylesheet" href="perfilOrganizador.css">
</head>
<body>
    <div class="perfil-container">
        <!-- Seção da Foto de Perfil -->
        <div class="profile-photo">
            <img src="foto_perfil.jpg" alt="Foto de Perfil" class="profile-img">
        </div>
        <form action="ExcluirUsuario.php" method="post">
        <h2>Perfil do Organizador</h2>
        <div class="user-info">
            <h3>Informações Pessoais</h3>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($user['NOME']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['EMAIL']); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($user['TELEFONE']); ?></p>
            <p><strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($user['DATA_NASCIMENTO']); ?></p>
            <p><strong>Tipo:</strong> <?php echo htmlspecialchars($user['TIPO']); ?></p>
            <p><strong>CPF:</strong> <?php echo htmlspecialchars($user['CPF_CNPJ']); ?></p>
            <p><strong>Empresa:</strong> <?php echo htmlspecialchars($user['CNPJ_EMPRESA']); ?></p>
        </div>

        <!-- Botões de Ação: Editar, Excluir e Criar Evento -->
        <div class="user-actions">
            <button type="button" onclick="window.location.href='AlteracoesOrganizador.php'">Editar Informações</button>
            <button type="button" onclick="window.location.href='cadastroEvento.html'">Criar Evento</button>
            <button type="button" onclick="window.location.href='eventos_organizador.php'">Relatório de Eventos</button>
            <button type="submit" name="deletar" style="background-color: red; color: white;">Excluir Conta</button>
        </div>

        <div class="user-purchases">
            <h3>Eventos Recentes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Evento</th>
                        <th>Data</th>
                        <th>Local</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Verifica se há resultados e os exibe
                    if ($result_events->num_rows > 0) {
                        while ($row = $result_events->fetch_assoc()) {
                            // Formata o valor
                            // Formata a data no formato brasileiro
                            $data_formatada = date('d/m/Y', strtotime($row['data_evento']));
                            echo "<tr>";
                            echo "<td><img src='" . htmlspecialchars($row['imagem']) . "' alt='Imagem do evento' class='event-img'></td>";
                            echo "<td>" . htmlspecialchars($row['nome_evento']) . "</td>";
                            echo "<td>" . $data_formatada . "</td>";
                            echo "<td>" . htmlspecialchars($row['local']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['descricao']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Nenhum evento encontrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Botão Comprar Evento -->
        <div class="comprar-evento">
            <button type="button" onclick="window.location.href='comprar_evento.html'">Comprar Evento</button>
        </div>
    </div>
</body>
</html>
