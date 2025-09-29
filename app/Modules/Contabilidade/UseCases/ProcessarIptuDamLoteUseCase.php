<?php

namespace App\Modules\Contabilidade\UseCases;

use App\Jobs\ProcessarIptuDamJob;

class ProcessarIptuDamLoteUseCase
{
    public function __construct(
    ) {}

    public function execute(array $iptuDamIds, int $gestoraId): void
    {
        foreach ($iptuDamIds as $iptuDamId) {
            dispatch(new ProcessarIptuDamJob($iptuDamId, $gestoraId));
        }
    }
}
