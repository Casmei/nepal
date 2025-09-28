<?php

namespace App\Modules\Contracts\DTOs;

use Exception;
use Illuminate\Support\Facades\Config;

class GerarCobrancaPixComVencimentoDto
{
    public function __construct(
        public string $dataDeVencimento,
        public int $validadeAposVencimento,
        public ?string $cpf,
        public ?string $cnpj,
        public string $nome,
        public ?string $logradouro,
        public ?string $cidade,
        public ?string $uf,
        public ?string $cep,
        public string $valor,
        public ?array $multa,
        public ?array $juros,
        public ?array $desconto,
        public string $chave,
        public string $solicitacaoPagador,
        public array $infoAdicionais = []
    ) {}

    /**
     * Gera um txId de 26 a 35 caracteres
     * - Usa últimos 4 dígitos do CPF ou CNPJ para debugging
     * - Usa HMAC-SHA256 com segredo definido em services.php
     * - Encoda em base64url
     */
    public function gerarTxId(): string
    {
        $id = $this->cpf ?? $this->cnpj ?? '';
        $timestamp = (string) round(microtime(true) * 1000);

        $secret = Config::get('services.dados_bancarios.bb.pix_txid_secret');

        if (! $secret) {
            throw new Exception('PIX_TXID_SECRET não configurado em services.php/.env');
        }

        $raw = $id . '|' . $timestamp;

        // gera hash em hexadecimal (só [0-9a-f])
        $hash = hash_hmac('sha256', $raw, $secret, false);

        // últimos 4 dígitos do id para facilitar rastreio
        $suffix = $id ? substr($id, -4) : '0000';

        // junta sufixo + hash
        $txid = $suffix . $hash;

        // garante que fique entre 26 e 35 caracteres
        if (strlen($txid) > 35) {
            $txid = substr($txid, 0, 35);
        } elseif (strlen($txid) < 26) {
            $txid = str_pad($txid, 26, '0');
        }

        return $txid;
    }
}
