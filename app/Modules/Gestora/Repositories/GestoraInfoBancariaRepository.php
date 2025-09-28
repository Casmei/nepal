<?php

namespace App\Modules\Gestora\Repositories;

use App\Modules\Gestora\DTOs\GestoraInfoBancariaDto;
use App\Modules\Gestora\Repositories\Contratos\ContratoGestoraInfoBancariaRepository;
use Illuminate\Support\Facades\DB;

class GestoraInfoBancariaRepository implements ContratoGestoraInfoBancariaRepository
{
    protected $connection = 'sigafi_sistema';

    public function findOneByGestoraId(int $gestoraId): ?GestoraInfoBancariaDto
    {
        $row = DB::connection($this->connection)
            ->table('adm_gestora_pix_bb')
            ->where('adm_gestora_id', '=', $gestoraId)
            ->first();

        if (! $row) {
            return null;
        }

        return GestoraInfoBancariaDto::from($row);
    }
}
