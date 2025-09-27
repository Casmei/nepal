<?php

namespace Tests\Unit\Contabilidade;

use App\Modules\Contabilidade\UseCases\VisualizarIptuDamPdfUseCase;
use App\Modules\Contracts\ContratoArmazenamento;
use App\Modules\Contracts\ContratoPdfGerador;
use DomainException;
use PHPUnit\Framework\TestCase;
use Tests\Factories\IptuDamDtoFactory;
use Tests\Fakes\TributarioIptuDamRepositoryFake;

class VisualizarIptuDamPdfUseCaseTest extends TestCase
{
    private $repository;
    private $documentoGerador;
    private $armazenamento;
    private $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new TributarioIptuDamRepositoryFake;
        $this->documentoGerador = $this->createMock(ContratoPdfGerador::class);
        $this->armazenamento = $this->createMock(ContratoArmazenamento::class);

        $this->useCase = new VisualizarIptuDamPdfUseCase(
            $this->repository,
            $this->documentoGerador,
            $this->armazenamento
        );
    }

    /**
     * @test
     */
    #[Test]
    public function deve_retornar_caminho_quando_iptu_dam_existe(): void
    {
        $dtoId = 1;
        $dto = IptuDamDtoFactory::make([
            'id' => $dtoId,
            'caminho_carne_pdf' => 'outro/caminho.pdf',
        ]);

        $this->repository->add($dto);

        $this->armazenamento
            ->expects($this->once())
            ->method('path')
            ->with($dto->caminho_carne_pdf)
            ->willReturn('/storage/caminho/arquivo.pdf');

        $result = $this->useCase->execute($dtoId);

        $this->assertEquals('/storage/caminho/arquivo.pdf', $result);
    }

    /**
     * @test
     */
    #[Test]
    public function deve_lancar_excecao_quando_iptu_dam_nao_existe(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('IPTU DAM com ID 999 não encontrado.');

        $this->useCase->execute(999);
    }
}
