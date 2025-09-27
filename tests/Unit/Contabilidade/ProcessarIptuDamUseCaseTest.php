<?php

namespace Tests\Unit\Contabilidade;

use PHPUnit\Framework\TestCase;
use App\Modules\Contabilidade\UseCases\ProcessarIptuDamUseCase;
use App\Modules\Contracts\ContratoPagamentoGateway;
use App\Modules\Contracts\ContratoPdfGerador;
use App\Modules\Contracts\ContratoArmazenamento;
use Test;
use Tests\Fakes\TributarioIptuDamRepositoryFake;
use Tests\Factories\IptuDamDtoFactory;
use DomainException;

class ProcessarIptuDamUseCaseTest extends TestCase
{
    private $repository;
    private $pagamentoGateway;
    private $documentoGerador;
    private $armazenamento;
    private $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new TributarioIptuDamRepositoryFake();
        $this->pagamentoGateway = $this->createMock(ContratoPagamentoGateway::class);
        $this->documentoGerador = $this->createMock(ContratoPdfGerador::class);
        $this->armazenamento = $this->createMock(ContratoArmazenamento::class);

        $this->useCase = new ProcessarIptuDamUseCase(
            $this->repository,
            $this->pagamentoGateway,
            $this->documentoGerador,
            $this->armazenamento
        );
    }

    #[Test]
    public function testDeveGerarPixEPdfQuandoIptuExiste(): void
    {
        $dto = IptuDamDtoFactory::make(['id' => 1, 'valor' => '150.00']);

        $this->repository->add($dto);

        $this->pagamentoGateway
            ->expects($this->once())
            ->method('gerarCobrancaPix')
            ->with('150.00', 'IPTU')
            ->willReturn('pix_code_123');

        $this->documentoGerador
            ->expects($this->once())
            ->method('gerarPdf')
            ->with('pdf.contabilidade.iptu.dam', ['iptuDam' => $dto])
            ->willReturn('%PDF-MOCK%');

        $this->armazenamento
            ->expects($this->once())
            ->method('put')
            ->with('pdf/iptu/dam/iptu_dam_1.pdf', '%PDF-MOCK%');

        $this->useCase->execute(1);

        // garante que o fake repository foi atualizado
        $iptuAtualizado = $this->repository->findOneById(1);

        $this->assertEquals('pix_code_123', $iptuAtualizado->pix_qr_code);
        $this->assertEquals('pdf/iptu/dam/iptu_dam_1.pdf', $iptuAtualizado->caminho_carne_pdf);
    }

    #[Test]
    public function testDeveLancarExcecaoQuandoIptuNaoExiste(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('IPTU DAM com ID 999 não encontrado.');

        $this->useCase->execute(999);
    }
}
