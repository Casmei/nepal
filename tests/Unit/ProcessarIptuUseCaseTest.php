<?php

namespace Tests\Unit\Modules\Contabilidade\UseCases;

use App\Jobs\ProcessarIptuJob;
use App\Modules\Contabilidade\UseCases\ProcessarIptuUseCase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProcessarIptuUseCaseTest extends TestCase
{
    #[Test]
    public function deve_disparar_o_job_correto_na_fila()
    {
        Queue::fake();
        $useCase = new ProcessarIptuUseCase;
        $useCase->execute();
        Queue::assertPushed(ProcessarIptuJob::class);
    }
}
