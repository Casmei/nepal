<?php

namespace App\Modules\Contracts\DTOs;

class PagamentoGatewayConfigDto
{
    public function __construct(
        public string $clientId,
        public string $clientSecret,
        public string $gwDevAppKey,
        public string $basicAuth,
        public string $ambiente = 'homolog',
        public ?string $chavePix = null
    ) {}

    public static function from(array|object $data): self
    {
        return new self(
            $data['client_id'] ?? $data->clientId,
            $data['client_secret'] ?? $data->clientSecret,
            $data['developer_application_key'] ?? $data->gwDevAppKey,
            $data['ambiente'] ?? $data->ambiente ?? 'homolog',
            $data['chave_pix'] ?? $data->chavePix ?? null
        );
    }
}
