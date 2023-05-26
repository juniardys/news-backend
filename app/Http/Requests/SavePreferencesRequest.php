<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\ValidationRules\Rules\Delimited;

class SavePreferencesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sources' => ['present', (new Delimited('numeric'))],
            'categories' => ['present', (new Delimited('numeric'))],
            'authors' => ['present', (new Delimited('numeric'))],
        ];
    }
}
