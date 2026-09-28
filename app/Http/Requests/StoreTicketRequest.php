<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'buyer_name'  => ['required', 'string', 'max:255'],
            'buyer_email' => ['required', 'email', 'max:255'],
        ];
    }
}