<?php

namespace App\Modules\Contracts;

use App\Modules\Contracts\DTOs\GerarCobrancaPixComVencimentoDto;
use App\Modules\Contracts\DTOs\PagamentoGatewayConfigDto;
use App\Modules\Contracts\DTOs\RespostaCobrancaPixComVencimentoDto;

interface ContratoPagamentoGateway
{
    public function definirConfiguracao(PagamentoGatewayConfigDto $config): self;

    public function gerarCobrancaPixComVencimento(GerarCobrancaPixComVencimentoDto $data): RespostaCobrancaPixComVencimentoDto;
}
