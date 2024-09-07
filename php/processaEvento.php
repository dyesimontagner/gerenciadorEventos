<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Dados do evento
    $nome_evento = $_POST['nome_evento'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $local = $_POST['local'];
    $descricao_evento = $_POST['descricao_evento'];

    // Dados dos ingressos
    $quantidade_ingressos = $_POST['quantidade_ingressos'];
    $preco_ingresso = $_POST['preco_ingresso'];
    $categoria_ingresso = $_POST['categoria_ingresso'];

    // Processa o upload da imagem
    if (isset($_FILES['imagem_evento'])) {
        $imagem_nome = $_FILES['imagem_evento']['name'];
        $imagem_tmp = $_FILES['imagem_evento']['tmp_name'];
        $imagem_tamanho = $_FILES['imagem_evento']['size'];
        $imagem_tipo = $_FILES['imagem_evento']['type'];

        // Define o diretório de upload
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Cria o diretório se não existir
        }

        // Verifica o tipo de arquivo (opcional)
        $extensao_permitida = ['jpg', 'jpeg', 'png', 'gif'];
        $extensao_imagem = pathinfo($imagem_nome, PATHINFO_EXTENSION);
        
        if (in_array($extensao_imagem, $extensao_permitida)) {
            // Gera um nome único para a imagem e salva no diretório
            $novo_nome_imagem = uniqid() . '.' . $extensao_imagem;
            $destino_imagem = $upload_dir . $novo_nome_imagem;

            if (move_uploaded_file($imagem_tmp, $destino_imagem)) {
                echo "Imagem enviada com sucesso!";
            } else {
                echo "Erro ao enviar a imagem.";
            }
        } else {
            echo "Tipo de imagem inválido. Permitido: jpg, jpeg, png, gif.";
        }
    }

    // Aqui você pode inserir os dados no banco de dados e chamar a procedure
    // Exemplo de chamada à procedure (ajustar conforme sua necessidade):
    $sql = "CALL add_evento(?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssids", $nome_evento, $data_inicio, $data_fim, $local, $descricao_evento, $novo_nome_imagem, $quantidade_ingressos, $preco_ingresso, $categoria_ingresso);
        if ($stmt->execute()) {
            echo "Evento e ingressos cadastrados com sucesso!";
        } else {
            echo "Erro ao cadastrar evento: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro na preparação da query: " . $conn->error;
    }

    $conn->close();
}
?>