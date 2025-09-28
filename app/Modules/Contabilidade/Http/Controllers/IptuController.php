<?php

namespace App\Modules\Contabilidade\Http\Controllers;

use App\Modules\Contabilidade\Http\Requests\CriarLoteDeProcessamentoIptuDamRequest;
use App\Modules\Contabilidade\Http\Requests\VisualizarIptuDamPdfRequest;
use App\Modules\Contabilidade\UseCases\ProcessarIptuDamLoteUseCase;
use App\Modules\Contabilidade\UseCases\ProcessarIptuDamUseCase;
use App\Modules\Contabilidade\UseCases\VisualizarIptuDamPdfUseCase;

class IptuController
{
    public function __construct(
        private ProcessarIptuDamLoteUseCase $processarIptuDamLoteUseCase,
        private ProcessarIptuDamUseCase $processarIptuDamUseCase,
        private VisualizarIptuDamPdfUseCase $visualizarIptuDamPdfUseCase,
    ) {}

    public function processarLoteIptuDam(CriarLoteDeProcessamentoIptuDamRequest $request)
    {
        $request = $request->validated();

        $this->processarIptuDamLoteUseCase->execute($request['ids']);

        // Descomentar para testes sem fila.
        // $this->processarIptuDamUseCase->execute($request['ids'][0]);

        return response()->json([
            'message' => 'Processing request was accepted and enqueued.',
            'status' => 'accepted',
        ], 202);
    }

    public function visualizarIptuDamPdf(VisualizarIptuDamPdfRequest $request)
    {
        $caminhoCompleto = $this->visualizarIptuDamPdfUseCase->execute($request['id']);

        return response()->file($caminhoCompleto, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($caminhoCompleto) . '"',
        ]);
    }
}
