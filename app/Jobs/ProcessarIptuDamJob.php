<?php

namespace App\Jobs;

use App\Modules\Contabilidade\UseCases\ProcessarIptuDamUseCase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessarIptuDamJob implements ShouldQueue
{
    use Queueable;

    private $iptuDamId;
    private $gestoraId;

    public function __construct(int $iptuDamId, int $gestoraId)
    {
        $this->iptuDamId = $iptuDamId;
        $this->gestoraId = $gestoraId;

    }

    public function handle(ProcessarIptuDamUseCase $useCase): void
    {
        Log::info('Iniciando job para o processamento do IPTU', ['id' => $this->iptuDamId]);

        $useCase->execute($this->iptuDamId, $this->gestoraId);
    }
}
