<?php

namespace App\Services;

use App\Modules\Contracts\ContratoPagamentoGateway;

class BancoDoBrasilGateway implements ContratoPagamentoGateway
{
    public function gerarCobrancaPix(float $valor, string $descricao): string
    {
        return 'codigo_pix_gerado';
    }
}
