<?php

namespace App\Services;

use App\Modules\Contracts\ContratoPagamentoGateway;
use App\Modules\Contracts\DTOs\GerarCobrancaPixComVencimentoDto;
use App\Modules\Contracts\DTOs\PagamentoGatewayConfigDto;
use App\Modules\Contracts\DTOs\RespostaCobrancaPixComVencimentoDto;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class BancoDoBrasilGateway implements ContratoPagamentoGateway
{
    private string $clientId;
    private string $clientSecret;
    private string $gwDevAppKey;
    private string $ambiente;
    private string $basicAuth;
    private ?string $accessToken = null;

    public function definirConfiguracao(PagamentoGatewayConfigDto $config): self
    {
        $this->clientId = $config->clientId;
        $this->clientSecret = $config->clientSecret;
        $this->gwDevAppKey = $config->gwDevAppKey;
        $this->basicAuth = $config->basicAuth;
        $this->ambiente = $config->ambiente;

        return $this;
    }

    /**
     * Gera cobrança PIX
     */
    public function gerarCobrancaPixComVencimento(GerarCobrancaPixComVencimentoDto $data): RespostaCobrancaPixComVencimentoDto
    {
        $this->autenticar();

        $url = $this->getBaseApiUrl() . '/pix/v2/cobv/' . $data->gerarTxId();
        $payload = $this->payloadCobrancaPixComVencimento($data);
        $client = new Client;

        Log::info('Iniciando geração de cobrança Pix', [
            'txid' => $data->gerarTxId(),
            'url' => $url,
            'payload' => $payload,
        ]);

        try {
            $response = $client->put($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                    'gw-dev-app-key' => $this->gwDevAppKey,
                ],
                'http_errors' => false,
                'json' => $payload,
            ]);

            $decoded = json_decode($response->getBody()->getContents(), true);

            // todo: Pensar em uma forma elegante de lidar com erros desse service
            if ($response->getStatusCode() >= 400) {
                Log::warning('Erro ao gerar cobrança Pix', [
                    'txid' => $data->gerarTxId(),
                    'status_code' => $response->getStatusCode(),
                    'response' => $decoded,
                ]);
                throw new Exception('Erro do Gateway: ' . ($decoded['detail'] ?? 'Erro desconhecido'));
            }

            Log::info('Cobrança Pix gerada com sucesso', [
                'txid' => $decoded['txid'] ?? null,
                'status' => $decoded['status'] ?? null,
            ]);

            return RespostaCobrancaPixComVencimentoDto::from($decoded);
        } catch (RequestException $e) {
            Log::error('Falha ao gerar cobrança Pix', [
                'txid' => $data->gerarTxId(),
                'mensagem' => $e->getMessage(),
            ]);

            throw new Exception('Erro ao gerar PIX: ' . $e->getMessage());
        }
    }

    /**
     * Autentica e salva o token
     */
    private function autenticar(): void
    {
        if ($this->accessToken) {
            return;
        }

        $url = $this->getBaseAuthUrl() . '/oauth/token';
        $client = new Client;

        try {
            $response = $client->post($url . '?grant_type=client_credentials', [
                'headers' => [
                    'Authorization' => 'Basic ' . $this->basicAuth,
                ],
                'http_errors' => false,
            ]);

            $decoded = json_decode($response->getBody()->getContents(), true);

            // todo: Pensar em uma forma elegante de lidar com erros desse service
            if ($response->getStatusCode() >= 400) {
                Log::warning('Erro ao gerar cobrança Pix', [
                    'status_code' => $response->getStatusCode(),
                    'response' => $decoded['detail'],
                ]);
                throw new Exception('Erro do Gateway: ' . ($decoded['detail'] ?? 'Erro desconhecido'));
            }

            $this->accessToken = $decoded['access_token'];
        } catch (RequestException $e) {
            $errorBody = '';
            if ($e->hasResponse()) {
                $errorBody = (string) $e->getResponse()->getBody();
            }

            throw new Exception(
                'Erro na autenticação BB: ' . $e->getMessage() .
                ($errorBody ? ' | Body: ' . $errorBody : '')
            );
        }
    }

    private function payloadCobrancaPixComVencimento(GerarCobrancaPixComVencimentoDto $data)
    {
        $payload = [
            'calendario' => [
                'dataDeVencimento' => $data->dataDeVencimento,
                'validadeAposVencimento' => $data->validadeAposVencimento,
            ],
            'devedor' => [
                'nome' => $data->nome,
            ],
            'valor' => [
                'original' => $data->valor,
            ],
            'chave' => $data->chave,
            'solicitacaoPagador' => $data->solicitacaoPagador,
        ];

        if ($data->cpf) {
            $payload['devedor']['cpf'] = $data->cpf;
        } elseif ($data->cnpj) {
            $payload['devedor']['cnpj'] = $data->cnpj;
        }

        if (! empty($data->logradouro)) {
            $payload['devedor']['logradouro'] = $data->logradouro;
            $payload['devedor']['cidade'] = $data->cidade;
            $payload['devedor']['uf'] = $data->uf;
            $payload['devedor']['cep'] = $data->cep;
        }

        if (! empty($data->multa)) {
            $payload['valor']['multa'] = $data->multa;
        }

        if (! empty($data->juros)) {
            $payload['valor']['juros'] = $data->juros;
        }

        if (! empty($data->desconto)) {
            $payload['valor']['desconto'] = $data->desconto;
        }

        if (! empty($data->infoAdicionais)) {
            $payload['infoAdicionais'] = $data->infoAdicionais;
        }

        return $payload;
    }

    /**
     * URL de autenticação dependendo do ambiente
     */
    private function getBaseAuthUrl(): string
    {
        if (App::environment('local')) {
            return 'https://oauth.hm.bb.com.br';
        }

        return $this->ambiente === 'prod'
            ? 'https://oauth.bb.com.br'
            : 'https://oauth.hm.bb.com.br';
    }

    /**
     * URL da api dependendo do ambiente
     */
    private function getBaseApiUrl(): string
    {
        if (App::environment('local')) {
            return 'https://api.hm.bb.com.br';
        }

        return $this->ambiente === 'prod'
            ? 'https://api.bb.com.br'
            : 'https://api.hm.bb.com.br';
    }
}
