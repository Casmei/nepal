<?php

namespace App\Services;

use App\Modules\Contracts\ContratoPagamentoGateway;
use App\Modules\Contracts\DTOs\GerarCobrancaPixComVencimentoDto;
use App\Modules\Contracts\DTOs\PagamentoGatewayConfigDto;
use App\Modules\Contracts\DTOs\RespostaCobrancaPixComVencimentoDto;
use DateInterval;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class BancoDoBrasilGateway implements ContratoPagamentoGateway
{
    private string $clientId;
    private string $clientSecret;
    private string $gwDevAppKey;
    private string $ambiente;
    private string $basicAuth;
    private ?string $accessToken = 'Wc9QUR63Bslr5sORPsK6bcqnxJ8daNAaFFpaEZHdmPwXpxFXUxQ617Tc3LVg1IQsbJRStrWWxvyGl00Tpfuxsw.AohU16iqmVRnbw4d6TmlrHgYhHpUB1ubAAzESYWm96wGGh-dZ0kyxP4FzcewiVgZGZNhcz6MLIN-lAldsW_0yCVmtx2PYh4JODWuOjRTFbrEaM9akxfC94awu-v0tv5ARZe1QZ3w_LYNXL9kiO279zB1aqgBX3Ot0IBZN1ae5Um8uEcEv_BjOaEKd0uMm3hp7zBykOA_mvMiwG-e7vL6F1GCRG7gUdb3tDWS6DWpyI7MnnaxKy7hUkqKirl4Xn9LGtw6MRHNnkSg-TySKfX1zVTGwOWwaBd84NBDoHAmcenlcsWkwlj7mWwF9RgAtdI1gRLMIAYqXarRjnUajypLBUy7MChAH9sZBjBamkKy-Q0eqqlhnkcmR2VRuX5MrGpA1AS7s-aAdNnTZ3UN06vFmUH8CccoZMqahWE86koFTB4pCtosT94vWh66M3YF25141N8DcFNGMbYecaHLyelg8b_7-k6M1EgWuAOvfjYihC4IDqXX4s4bm6CquY7iUYERnV3WM2n9WMzDpN0NAmjLz5W6yv_fN1z0ujexckmz4AqCi7AObjaWhjYsnkvfqCf_SmlxjE6H-qztah_Kg2uPedHAifnsG5i1l0b23AvFD6vEcUzGZT-cWgr6JLRdgSnbXP5ekygO4azlrvtNGEmU3t93QfZPv40S5krvuor7Z-yg6xVJJuQ5qfnm4LBSftXoSvW144-D3hXJRlK96LGxPhmbVYiwISrNJgBtStk86DMZPsPe3ktB54Nby6ptGHPnENvwKOq6udqYd44TVffztop6gr212YNdVLKAoam34D0hWjp2Ry2EqOjKY3kmsSc1AddUoW0uM-Ct_1koSNKW6uNCp5zsz9_G1ljah0rJRYennw9ALZPk0uh9sKqRWcYX-C6H8dZUJvmu5eoMF9vuZ0GS-ydUSWgK70cWPt58owxQLKRKWIVAcng0wK6jpxyQhlM0jORQXHWjggWnYQUzCLPV0saIOHJf5zLz488eFhRgNerQqIu6W4niMNwwfoo-jX6iEmZDc2dmNWJOXRudg3X64-GAgZVlDhWwNlXIWaZcz6JlsgoOd1v2oY3XfA19XbMI4RxwDSbWcd5qeZe6C_IlIT3NnEIYliRxjSSvIi4nzQccxlKWHssO8evgM0JEr9iu-qcL5RRebmzb25PfqpGvRnAe9oykBcAfHdh7AB8LjRtUoBdH4W9dJR4TSsa_q1pGnZn10Jry_FVEiVuvcnq-Ghy-5wxPwBYF3vA8c_S9IpRmfo3KQ7s9aYwObtrDB5oTzxm4NF8LXVJ7T4p1Jh2ZYSJkiu0Fx0G3td6YKGkG99Uk8Vy4Pp72irzWdrYe7lNPupZRKK-BuLIxZWE__V--ZudLhNYOzHrQcrSeZ3iTBsEk2B0F0dEn7Hp7NbRHQolXD_f4GD2kaheMjIsgbw.CKNr_GL3nKxdckkYhri9VzYznrH64eYeBHx_K0rrltsdg2z8lC0gErRGKPd8R_OiG7e7z_VeJlgHo019rMB99w';
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
     * Autentica e salva o token
     * todo: Validar o por que a autenticação não está sendo realizada corretamente
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

            $data = json_decode($response->getBody()->getContents(), true);
            $this->accessToken = $data['access_token'];
            $this->tokenExpiracao = (new DateTime)->add(new DateInterval('PT' . $data['expires_in'] . 'S'));
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

    /**
     * URL de autenticação dependendo do ambiente
     */
    private function getBaseAuthUrl(): string
    {
        return $this->ambiente === 'prod'
            ? 'https://oauth.bb.com.br'
            : 'https://oauth.hm.bb.com.br';
    }

    /**
     * URL da api dependendo do ambiente
     */
    private function getBaseApiUrl(): string
    {
        return $this->ambiente === 'prod'
            ? 'https://api.bb.com.br'
            : 'https://api.hm.bb.com.br';
    }
}
