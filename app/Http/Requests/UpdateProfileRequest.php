<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class UpdateProfileRequest extends FormRequest
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
        $rules = [
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|email:rfc,dns',

        ];
        if($this->request->get('password'))
        {
            $rules['password']='confirmed|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'first_name.required' => 'First Name Field is Required.',
            'last_name.required' => 'Last Name Field is Required.',
            'email.required' => 'Email Field is Required.',
            'password.confrimed'=>'Password not match',
            'password.regex'=>'Password Should Contain One Uppercase Letter,Oner Lowercase Letter,Oner Numeric Value,One Special Character',
            'password.min'=>'Password length should be 6 digit'
        ];
    }
}
