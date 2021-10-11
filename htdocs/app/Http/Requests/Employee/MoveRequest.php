<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use libphonenumber\NumberParseException;
use App\Models\Employee;

class MoveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
            'parent_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!empty($value))
                    {
                        $employee = Employee::find($value);
                        if (empty($employee->id)){
                            $fail('The head employee not found');
                        }
                        if ($employee->level >= 4){
                            $fail('This worker can\'t manage employees.');
                        }
                        /*if ($value != $employee->id && $employee->subs >= 10){
                            $fail('This worker can\'t have more employees.');
                        }*/
                    }
                },
            ]
        ];
    }

}
