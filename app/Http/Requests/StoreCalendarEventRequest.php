<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalendarEventRequest extends FormRequest
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
            'id' => ['nullable', 'integer'],
            'title' => ['required','string'],
            'description' => ['nullable','string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'location' => ['nullable','string'],
            'url' => ['nullable','url'],
        ];
    }
}
