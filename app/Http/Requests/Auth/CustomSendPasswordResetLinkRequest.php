<?php

namespace App\Http\Requests\Auth;

use Laravel\Fortify\Http\Requests\SendPasswordResetLinkRequest as FortifySendPasswordResetLinkRequest;

class CustomSendPasswordResetLinkRequest extends FortifySendPasswordResetLinkRequest
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
            \Laravel\Fortify\Fortify::email() => 'required|email|exists:t02_users,email',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $emailKey = \Laravel\Fortify\Fortify::email();
        return [
            $emailKey . '.required' => 'Kolom email wajib diisi.',
            $emailKey . '.email' => 'Format email tidak valid.',
            $emailKey . '.exists' => 'User tidak ditemukan.',
        ];
    }
}
