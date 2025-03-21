<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'type' => 'required|in:visitor,farmer',
            'password' => 'required|min:8|confirmed', // Ensure password confirmation
            'fcm_token' => 'required'
        ];
        if (request()->input('type') === 'farmer') {
            $rules['certificate'] = "required|mimes:pdf,word|max:10000";
        }
        return $rules; 
    }
}
