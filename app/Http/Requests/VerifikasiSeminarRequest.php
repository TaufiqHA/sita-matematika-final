<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VerifikasiSeminarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('TU');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'in:diverifikasi,perlu_revisi'],
            'catatan_tu' => ['nullable', 'string'],
            'berkas_status' => ['nullable', 'array'],
            'berkas_status.*.id' => ['required', 'exists:berkas_seminars,id'],
            'berkas_status.*.status' => ['required', 'in:valid,tidak_valid'],
        ];
    }
}
