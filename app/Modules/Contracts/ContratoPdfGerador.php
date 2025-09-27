<?php

namespace App\Modules\Contracts;

interface ContratoPdfGerador
{
    public function gerarPdf(string $caminhoView, array $dados): string;
}
