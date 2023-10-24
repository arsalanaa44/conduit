<?php

namespace App\Http\Requests\user;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateValidation extends FormRequest
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
        $user = Auth::user();
        return [
            'user.email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'user.username' => 'sometimes|required|unique:users,username,' . $user->id,
            'user.password' => 'sometimes|required|min:8',
            'user.bio' => 'sometimes|string',
            'user.image' => 'sometimes|url'
        ];

    }
}
