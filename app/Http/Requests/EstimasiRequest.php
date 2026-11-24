<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstimasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tingkat_kerusakan'   => ['required', 'numeric', 'min:0', 'max:100'],
            'usia_laptop'         => ['required', 'numeric', 'min:0', 'max:10'],
            'kesulitan_perbaikan' => ['required', 'numeric', 'min:0', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'tingkat_kerusakan.required'   => 'Tingkat kerusakan wajib diisi.',
            'tingkat_kerusakan.numeric'    => 'Tingkat kerusakan harus berupa angka.',
            'tingkat_kerusakan.min'        => 'Tingkat kerusakan minimal 0.',
            'tingkat_kerusakan.max'        => 'Tingkat kerusakan maksimal 100.',
            'usia_laptop.required'         => 'Usia laptop wajib diisi.',
            'usia_laptop.numeric'          => 'Usia laptop harus berupa angka.',
            'usia_laptop.min'              => 'Usia laptop minimal 0.',
            'usia_laptop.max'              => 'Usia laptop maksimal 10 (jika lebih dari 10 tahun, isi 10).',
            'kesulitan_perbaikan.required' => 'Tingkat kesulitan perbaikan wajib diisi.',
            'kesulitan_perbaikan.numeric'  => 'Tingkat kesulitan harus berupa angka.',
            'kesulitan_perbaikan.min'      => 'Tingkat kesulitan minimal 0.',
            'kesulitan_perbaikan.max'      => 'Tingkat kesulitan maksimal 10.',
        ];
    }

    public function attributes(): array
    {
        return [
            'tingkat_kerusakan'   => 'Tingkat Kerusakan',
            'usia_laptop'         => 'Usia Laptop',
            'kesulitan_perbaikan' => 'Tingkat Kesulitan',
        ];
    }
}
