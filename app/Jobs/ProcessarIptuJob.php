<?php

namespace App\Jobs;

use App\Modules\Contabilidade\DTOs\IptuDamDto;
use App\Modules\Contabilidade\UseCases\AtualizarIptuUseCase;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessarIptuJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(AtualizarIptuUseCase $useCase): void
    {
        Log::info('Iniciando o processamento em lote para geração de QR Codes do IPTU.');

        // TODO: Pensar em uma forma de não sujar a camada de infra, isso deveria estar em um repository;
        $query = DB::table('tributario_iptu_calculo', 'tic')
            ->leftJoin('tributario_iptu_calculo_dam as dam', 'tic.id', '=', 'dam.iptu_calculo_id')
            ->where('tic.gestora_id', 54)
            ->whereNull('dam.pix_qr_code')
            ->select('dam.*');

        $query->orderBy('dam.id')->chunk(100, function (Collection $iptuDams) use ($useCase) {
            Log::info('Processando um novo lote de 100 documentos de IPTU.');

            foreach ($iptuDams as $iptuDamObject) {
                try {
                    $dto = IptuDamDto::from($iptuDamObject);

                    $useCase->execute($dto);

                    Log::info('Documento de IPTU processado com sucesso.', [
                        'dam_id' => $dto->id,
                    ]);
                } catch (Exception $e) {
                    Log::error('Erro ao processar documento de IPTU.', [
                        'dam_id' => $iptuDamObject->id,
                        'error_message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

            }
        });

        Log::info('Processamento em lote para geração de QR Codes do IPTU finalizado.');
    }
}
