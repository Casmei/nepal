<?php

use App\Modules\Contabilidade\Http\Controllers\IptuController;
use Illuminate\Support\Facades\Route;

Route::get('contabilidades/processar/iptu', [IptuController::class, 'processarIptu']);
