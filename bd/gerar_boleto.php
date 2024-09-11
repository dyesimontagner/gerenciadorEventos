<?php
require '../libs/vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;
use Dompdf\Dompdf;
use Dompdf\Options;
// echo "<pre>";
// print_r($_GET);
// echo "</pre>";
// exit;
// Pega os parâmetros da URL
$nomeOrganizador = isset($_GET['nome']) ? $_GET['nome'] : 'Organizadora do Evento';
$valor = isset($_GET['valor']) ? $_GET['valor'] : '0.00';
$vencimento = isset($_GET['vencimento']) ? $_GET['vencimento'] : date('Y-m-d');
$evento = isset($_GET['evento']) ? $_GET['evento'] : 'exemplo de evento';
// Instanciar o gerador de código de barras
try {
    $generator = new BarcodeGeneratorPNG();
    $barcode = $generator->getBarcode('1234.5678 9012.3456 7890.1234 5678.901234 5678.90123 45', $generator::TYPE_CODE_128, 2, 150); // Ajuste a largura e altura conforme necessário
    $barcodeBase64 = base64_encode($barcode); // Converte a imagem para base64
} catch (Exception $e) {
    die('Erro ao gerar o código de barras: ' . $e->getMessage());
}

// Função para converter uma imagem para base64
function imageToBase64($filePath) {
    if (file_exists($filePath)) {
        $imageData = file_get_contents($filePath);
        return base64_encode($imageData);
    }
    return false;
}

// Caminho para a imagem do logo
$logoPath = __DIR__ . '/../imagens/logo.jpg'; 
$logoBase64 = imageToBase64($logoPath);
if (!$logoBase64) {
    die('Não foi possível carregar a imagem do logo. Caminho: ' . $logoPath);
}

// Configuração do Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', false);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);


$html = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }
        .boleto {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 12px;
        }
        .boleto .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .boleto .logo-banco {
            width: 120px;
            height: auto;
            margin-right: 20px;
        }
        .boleto .title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 0;
            line-height: 1.2;
            flex-grow: 1;
            text-align: right;
        }
        .boleto .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .boleto .info-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .boleto .info-table td.label {
            background: #f0f0f0;
            font-weight: bold;
        }
        .boleto .info-table td.value {
            background: #fff;
        }
        .boleto .codigo-barras {
            text-align: center;
            margin: 20px 0;
        }
        .boleto .codigo-barras img {
            width: 100%;
            max-width: 400px;
        }
        .boleto .codigo-barras-texto {
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
        }
        .boleto .footer {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class='boleto'>
        <div class='header'>
            <img src='data:image/jpg;base64, $logoBase64' class='logo-banco' alt='Logo Banco'>
        </div>
        <h1 class='title'>BANCO DO BRASIL SA</h1>
        <table class='info-table'>
            <tr>
                <td class='label'>Beneficiário:</td>
                <td class='value'>$nomeOrganizador</td>
            </tr>
            <tr>
                <td class='label'>Agência/Código do Beneficiário:</td>
                <td class='value'>1234/567890</td>
            </tr>
            <tr>
                <td class='label'>Data de Vencimento:</td>
                <td class='value'>" . date('d/m/Y', strtotime($vencimento)) . "</td>
            </tr>
            <tr>
                <td class='label'>Nosso Número:</td>
                <td class='value'>1234567890123456789012345678901234</td>
            </tr>
        </table>
        <div class='info'>
             <p><strong>Nome do Evento:</strong> $evento</p>
            <p><strong>Valor:</strong> R$ $valor</p>
        </div>
        <div class='codigo-barras'>
            <img src='data:image/png;base64, $barcodeBase64' alt='Código de Barras'>
        </div>
        <div class='codigo-barras-texto'>
            <p>1234.5678 9012.3456 7890.1234 5678.901234 5678.90123 45</p>
        </div>
        <div class='footer'>
            <p>Instruções de pagamento e outras informações relevantes para o boleto.</p>
        </div>
    </div>
</body>
</html>
";

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("boleto_simulado.pdf", ["Attachment" => false]);
exit;
?>
