<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePendaftaranSeminarRequest extends FormRequest
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
            'jenis_seminar' => ['required', 'in:proposal,hasil,munaqasyah'],
            'berkas' => ['required', 'array'],
            'berkas.*.nama_berkas' => ['required', 'string', 'max:255'],
            'berkas.*.file' => ['required', 'file', 'mimes:pdf,jpg,png', 'max:5120'], // Maks 5MB per file
        ];
    }
}
