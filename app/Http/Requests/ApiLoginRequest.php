<?php

// app/Http/Requests/ApiLogin.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiLogin extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string',
        ];
    }
}
