<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('person');
        return [
            'login' => 'required|string|max:255|unique:people,login,' . $id,
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14',
            'email' => 'required|email|max:255',
            'address' => 'nullable|string|max:255',
            'password' => $this->isMethod('post') ? 'required|string|min:6' : 'nullable|string|min:6',
            'type' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg|max:5120',
        ];
    }
}
