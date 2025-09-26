<?php

namespace Tests\Unit\Modules\Contabilidade\UseCases;

use App\Modules\Contabilidade\DTOs\IptuDamDto;
use App\Modules\Contabilidade\Repositories\TributarioIptuRepository;
use App\Modules\Contabilidade\UseCases\AtualizarIptuUseCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AtualizarIptuUseCaseTest extends TestCase
{
    #[Test]
    public function deve_chamar_o_metodo_update_pix_no_repositorio_com_os_parametros_corretos()
    {
        // Arrange
        $repositoryMock = $this->createMock(TributarioIptuRepository::class);

        $iptuDamDto = new IptuDamDto(
            id: 1,
            iptu_calculo_id: 100,
            tipo: 'integral',
            numero_parcela: null,
            data_vencimento: '2025-08-31',
            competencia: '2025',
            mes_competencia: 7,
            valor: '813.30',
            demonstrativo: '...',
            desconto: '0.00',
            acrescimo: '0.00',
            juros: '0.00',
            multa: '0.00',
            mora: '0.00',
            iptu_calculo_rotina_id: null,
            instrucao_pagamento: '...',
            pix_qr_code: null
        );

        // Assert
        $repositoryMock->expects($this->once())
            ->method('updatePix')
            ->with(
                $this->equalTo($iptuDamDto),
                $this->stringContains(''),
            );

        $useCase = new AtualizarIptuUseCase($repositoryMock);

        // Act
        $useCase->execute($iptuDamDto);
    }
}
