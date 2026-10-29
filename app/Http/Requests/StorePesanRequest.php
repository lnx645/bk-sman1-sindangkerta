<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePesanRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Otorisasi akses pengajuan (milik sendiri / kewenangan guru BK)
        // diputuskan di controller lewat Pengajuan::visibleTo(); request ini
        // hanya memvalidasi isi pesan. Admin tidak punya rute ke sini.
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        // Trim dulu supaya pesan yang hanya berisi spasi dianggap kosong.
        if (is_string($this->input('isi'))) {
            $this->merge(['isi' => trim($this->input('isi'))]);
        }
    }

    public function rules(): array
    {
        return [
            'isi' => ['required', 'string', 'min:1', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'isi.required' => 'Pesan tidak boleh kosong. Tulis balasan Anda sebelum menekan Kirim.',
            'isi.min' => 'Pesan tidak boleh kosong. Tulis balasan Anda sebelum menekan Kirim.',
            'isi.max' => 'Pesan maksimal 2000 karakter.',
        ];
    }
}
