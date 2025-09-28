<?php

namespace App\Modules\Gestora\Repositories\Contratos;

use App\Modules\Gestora\DTOs\GestoraInfoBancariaDto;

interface ContratoGestoraInfoBancariaRepository
{
    public function findOneByGestoraId(int $gestoraId): ?GestoraInfoBancariaDto;
}
