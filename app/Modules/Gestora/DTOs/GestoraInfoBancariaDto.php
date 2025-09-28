<?php

namespace App\Modules\Gestora\DTOs;

class GestoraInfoBancariaDto
{
    public function __construct(
        public int $id,
        public int $adm_gestora_id,
        public string $developer_application_key,
        public string $client_id,
        public string $client_secret,
        public string $ambiente,
        public string $chave_pix,
        public string $orgao,
        public string $cnpj,
        public string $aplicacao,
        public int $id_aplicacao,
        public string $versao_api,
        public ?string $numero_convenio,
        public ?string $cert_client,
        public ?string $cert_key,
        public ?string $cert_ca
    ) {}

    public static function from(object $data): self
    {
        return new self(
            $data->id,
            $data->adm_gestora_id,
            $data->developer_application_key,
            $data->client_id,
            $data->client_secret,
            $data->ambiente,
            $data->chave_pix,
            $data->orgao,
            $data->cnpj,
            $data->aplicacao,
            $data->id_aplicacao,
            $data->versao_api,
            $data->numero_convenio ?? null,
            $data->cert_client ?? null,
            $data->cert_key ?? null,
            $data->cert_ca ?? null
        );
    }

    public function gerarTokenDeAcesso(): string
    {
        return base64_encode($this->client_id . ':' . $this->client_secret);
    }
}
