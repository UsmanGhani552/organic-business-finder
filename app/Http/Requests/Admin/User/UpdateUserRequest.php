<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')->id;
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $userId,
            'type' => 'required|in:visitor,farmer',
            'password' => 'nullable|min:8|confirmed', // Ensure password confirmation
            'image' => 'image|max:2048'
        ]; 
    }
}
