<?php

namespace App\Http\Requests\General\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AuthRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required','string','email','max:255'],
            'password' => ['required','string','min:6'],
            'remember_me' => ['nullable', 'in:0,1'],
        ];
    }
}
