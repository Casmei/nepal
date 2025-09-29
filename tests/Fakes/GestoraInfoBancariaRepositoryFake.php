<?php

namespace Tests\Fakes;

use App\Modules\Gestora\DTOs\GestoraInfoBancariaDto;
use App\Modules\Gestora\Repositories\Contratos\ContratoGestoraInfoBancariaRepository;

class GestoraInfoBancariaRepositoryFake implements ContratoGestoraInfoBancariaRepository
{
    private array $items = [];

    public function add(GestoraInfoBancariaDto $dto): void
    {
        $this->items[$dto->id] = $dto;
    }

    public function findOneByGestoraId(int $gestoraId): ?GestoraInfoBancariaDto
    {
        return $this->items[$gestoraId] ?? null;
    }
}
