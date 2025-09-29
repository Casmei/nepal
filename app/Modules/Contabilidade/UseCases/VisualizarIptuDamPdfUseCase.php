<?php

namespace App\Modules\Contabilidade\UseCases;

use App\Modules\Contabilidade\Repositories\Contratos\ContratoTributarioIptuDamRepository;
use App\Modules\Contracts\ContratoArmazenamento;
use App\Modules\Contracts\ContratoPdfGerador;
use DomainException;

class VisualizarIptuDamPdfUseCase
{
    public function __construct(
        private ContratoTributarioIptuDamRepository $repository,
        private ContratoPdfGerador $documentoGerador,
        private ContratoArmazenamento $armazenamento,
    ) {}

    public function execute(int $iptuDamId): string
    {
        $iptuDam = $this->repository->findOneById($iptuDamId);

        if (! $iptuDam) {
            throw new DomainException("IPTU DAM com ID {$iptuDamId} não encontrado.");
        }

        if (! isset($iptuDam->caminho_carne_pdf)) {
            throw new DomainException("IPTU DAM carnê com ID {$iptuDamId} não encontrado.");
        }

        return $this->armazenamento->path($iptuDam->caminho_carne_pdf);
    }
}
