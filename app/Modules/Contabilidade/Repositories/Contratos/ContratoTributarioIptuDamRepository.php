<?php

namespace App\Modules\Contabilidade\Repositories\Contratos;

use App\Modules\Contabilidade\DTOs\IptuDamDto;
use Closure;

interface ContratoTributarioIptuDamRepository
{
    public function findOneById(int $iptuDamId): ?IptuDamDto;

    public function updatePix(int $iptuDamId, string $pixQrCode): void;

    public function updatePdfPath(int $iptuDamId, string $caminhoCarnePdf): void;

    public function chunkIptuDamsByGestora(
        int $gestoraId,
        int $batchSize,
        Closure $callback
    ): void;
}
