<?php

namespace App\Modules\Contabilidade\Repositories;

use App\Modules\Contabilidade\DTOs\IptuDamDto;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TributarioIptuRepository
{
    public function updatePix(IptuDamDto $iptuDamDto, string $pixQrCode): void
    {
        DB::table('tributario_iptu_calculo_dam')
            ->where('id', $iptuDamDto->id)
            ->update([
                'pix_qr_code' => $pixQrCode,
            ]);
    }

    public function chunkIptuDamsByGestora(int $gestoraId, Closure $callback, int $batchSize = 100): void
    {
        DB::table('tributario_iptu_calculo', 'tic')
            ->leftJoin('tributario_iptu_calculo_dam as dam', 'tic.id', '=', 'dam.iptu_calculo_id')
            ->where('tic.gestora_id', $gestoraId)
            ->whereNull('dam.pix_qr_code')
            ->select('dam.*')
            ->chunk($batchSize, function (Collection $iptuDams) use ($callback) {
                $callback($iptuDams);
            });
    }
}
