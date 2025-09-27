<?php

namespace App\Modules\Contabilidade\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriarLoteDeProcessamentoIptuDamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ids.required' => 'O campo ids é obrigatório.',
            'ids.array' => 'O campo ids deve ser um array.',
            'ids.*.integer' => 'Todos os valores de ids devem ser números inteiros.',
        ];
    }
}
