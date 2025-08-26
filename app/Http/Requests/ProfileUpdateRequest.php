<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Aturan validasi dasar yang berlaku untuk semua role
        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'npm' => ['nullable', 'string', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'nidn' => ['nullable', 'string', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'tanggal_yudisium' => ['nullable', 'date'],
        ];

        // PERBAIKAN: Tambahkan aturan validasi email hanya jika role BUKAN dosen
        if ($this->user()->role !== 'dosen') {
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)];
        }

        return $rules;
    }
}
