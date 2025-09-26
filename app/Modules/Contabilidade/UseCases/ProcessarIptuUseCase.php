<?php

namespace App\Modules\Contabilidade\UseCases;

use App\Jobs\ProcessarIptuJob;

class ProcessarIptuUseCase
{
    public function execute()
    {
        ProcessarIptuJob::dispatch();
    }
}
