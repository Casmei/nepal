<?php

namespace App\Modules\Contabilidade\DTOs;

class IptuDamDto
{
    public function __construct(
        public int $id,
        public int $iptu_calculo_id,
        public string $tipo,
        public ?int $numero_parcela,
        public string $data_vencimento,
        public string $competencia,
        public int $mes_competencia,
        public string $valor,
        public string $demonstrativo,
        public string $desconto,
        public string $acrescimo,
        public string $juros,
        public string $multa,
        public string $mora,
        public ?int $iptu_calculo_rotina_id,
        public ?string $instrucao_pagamento,
        public ?string $pix_qr_code,
        public ?string $caminho_carne_pdf
    ) {}

    public static function from(object $data): self
    {
        return new self(
            $data->id,
            $data->iptu_calculo_id,
            $data->tipo,
            $data->numero_parcela,
            $data->data_vencimento,
            $data->competencia,
            $data->mes_competencia,
            $data->valor,
            $data->demonstrativo,
            $data->desconto,
            $data->acrescimo,
            $data->juros,
            $data->multa,
            $data->mora,
            $data->iptu_calculo_rotina_id,
            $data->instrucao_pagamento,
            $data->pix_qr_code,
            $data->caminho_carne_pdf
        );
    }
}
