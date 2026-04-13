<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanJudulRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('Mahasiswa');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'topik' => ['required', 'string', 'max:255'],
            'bidang' => ['required', 'string', 'max:255'],
            'latar_belakang' => ['required', 'string'],
            'file_proposal' => ['required', 'file', 'mimes:pdf', 'max:5120'], // Maks 5MB
        ];
    }
}
