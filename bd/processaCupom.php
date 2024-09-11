
<?php
include('conexao.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura e valida os dados do formulário
    $id_cupom = isset($_POST['id_cupom']) ? $_POST['id_cupom'] : null;
    $codigo = isset($_POST['codigo']) ? $_POST['codigo'] : null;
    $desconto = isset($_POST['desconto']) ? $_POST['desconto'] : null;
    $validade = isset($_POST['validade']) ? $_POST['validade'] : null;
    $quant = isset($_POST['quant']) ? (int)$_POST['quant'] : 0;
    $retirar = isset($_POST['retirar']) ? (int)$_POST['retirar'] : 0;

    if ($codigo && $desconto !== null && $validade) {
        if (!empty($id_cupom) && $id_cupom !== null) {
            // Atualiza o cupom existente
            $query = "SELECT RESTANTES FROM cupom_desconto WHERE ID_CUPOM = ?";
            $stmt = $conexao->prepare($query);
            $stmt->bind_param('i', $id_cupom);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $cupomAtual = $result->fetch_assoc();
                $restantes = $cupomAtual['RESTANTES'];

                $novos_restantes = $restantes + $quant - $retirar;
                $novos_restantes = max($novos_restantes, 0); // Garante que não fique negativo

                // Atualiza o cupom no banco de dados
                $query = "UPDATE cupom_desconto SET CODIGO = ?, DESCONTO = ?, VALIDADE = ?, QUANT = ?, RESTANTES = ? WHERE ID_CUPOM = ?";
                $stmt = $conexao->prepare($query);
                $stmt->bind_param('sisiii', $codigo, $desconto, $validade, $quant, $novos_restantes, $id_cupom);

                if ($stmt->execute()) {
                    $mensagem = 'Cupom atualizado com sucesso!';
                } else {
                    $mensagem = 'Erro ao atualizar o cupom: ' . $conexao->error;
                }
            } else {
                $mensagem = 'Cupom não encontrado.';
            }
        } else {
            // Insere um novo cupom
            $query = "INSERT INTO cupom_desconto (CODIGO, DESCONTO, VALIDADE, QUANT, RESTANTES) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conexao->prepare($query);
            $stmt->bind_param('sisii', $codigo, $desconto, $validade, $quant, $quant);

            if ($stmt->execute()) {
                $mensagem = 'Novo cupom cadastrado com sucesso!';
            } else {
                $mensagem = 'Erro ao cadastrar o cupom: ' . $conexao->error;
            }
        }
    } else {
        $mensagem = 'Dados inválidos fornecidos.';
    }

    // Redireciona com uma mensagem de sucesso ou erro
    header("Location: http://localhost/gerEventos/bd/PerfilOrga.php?mensagem=" . urlencode($mensagem));
    exit();
}

// Fecha a conexão com o banco de dados
$conexao->close();
?>