<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\ValidationRules\Rules\Delimited;

class NewsListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => 'numeric',
            'limit' => 'numeric',
            'sources' => [(new Delimited('numeric'))],
            'categories' => [(new Delimited('numeric'))],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|after_or_equal:start_date',
        ];
    }
}
