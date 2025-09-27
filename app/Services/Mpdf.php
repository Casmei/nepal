<?php

namespace App\Services;

use App\Modules\Contracts\ContratoPdfGerador;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;
use Mccarlosen\LaravelMpdf\LaravelMpdf as LaravelMpdfType;

class Mpdf implements ContratoPdfGerador
{
    private LaravelMpdfType $pdf;

    public function gerarPdf(string $caminhoView, array $dados): string
    {
        $this->pdf = LaravelMpdf::loadView($caminhoView, $dados);

        return $this->pdf->output();
    }
}
