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
/*
    // Processa o upload da imagem
    if (isset($_FILES['imagem_evento'])) {
        $imagem_nome = $_FILES['imagem_evento']['name'];
        $imagem_tmp = $_FILES['imagem_evento']['tmp_name'];
        $imagem_tamanho = $_FILES['imagem_evento']['size'];
        $imagem_tipo = $_FILES['imagem_evento']['type'];

        // Define o diretório de upload
        $upload_dir = 'assets/img';
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
*/ 

    // Exemplo de chamada à procedure (ajustar conforme sua necessidade):
    $sql = "CALL SP_EVENTOIA(?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        // No caso de uma inserção, PID_EVENTO será 0
        $pid_evento = 0;

        $stmt->bind_param("isssss", 
            $pid_evento,         // ID do evento (0 para inserção)
            $nome_evento,        // Nome do evento
            $data_inicio,        // Data de início
            $data_fim,           // Data de fim
            $local,              // Local do evento
            $descricao_evento    // Descrição do evento
        );

        if ($stmt->execute()) {
            //pega o último id do evento
            $result = $conn->query("SELECT MAX(id_evento) AS id_evento FROM evento");
            if ($result && $row = $result->fetch_assoc()) {
                $last_evento_id = $row['id_evento'];
            } else {
                echo "Erro ao obter o ID do evento.";
            }

            $sql_ingresso = "CALL SP_INGRESSOSIA(?, ?, ?, ?)";
            //$sql_ingresso = "CALL SP_INGRESSOSIA(?, ?, ?, ?, ?)";
            if ($stmt_ingresso = $conn->prepare($sql_ingresso)) {
                $pid_ingresso = 0;
                //$stmt_ingresso->bind_param("iiids", 
                $stmt_ingresso->bind_param("iids",
                    $pid_ingresso,         // ID do ingresso (0 para inserção)
                    $last_evento_id,        // ID do evento (relacionado)
                    $preco_ingresso,       // Preço do ingresso
                    $categoria_ingresso,    // Categoria do ingresso
                    //$quantidade_ingressos // Quantidade de ingressos
                );
                if ($stmt_ingresso->execute()) {
                    echo "Evento e ingressos cadastrados com sucesso!";
                } else {
                    echo "Erro ao cadastrar ingressos: " . $stmt_ingresso->error;
                }
            }
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