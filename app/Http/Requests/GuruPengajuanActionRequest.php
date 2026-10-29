<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuruPengajuanActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isGuruBk() ?? false;
    }

    public function rules(): array
    {
        $aksi = $this->input('aksi');

        return [
            'aksi' => ['required', Rule::in(['tinjau', 'setujui', 'tolak', 'jadwalkan_ulang', 'selesai'])],
            'alasan_penolakan' => ['required_if:aksi,tolak', 'nullable', 'string', 'max:500'],
            'waktu_final' => ['required_if:aksi,setujui,jadwalkan_ulang', 'nullable', 'date', 'after:now'],
            'tempat' => ['nullable', 'string', 'max:255'],
            'metode' => ['nullable', Rule::in(['tatap_muka', 'daring'])],
            'catatan_reschedule' => ['required_if:aksi,jadwalkan_ulang', 'nullable', 'string', 'max:500'],
            'catatan' => ['required_if:aksi,selesai', 'nullable', 'string', 'max:2000'],
            'tindak_lanjut' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
