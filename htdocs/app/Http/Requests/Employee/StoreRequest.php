<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use libphonenumber\NumberParseException;
use App\Models\Employee;

class StoreRequest extends FormRequest
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
            'photo' => [
                'bail',
                'max:5120',
                'mimetypes:image/jpeg,image/jpg,image/png',
                'dimensions:min_width=300,min_height=300',
                function($attribute,$value,$fail)
                {
                    if (!empty($this->file('photo'))){
                        $file_name_orig = $this->file('photo')->getClientOriginalName();
                        $file_name_tmp = $this->file('photo')->getFilename().'_'.$file_name_orig;
                        $this->file('photo')->move(storage_path('app').'/../tmp/',$file_name_tmp);
                        $this->session()->put('photo_tmp',$file_name_tmp);
                        $this->session()->put('photo_orig',$file_name_orig);
                    }
                }
            ],
            'full_name' => [
                'required',
                'max:256',
                'regex:/^([0-9а-яА-Яa-zA-Z\s\.,_їЇґҐєЄ\'-]+)$/uis',
                'unique:employees,full_name'
            ],
            'phone' => [
                'required',
                function ($attribute, $value, $fail) {
                    $phoneInstance = \libphonenumber\PhoneNumberUtil::getInstance();
                    try {
                        $test = $phoneInstance->parse($value,'UA');
                        if (!$phoneInstance->isValidNumber($test)){
                            $fail('Phone number is invalid. Shoul be valid ukrainian operator (+38 0XX XXX XX XX)');
                        }
                        elseif (!$phoneInstance->isValidNumberForRegion($test,'UA')){
                            $fail('Phone number is invalid for Ukraine');
                        }
                    } catch (NumberParseException $e){
                        $fail('Phone number is invalid');
                    }
                },
                'unique:employees,phone'
            ],
            'email' => [
                'required',
                'email',
                'unique:employees,email'
            ],
            'position_id' => [
                'required',
                'integer',
                'gt:0',
                'exists:App\Models\Position,id'
            ],
            'salary' => [
                'required',
                'numeric',
                'gt:0',
                'lte:500',
                'not_in:0'
            ],
            'parent_id' => [
                'present',
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
                        /*if ($employee->subs >= 10){
                            $fail('This worker can\'t have more employees.');
                        }*/
                    }
                },
            ],
            'date_start' => [
                'required',
                'date_format:d.m.Y'
            ]
        ];
    }


    public function messages()
    {
        return [
            'position' => 'Position should be valid. Choose among all the positions.',
            'salary.gt' => 'Salary should be more than 0 an less than 500',
            'salary:not_in' => 'Salary should be more than 0 an less than 500',
            'lte' => 'Salary should be more than 0 an less than 500'
        ];
    }
}
