<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use app\Models\User;

class ForgetPasswordRequest extends FormRequest
{

    public function authorize()
    {
        return !($user = auth()->user()) || !($user instanceof User);
    }

    public function rule()
    {
        return
            [
                'email' => [
                    'required',
                    'email'
                ]

            ];
    }
}
