<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>DAM/IPTU - Documento de Arrecadação Municipal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 15px;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 14pt;
            margin: 0;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .data-table th {
            background-color: #f0f0f0;
            width: 30%;
        }

        .pix-section {
            border: 2px dashed #000;
            padding: 15px;
            margin-top: 20px;
            text-align: center;
        }

        .pix-code {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 10px;
            word-break: break-all;
        }

        .instructions {
            margin-top: 20px;
            font-size: 9pt;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>DOCUMENTO DE ARRECADAÇÃO MUNICIPAL (DAM/IPTU)</h1>
            <p>ID Documento: <?php echo htmlspecialchars($iptuDam->id); ?></p>
        </div>

        <table class="data-table">
            <tr>
                <th>Tipo de Documento</th>
                <td><?php echo htmlspecialchars($iptuDam->tipo); ?> (Parcela:
                    <?php echo htmlspecialchars($iptuDam->numero_parcela ?? 'Única'); ?>)
                </td>
            </tr>
            <tr>
                <th>Competência / Mês</th>
                <td><?php echo htmlspecialchars($iptuDam->competencia); ?> /
                    <?php echo htmlspecialchars($iptuDam->mes_competencia); ?>
                </td>
            </tr>
            <tr>
                <th>Data de Vencimento</th>
                <td><strong><?php echo htmlspecialchars(date('d/m/Y', strtotime($iptuDam->data_vencimento))); ?></strong>
                </td>
            </tr>
            <tr>
                <th>Valor Total</th>
                <td>R$ <?php echo htmlspecialchars(number_format($iptuDam->valor, 2, ',', '.')); ?></td>
            </tr>
            <tr>
                <th>Demonstrativo</th>
                <td><?php echo htmlspecialchars($iptuDam->demonstrativo); ?></td>
            </tr>
        </table>

        <!-- Detalhes de Acréscimos/Descontos -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Desconto</th>
                    <th>Acréscimo</th>
                    <th>Juros</th>
                    <th>Multa</th>
                    <th>Mora</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>R$ <?php echo htmlspecialchars(number_format($iptuDam->desconto, 2, ',', '.')); ?></td>
                    <td>R$ <?php echo htmlspecialchars(number_format($iptuDam->acrescimo, 2, ',', '.')); ?></td>
                    <td>R$ <?php echo htmlspecialchars(number_format($iptuDam->juros, 2, ',', '.')); ?></td>
                    <td>R$ <?php echo htmlspecialchars(number_format($iptuDam->multa, 2, ',', '.')); ?></td>
                    <td>R$ <?php echo htmlspecialchars(number_format($iptuDam->mora, 2, ',', '.')); ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Seção de Pagamento PIX -->
        <div class="pix-section">
            <h2>PAGAMENTO VIA PIX</h2>
            <p>Utilize o código abaixo (Copia e Cola) ou escaneie o QR Code (simulado):</p>

            <!-- Atenção: O Dompdf não renderiza QR Codes diretamente.
                 Se você precisar de um QR Code visível, use uma biblioteca
                 que gere o SVG ou a imagem do QR Code a partir do $iptuDam->pix_qr_code
                 antes de injetar na view. Aqui, exibimos apenas o código.
            -->
            <img src="https://placehold.co/150x150/000/fff?text=QR+Code+PIX" alt="QR Code PIX Simulado"
                style="margin: 10px;" />

            <p><strong>CÓDIGO PIX (COPIA E COLA):</strong></p>
            <div class="pix-code">
                <?php echo htmlspecialchars($iptuDam->pix_qr_code); ?>
            </div>
        </div>

        <!-- Instruções de Pagamento -->
        <div class="instructions">
            <strong>Instruções de Pagamento:</strong>
            <p><?php echo nl2br(htmlspecialchars($iptuDam->instrucao_pagamento)); ?></p>
        </div>
    </div>
</body>

</html>