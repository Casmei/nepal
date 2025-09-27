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

    public function __construct(int $iptuDamId)
    {
        $this->iptuDamId = $iptuDamId;
    }

    public function handle(ProcessarIptuDamUseCase $useCase): void
    {
        Log::info('Iniciando o processamento do IPTU', ['id' => $this->iptuDamId]);

        $useCase->execute($this->iptuDamId);
    }
}
