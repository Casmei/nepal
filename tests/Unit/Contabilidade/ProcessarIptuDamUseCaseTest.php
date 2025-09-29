<?php

namespace Tests\Unit\Contabilidade;

use App\Modules\Contabilidade\UseCases\ProcessarIptuDamUseCase;
use App\Modules\Contracts\ContratoArmazenamento;
use App\Modules\Contracts\ContratoPagamentoGateway;
use App\Modules\Contracts\ContratoPdfGerador;
use DomainException;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Factories\GestoraInfoBancariaDtoFactory;
use Tests\Fakes\GestoraInfoBancariaRepositoryFake;
use Tests\Fakes\TributarioIptuDamRepositoryFake;

class ProcessarIptuDamUseCaseTest extends TestCase
{
    private TributarioIptuDamRepositoryFake $tributarioIptuDamrepository;
    private GestoraInfoBancariaRepositoryFake $gestoraInfoBancariaRepositoryFake;
    private $pagamentoGateway;
    private $documentoGerador;
    private $armazenamento;
    private ProcessarIptuDamUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        // Usamos Fakes para simular o comportamento de repositórios (estado)
        $this->tributarioIptuDamrepository = new TributarioIptuDamRepositoryFake;
        $this->gestoraInfoBancariaRepositoryFake = new GestoraInfoBancariaRepositoryFake;

        // Usamos Mocks para verificar interações com serviços externos (comportamento)
        $this->pagamentoGateway = $this->createMock(ContratoPagamentoGateway::class);
        $this->documentoGerador = $this->createMock(ContratoPdfGerador::class);
        $this->armazenamento = $this->createMock(ContratoArmazenamento::class);

        $this->useCase = new ProcessarIptuDamUseCase(
            $this->tributarioIptuDamrepository,
            $this->gestoraInfoBancariaRepositoryFake,
            $this->pagamentoGateway,
            $this->documentoGerador,
            $this->armazenamento
        );
    }

    #[Test]
    public function deve_lancar_excecao_quando_iptu_dam_nao_encontrado(): void
    {
        // Arrange
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('IPTU DAM com ID 999 não encontrado.');

        // Adiciona a gestora para garantir que o erro é apenas a falta do IPTU
        $gestoraDto = GestoraInfoBancariaDtoFactory::make(['adm_gestora_id' => 54]);
        $this->gestoraInfoBancariaRepositoryFake->add($gestoraDto);

        // Act
        $this->useCase->execute(999, 54);
    }

    #[Test]
    public function deve_lancar_excecao_quando_gestora_info_bancaria_nao_encontrado(): void
    {
        // Arrange
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('IPTU DAM com ID 999 não encontrado.');

        // Adiciona a gestora para garantir que o erro é apenas a falta do IPTU
        $gestoraDto = GestoraInfoBancariaDtoFactory::make(['adm_gestora_id' => 54]);
        $this->gestoraInfoBancariaRepositoryFake->add($gestoraDto);

        // Act
        $this->useCase->execute(999, 54);
    }
}
