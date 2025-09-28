<?php

namespace App\Modules\Contabilidade\UseCases;

use App\Modules\Contabilidade\DTOs\IptuDamDto;
use App\Modules\Contabilidade\Repositories\Contratos\ContratoTributarioIptuDamRepository;
use App\Modules\Contracts\ContratoArmazenamento;
use App\Modules\Contracts\ContratoPagamentoGateway;
use App\Modules\Contracts\ContratoPdfGerador;
use App\Modules\Contracts\DTOs\PagamentoGatewayConfigDto;
use App\Modules\Contracts\DTOs\GerarCobrancaPixComVencimentoDto;
use App\Modules\Gestora\DTOs\GestoraInfoBancariaDto;
use App\Modules\Gestora\Repositories\Contratos\ContratoGestoraInfoBancariaRepository;
use DomainException;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessarIptuDamUseCase
{
    public function __construct(
        private ContratoTributarioIptuDamRepository $iptuDamRepository,
        private ContratoGestoraInfoBancariaRepository $gestoraInfoBancariaRepository,
        private ContratoPagamentoGateway $pagamentoGateway,
        private ContratoPdfGerador $documentoGerador,
        private ContratoArmazenamento $armazenamento,

    ) {}

    public function execute(int $iptuDamId): void
    {
        // todo: Esse gestora id deve ser pego do JWT enviado na requisição do Sigafi para o Nepal
        $gestoraId = 54;
        $iptuDam = $this->iptuDamRepository->findOneById($iptuDamId);
        $gestoraInfoBancarias = $this->gestoraInfoBancariaRepository->findOneByGestoraId($gestoraId);

        if (! $iptuDam) {
            Log::warning('IPTU DAM não encontrado', ['iptuDamId' => $iptuDamId]);
            throw new DomainException("IPTU DAM com ID {$iptuDamId} não encontrado.");
        }

        // todo: melhorar essas atualizações, pois são duas conexões ao banco para atualizar a mesma entidade;
        try {
            $this->gerarPixQrcode($iptuDam, $gestoraInfoBancarias);
            $this->gerarPdfCarneDePagamento($iptuDam);
        } catch (Throwable $e) {
            Log::error('Erro ao processar IPTU DAM', [
                'iptuDamId' => $iptuDamId,
                'mensagem' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e; // opcional, se quiser propagar
        }
    }

    private function gerarPdfCarneDePagamento(IptuDamDto $IptuDamDto): void
    {
        $caminhoView = 'pdf.contabilidade.iptu.dam';
        $nomeArquivo = "iptu_dam_{$IptuDamDto->id}.pdf";
        $caminhodDoPdf = "pdf/iptu/dam/{$nomeArquivo}";

        $conteudoPdf = $this->documentoGerador->gerarPdf(
            $caminhoView,
            ['iptuDam' => $IptuDamDto],
            [
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font_size' => 0,
                'default_font' => '',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 12,
                'margin_bottom' => 14,
                'margin_header' => 9,
                'margin_footer' => 9,
            ]
        );

        $this->armazenamento->put($caminhodDoPdf, $conteudoPdf);
        $this->iptuDamRepository->updatePdfPath($IptuDamDto->id, $caminhodDoPdf);
    }

    private function gerarPixQrcode(IptuDamDto $IptuDamDto, GestoraInfoBancariaDto $gestoraInfoBancariaDto)
    {
        $config = new PagamentoGatewayConfigDto(
            $gestoraInfoBancariaDto->client_id,
            $gestoraInfoBancariaDto->client_secret,
            $gestoraInfoBancariaDto->developer_application_key,
            $gestoraInfoBancariaDto->gerarTokenDeAcesso(),
            // todo: mudar isso depois com base nos dados que vem do banco
            'homolog',
            $gestoraInfoBancariaDto->chave_pix
        );

        $this->pagamentoGateway->definirConfiguracao($config);

        // todo: Criar um module voltado ao contribuiente, e centralizar lá a lógica de busca desses dados.
        $pix = $this->pagamentoGateway->gerarCobrancaPixComVencimento(
            new GerarCobrancaPixComVencimentoDto(
                dataDeVencimento: '2035-06-24',
                validadeAposVencimento: 30,
                cpf: '12345678909',
                cnpj: null,
                nome: 'Francisco da Silva',
                logradouro: 'Alameda Souza, Numero 80, Bairro Braz',
                cidade: 'Recife',
                uf: 'PE',
                cep: '70011750',
                valor: '123.45',
                multa: [
                    'modalidade' => '2',
                    'valorPerc' => '15.00',
                ],
                juros: [
                    'modalidade' => '2',
                    'valorPerc' => '2.00',
                ],
                desconto: [
                    'modalidade' => '1',
                    'descontoDataFixa' => [
                        [
                            'data' => '2030-06-24',
                            'valorPerc' => '30.00',
                        ],
                    ],
                ],
                // todo: implementar uma lógica de usar a chave de teste em ambientes de desenvolvimento
                // https://apoio.developers.bb.com.br/referency/post/684b0bee5484560013705120
                chave: '95127446000198',
                solicitacaoPagador: 'Cobrança dos serviços prestados.',
                infoAdicionais: []
            )
        );

        $IptuDamDto->pix_qr_code = $pix->pixCopiaECola;

        $this->iptuDamRepository->updatePix(
            $IptuDamDto->id,
            $IptuDamDto->pix_qr_code
        );
    }
}
