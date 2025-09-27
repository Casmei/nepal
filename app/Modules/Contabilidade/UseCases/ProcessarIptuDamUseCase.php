<?php

namespace App\Modules\Contabilidade\UseCases;

use App\Modules\Contabilidade\DTOs\IptuDamDto;
use App\Modules\Contabilidade\Repositories\Contratos\ContratoTributarioIptuDamRepository;
use App\Modules\Contracts\ContratoArmazenamento;
use App\Modules\Contracts\ContratoPagamentoGateway;
use App\Modules\Contracts\ContratoPdfGerador;
use DomainException;

class ProcessarIptuDamUseCase
{
    public function __construct(
        private ContratoTributarioIptuDamRepository $repository,
        private ContratoPagamentoGateway $pagamentoGateway,
        private ContratoPdfGerador $documentoGerador,
        private ContratoArmazenamento $armazenamento,

    ) {}

    public function execute(int $iptuDamId): void
    {
        $iptuDam = $this->repository->findOneById($iptuDamId);

        if (! $iptuDam) {
            throw new DomainException("IPTU DAM com ID {$iptuDamId} não encontrado.");
        }

        // todo: melhorar essas atualizações, pois são duas conexões ao banco para atualizar a mesma entidade;
        $this->gerarPixQrcode($iptuDam);
        $this->gerarPdfCarneDePagamento($iptuDam);
    }

    private function gerarPdfCarneDePagamento(IptuDamDto $IptuDamDto): void
    {
        $caminhoView = 'pdf.contabilidade.iptu.dam';
        $nomeArquivo = "iptu_dam_{$IptuDamDto->id}.pdf";
        $caminhodDoPdf = "pdf/iptu/dam/{$nomeArquivo}";

        $conteudoPdf = $this->documentoGerador->gerarPdf(
            $caminhoView,
            ['iptuDam' => $IptuDamDto]
        );

        $this->armazenamento->put($caminhodDoPdf, $conteudoPdf);
        $this->repository->updatePdfPath($IptuDamDto->id, $caminhodDoPdf);
    }

    private function gerarPixQrcode(IptuDamDto $IptuDamDto)
    {
        $IptuDamDto->pix_qr_code = $this->pagamentoGateway->gerarCobrancaPix(
            $IptuDamDto->valor,
            'IPTU'
        );

        $this->repository->updatePix(
            $IptuDamDto->id,
            $IptuDamDto->pix_qr_code
        );
    }
}
