<?php

namespace Tests\Factories;

use App\Modules\Contabilidade\DTOs\IptuDamDto;

class IptuDamDtoFactory
{
    public static function make(array $overrides = []): IptuDamDto
    {
        $defaults = [
            'id' => 1,
            'iptu_calculo_id' => 123,
            'tipo' => 'UNICO',
            'numero_parcela' => null,
            'data_vencimento' => '2025-01-01',
            'competencia' => '2025-01',
            'mes_competencia' => 1,
            'valor' => '100.00',
            'demonstrativo' => 'teste',
            'desconto' => '0',
            'acrescimo' => '0',
            'juros' => '0',
            'multa' => '0',
            'mora' => '0',
            'iptu_calculo_rotina_id' => null,
            'instrucao_pagamento' => null,
            'pix_qr_code' => null,
            'caminho_carne_pdf' => 'caminho/arquivo.pdf',
        ];

        $data = array_merge($defaults, $overrides);

        return new IptuDamDto(...$data);
    }
}
