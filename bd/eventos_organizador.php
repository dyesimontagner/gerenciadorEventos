<?php
session_start();
include('conexao.php'); // Inclua o arquivo de conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para o login
    header('Location: login.php');
    exit();
}

// Obtém o ID do usuário da sessão
$user_id = $_SESSION['user_id'];

// Prepare e execute a consulta
$sql = "SELECT nome as nome_evento, data_inicio as data_evento, qtd as ingressos_vendidos, total as rendimento, preco FROM vw_relatorio WHERE LKORGANIZADOR = ? ";
$stmt = $conexao->prepare($sql);

if ($stmt === false) {
    die("Erro ao preparar a consulta: " . $conexao->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Eventos</title>
    <link rel="stylesheet" href="eventos_organizador.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Relatório de Eventos</h1>
            <hr class="divider">
        </header>
        <table>
            <thead>
                <tr>
                    <th>Nome do Evento</th>
                    <th>Data</th>
                    <th>Preço</th>
                    <th>Ingressos Vendidos</th>
                    <th>Rendimento</th>
                </tr>
            </thead>
            <tbody>
                <?php
// Verifica se há resultados e os exibe
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Formata os valores de preço e rendimento
                            $preco_formatado = "R$ " . number_format($row['preco'], 2, ',', '.');
                            $rendimento_formatado = "R$ " . number_format($row['rendimento'], 2, ',', '.');

                            // Converte e formata a data para o formato brasileiro
                            $data_evento = new DateTime($row['data_evento']);
                            $data_evento_formatada = $data_evento->format('d/m/Y');
                            
                            // Verifica se ingressos vendidos está vazio e define como 0 se necessário
                            $ingressos_vendidos = empty($row['ingressos_vendidos']) ? '0' : $row['ingressos_vendidos'];

                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['nome_evento']) . "</td>";
                            echo "<td>" . htmlspecialchars($data_evento_formatada) . "</td>";
                            echo "<td>" . $preco_formatado . "</td>";
                            echo "<td>" . htmlspecialchars($ingressos_vendidos) . "</td>";
                            echo "<td>" . $rendimento_formatado . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Nenhum evento encontrado.</td></tr>";
                    }
                
                // Fecha a conexão
                $stmt->close();
                $conexao->close();
                ?>
            </tbody>
        </table>
        <div class="button-container">
            <button class="back-btn" onclick="window.location.href='perfilOrga.php'">Voltar</button>
            <button class="print-btn" onclick="window.print()">Imprimir Relatório</button>
        </div>
    </div>
</body>
</html>
