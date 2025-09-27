<?php

namespace App\Modules\Contabilidade\UseCases;

use App\Jobs\ProcessarIptuDamJob;
use App\Modules\Contabilidade\Repositories\TributarioIptuDamRepository;

class ProcessarIptuDamLoteUseCase
{
    public function __construct(
        private TributarioIptuDamRepository $repository
    ) {}

    public function execute(array $iptuDamIds): void
    {
        foreach ($iptuDamIds as $iptuDamId) {
            dispatch(new ProcessarIptuDamJob($iptuDamId));
        }
    }
}
