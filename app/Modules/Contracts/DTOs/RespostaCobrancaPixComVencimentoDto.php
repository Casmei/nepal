<?php

namespace App\Modules\Contracts\DTOs;

class RespostaCobrancaPixComVencimentoDto
{
    public function __construct(
        public readonly string $txid,
        public readonly string $status,
        public readonly array $calendario,
        public readonly array $devedor,
        public readonly array $recebedor,
        public readonly array $valor,
        public readonly string $chave,
        public readonly string $solicitacaoPagador,
        public readonly string $pixCopiaECola,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            txid: $data['txid'],
            status: $data['status'],
            calendario: $data['calendario'],
            devedor: $data['devedor'],
            recebedor: $data['recebedor'],
            valor: $data['valor'],
            chave: $data['chave'],
            solicitacaoPagador: $data['solicitacaoPagador'],
            pixCopiaECola: $data['pixCopiaECola'],
        );
    }
}
