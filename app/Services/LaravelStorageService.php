<?php

namespace App\Modules\Services;

use App\Modules\Contracts\ContratoArmazenamento;
use Illuminate\Support\Facades\Storage;

class LaravelStorageService implements ContratoArmazenamento
{
    /**
     * {@inheritdoc}
     */
    public function put(string $path, string $contents, ?string $disk = 'local'): void
    {
        // Usa o disco fornecido ou o 'local' por padrão (similar ao seu código original)
        $driver = Storage::disk($disk);
        $driver->put($path, $contents);
    }

    /**
     * {@inheritdoc}
     */
    public function path(string $path, ?string $disk = 'local'): string
    {
        // O método path() do Laravel é mais adequado para o disco 'local'.
        // Se você estivesse usando S3, esta função provavelmente deveria ser adaptada
        // para retornar Storage::url($path) ou você criaria um método getUrl()
        // no contrato, mas para o seu Use Case de "Visualizar", vamos manter path()
        // assumindo que é um caminho local.
        return Storage::disk($disk)->path($path);
    }
}
