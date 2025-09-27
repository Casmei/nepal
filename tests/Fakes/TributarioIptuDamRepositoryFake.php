<?php

namespace Tests\Fakes;

use App\Modules\Contabilidade\DTOs\IptuDamDto;
use App\Modules\Contabilidade\Repositories\Contratos\ContratoTributarioIptuDamRepository;
use Closure;

class TributarioIptuDamRepositoryFake implements ContratoTributarioIptuDamRepository
{
    /** @var array<int, IptuDamDto> */
    private array $items = [];

    public function add(IptuDamDto $dto): void
    {
        $this->items[$dto->id] = $dto;
    }

    public function findOneById(int $iptuDamId): ?IptuDamDto
    {
        return $this->items[$iptuDamId] ?? null;
    }

    public function updatePix(int $iptuDamId, string $pixQrCode): void
    {
        if (isset($this->items[$iptuDamId])) {
            $this->items[$iptuDamId]->pix_qr_code = $pixQrCode;
        }
    }

    public function updatePdfPath(int $iptuDamId, string $caminhoCarnePdf): void
    {
        if (isset($this->items[$iptuDamId])) {
            $this->items[$iptuDamId]->caminho_carne_pdf = $caminhoCarnePdf;
        }
    }

    public function chunkIptuDamsByGestora(
        int $gestoraId,
        int $batchSize,
        Closure $callback
    ): void {
        // Como é fake, a gente ignora o gestoraId e só divide o array
        // O gestoraId é um dado pertencente a outra tabela. Como usamos
        // o items, presupoe que os dados já são de determinada gestora.
        $chunks = array_chunk($this->items, $batchSize);

        foreach ($chunks as $chunk) {
            $callback(collect($chunk));
        }
    }
}
