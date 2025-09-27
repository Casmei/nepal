<?php

namespace App\Providers;

use App\Modules\Contabilidade\Repositories\Contratos\ContratoTributarioIptuDamRepository;
use App\Modules\Contabilidade\Repositories\TributarioIptuDamRepository;
use App\Modules\Contabilidade\UseCases\ProcessarIptuDamLoteUseCase;
use App\Modules\Contabilidade\UseCases\ProcessarIptuDamUseCase;
use App\Modules\Contabilidade\UseCases\VisualizarIptuDamPdfUseCase;
use App\Modules\Services\LaravelStorageService;
use App\Services\BancoDoBrasilGateway;
use App\Services\Mpdf;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // REPOSITORIES
        $this->app->bind(
            ContratoTributarioIptuDamRepository::class,
            TributarioIptuDamRepository::class
        );

        // USE CASES
        $this->app->bind(
            ProcessarIptuDamLoteUseCase::class,
            fn ($app) => new ProcessarIptuDamLoteUseCase(
                $app->make(TributarioIptuDamRepository::class)
            )

        );

        $this->app->bind(
            ProcessarIptuDamUseCase::class,
            fn ($app) => new ProcessarIptuDamUseCase(
                $app->make(TributarioIptuDamRepository::class),
                $app->make(BancoDoBrasilGateway::class),
                $app->make(Mpdf::class),
                $app->make(LaravelStorageService::class),
            )

        );

        $this->app->bind(
            VisualizarIptuDamPdfUseCase::class,
            fn ($app) => new VisualizarIptuDamPdfUseCase(
                $app->make(TributarioIptuDamRepository::class),
                $app->make(Mpdf::class),
                $app->make(LaravelStorageService::class),
            )

        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Aqui você pode colocar o código de inicialização, se necessário
    }
}
