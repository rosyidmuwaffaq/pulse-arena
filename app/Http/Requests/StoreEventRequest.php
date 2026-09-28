<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'division'    => ['required', Rule::in(['Combat Robot', 'Line Follower', 'Drone Racing', 'Robo-Soccer'])],
            'description' => ['nullable', 'string'],
            'location'    => ['required', 'string', 'max:255'],
            'event_date'  => ['required', 'date', 'after:now'],
            'price'       => ['required', 'integer', 'min:1'],
            'quota'       => ['required', 'integer', 'min:1'],
        ];
    }
}