<?php

namespace App\Modules\Contracts;

interface ContratoArmazenamento
{
    /**
     * Armazena o conteúdo do arquivo no caminho especificado.
     *
     * @param  string  $path  O caminho onde o arquivo deve ser armazenado.
     * @param  string  $contents  O conteúdo do arquivo (ex: conteúdo PDF binário).
     * @param  string|null  $disk  O disco do Laravel a ser usado (ex: 'local', 's3').
     */
    public function put(string $path, string $contents, ?string $disk = 'local'): void;

    /**
     * Obtém o caminho absoluto de um arquivo armazenado.
     *
     * Nota: Este método é geralmente útil apenas para drivers de disco local.
     *
     * @param  string  $path  O caminho do arquivo dentro do disco.
     * @param  string|null  $disk  O disco do Laravel a ser usado.
     * @return string O caminho absoluto completo (para local) ou um caminho virtual.
     */
    public function path(string $path, ?string $disk = 'local'): string;

    // todo: implementar contratos de busca para drivers diversos;
}
