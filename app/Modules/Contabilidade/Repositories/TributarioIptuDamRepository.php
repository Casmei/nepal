<?php

namespace App\Modules\Contabilidade\Repositories;

use App\Modules\Contabilidade\DTOs\IptuDamDto;
use App\Modules\Contabilidade\Repositories\Contratos\ContratoTributarioIptuDamRepository;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TributarioIptuDamRepository implements ContratoTributarioIptuDamRepository
{
    public function updatePix(int $iptuDamId, string $pixQrCode): void
    {
        DB::table('tributario_iptu_calculo_dam')
            ->where('id', $iptuDamId)
            ->update([
                'pix_qr_code' => $pixQrCode,
            ]);
    }

    public function updatePdfPath(int $iptuDamId, string $caminhoCarnePdf): void
    {
        DB::table('tributario_iptu_calculo_dam')
            ->where('id', $iptuDamId)
            ->update([
                'caminho_carne_pdf' => $caminhoCarnePdf,
            ]);
    }

    // todo: Implementar ele em uma rotina;
    public function chunkIptuDamsByGestora(
        int $gestoraId,
        int $batchSize,
        Closure $callback
    ): void {
        DB::table('tributario_iptu_calculo as tic')
            ->leftJoin('tributario_iptu_calculo_dam as dam', 'tic.id', '=', 'dam.iptu_calculo_id')
            ->where('tic.gestora_id', $gestoraId)
            ->whereNull('dam.pix_qr_code')
            ->select('dam.id')
            ->orderBy('dam.id')
            ->chunk($batchSize, function (Collection $iptuDams) use ($callback) {
                $callback($iptuDams);
            });
    }

    public function findOneById(int $iptuDamId): ?IptuDamDto
    {
        $row = DB::table('tributario_iptu_calculo_dam')->find($iptuDamId);

        if (!$row) {
            return null;
        }

        return IptuDamDto::from($row);
    }
}
