<?php

namespace App\Modules\Contabilidade\UseCases;

use App\Modules\Contabilidade\DTOs\IptuDamDto;
use App\Modules\Contabilidade\Repositories\TributarioIptuRepository;

class AtualizarIptuUseCase
{
    protected $repository;

    public function __construct(TributarioIptuRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(IptuDamDto $iptuDamDto): void
    {
        $codigo = bin2hex(random_bytes(16)); // gera string aleatória imitando o pix
        $this->repository->updatePix($iptuDamDto, $codigo);
    }
}
