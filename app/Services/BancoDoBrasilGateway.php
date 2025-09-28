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
    private ?string $accessToken = 'N8hZ2yXxzMshe16gxFFPVvXlM4qCF7xhnh7lGWRNXENfmYY8LBzfOMNywy2l4R4-8sq4QBHlYJ7JUWGOa3gO5A.C2_JJ9Ax-OmrnsNSs0aARMb8O6P_oRhJtgxxwqwDpb1LTzf8vPw3nM1LMfj2wT4GIFf1nfeC_WuA9gJr2Nv7Qo5EsJohbq6Y5quGW5c9DoMA-ikNTMfXPTLUVRrHl8wpyNuvWeaAmw0QZ6m_L51U7u8D8ZCEm_oV-erub3c4IMkKtxWgB3RVw4aYZxmxwwbt3GfutrvnByNC1yMc40EQ3L5ltvp_jj1eeme6FoUsUbFJcKQA7NFpIGms2mqoPZW2Azy2Nhx4-M3q1rc6S35LNVBsNfAUyveXEOuuHi7jkEorT-2LzlY0jpkgME841zmhU1TiEtR1QPBfdQ2DhOvgnB1XhZcevqAFL2Oic5jlZW4fh6c8v6fT4ljZOjmtE2F-SUUZ1tdhOYV932sbRNdFGG43cyuPtUitbmr49RK5QoHEgLlBCSq5u6tmxwQT3qNx10rpc1HJc1MlZmw-1OUDNj0te9-rppFLXKtRijBZeqIa1l3cfEEqKDtxT16hU0D9HmIXVfg3JgvXMn1b2fP8EeHErJALilGkh5m-4Sm7VjXO2n09yPD8Xzf7gbiCCm3Tv2eGcieOSN1GbRUsS6ctnMkW_UALbU3FkMDJ7nrZdq0yBeCyf6BpRXFKdZ_dQi_t_K32eh21eASsBP6RN_kIYRTXa4-d8YZjTk1bP0tP6shNJ2ZJ9WBDu6PqpDVEmwKWSLnrcjXTymjE0a35rHKbNHzYWvmXgrQjMUkfcApN93rFcnwQexUxv7BfjIxp4izp-k49INoYrA4akUDqzosPd309xovRj1AsUirAiMCzHhNu-2oWEb_qOk_FHFycCmilruoMleT_3Qrcwy5mxHr8NjC33YMkoi42-qhMbDPM8_unry4t02Tcn9QFwkgB3JNqvO-tEVKCcFDfvaXiEM5qpi-FhRxxIJS4LDjFpeQN7OKY1B8wORTLPD1DHCxCnLIldcgQSmCGMAmnCIIljox6QKiaN-tWIfStUf_aKNBkftT6Jb9e-1mcH9R6f597y7K6BfQxgF7jh_NjBJ8ZCVKNDdoYcpm4Y5Nb4p-xzKoYY9Oah3gvWd2_Hf_c6O4sRH8Smau0IXSOvc7bXg0r1-CSIS1iftimeF00-FHg1V-uTuAO_7n30GDtigSVTu-bbJb3b5Ey0LW7c7N84VFgWCOurTbFpZK_nY8aBy4lPBUnTlpgxKbt9cFJnuAU3BuiHtZOXK9MSlLsUEW_J7l9HM7Jui4Z0lNkTECSYnXjHyLbzYSkAzWwe0DFpQ81qiTgEfKZdTa-P6RqZInXHhisQFbEH-u_6OVh_HnX2SG5BFkZutCZgpYi8i6X6uqj1JAwa9Wn8SqZs4Z3yespTWmfG5c1kXjIvvw7sSMznL2RXBiX-ko0uRHNlbZPK0Q4kRGssEsy9i5z1Ns4MF9LqVne7vIbxw.Uwh8cJD-w4-fNKShaJxNeVdqiWrsUdJBBgW8AtroN0u0Nt1N-tC8McM3zdATobxBQpf88_fPyF6QZAKDcDjk_Q'; //todo: procurar uma forma de fazer isso funcionar
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
