<?php

namespace App\Http\Requests;

use App\Synonyms;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SynonymsRequest extends FormRequest
{
    /**
     * Determine if the synonyms is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'synonyms' => [
                'required', 'min:3'
            ]
        ];
    }
}
