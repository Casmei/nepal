<?php

namespace App\Modules\Contabilidade\Http\Controllers;

use App\Modules\Contabilidade\UseCases\ProcessarIptuUseCase;
use Illuminate\Http\Request;

class IptuController
{
    protected $processarIptuUseCase;

    public function __construct(ProcessarIptuUseCase $processarIptuUseCase)
    {
        $this->processarIptuUseCase = $processarIptuUseCase;
    }

    public function processarIptu(Request $request)
    {
        $this->processarIptuUseCase->execute();

        return response()->json([
            'message' => 'Processing request was accepted and enqueued.',
            'status' => 'accepted',
        ], 202);
    }
}
