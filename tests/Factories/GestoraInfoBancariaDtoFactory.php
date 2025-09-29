<?php

namespace Tests\Factories;

use App\Modules\Gestora\DTOs\GestoraInfoBancariaDto;

class GestoraInfoBancariaDtoFactory
{
    public static function make(array $overrides = []): GestoraInfoBancariaDto
    {
        $defaults = [
            'id' => 1,
            'adm_gestora_id' => 1,
            'developer_application_key' => 'fake-dev-app-key',
            'client_id' => 'fake-client-id',
            'client_secret' => 'fake-client-secret',
            'ambiente' => 'homologacao',
            'chave_pix' => '00000000000191',
            'orgao' => 'ORGAO TESTE',
            'cnpj' => '00.000.000/0001-91',
            'aplicacao' => 'APLICACAO TESTE',
            'id_aplicacao' => 99,
            'versao_api' => '1.0.0',
            'numero_convenio' => '1234567',
            'cert_client' => null,
            'cert_key' => null,
            'cert_ca' => null,
        ];

        $data = array_merge($defaults, $overrides);

        return new GestoraInfoBancariaDto(...$data);
    }
}