<?php

namespace App\Modules\Contracts;

interface ContratoPagamentoGateway
{
    public function gerarCobrancaPix(float $valor, string $descricao): string;
}
