<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSiswa() ?? false;
    }

    public function rules(): array
    {
        return [
            'kategori_id' => ['required', 'exists:kategori_konsultasi,id'],
            'deskripsi' => ['required', 'string', 'min:10', 'max:1000'],
            'waktu_diminta' => ['required', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori konsultasi wajib dipilih.',
            'deskripsi.min' => 'Deskripsi masalah minimal 10 karakter.',
            'waktu_diminta.after' => 'Waktu yang diinginkan harus di masa depan.',
        ];
    }
}
