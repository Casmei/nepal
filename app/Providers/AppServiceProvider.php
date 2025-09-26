<?php

namespace App\Providers;

use App\Modules\Contabilidade\Repositories\TributarioIptuRepository;
use App\Modules\Contabilidade\UseCases\AtualizarIptuUseCase;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar o binding do repositório
        $this->app->bind(
            TributarioIptuRepository::class,
            function ($app) {
                return new TributarioIptuRepository;
            }
        );

        $this->app->bind(
            AtualizarIptuUseCase::class,
            function ($app) {
                return new AtualizarIptuUseCase(
                    $app->make(TributarioIptuRepository::class)
                );
            }
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
