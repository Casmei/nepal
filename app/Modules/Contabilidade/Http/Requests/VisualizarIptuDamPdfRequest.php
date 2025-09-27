<?php

namespace App\Modules\Contabilidade\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisualizarIptuDamPdfRequest extends FormRequest
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
            'id' => 'required|integer',
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
            'id.required' => 'A query id é obrigatório.',
            'id.integer' => 'O id deve ser um número inteiro.',
        ];
    }
}
