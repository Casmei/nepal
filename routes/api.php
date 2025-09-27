<?php

use App\Modules\Contabilidade\Http\Controllers\IptuController;
use Illuminate\Support\Facades\Route;

Route::post('contabilidades/iptu/processar', [IptuController::class, 'processarLoteIptuDam']);
Route::get('contabilidades/iptu/pdf', [IptuController::class, 'visualizarIptuDamPdf']);
