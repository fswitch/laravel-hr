<?php

namespace App\Http\Requests\Position;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Position;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(Position $position)
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
            'title' => [
                'bail',
                'required',
                'max:256',
                'regex:/^[0-9а-яА-Яa-zA-Z\s\.,\-_їЇґҐєЄ]+$/uis'
            ]
        ];
    }
}
