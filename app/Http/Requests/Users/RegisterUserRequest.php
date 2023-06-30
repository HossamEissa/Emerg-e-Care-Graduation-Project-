<?php

namespace App\Http\Requests\Users;

use App\Traits\responseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RegisterUserRequest extends FormRequest
{
    use responseTrait;
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
            'name' => 'required|string|max:255',
            'national_id' => 'required|numeric|digits:14|unique:users',
            'phone_number' => 'required|numeric|digits:10|unique:users',
            'gender' => 'required',
            'age' => 'required|numeric',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|min:8'
        ];
    }

//    public function failedValidation(Validator $validator)
//    {
//        throw (new ValidationException($validator))
//            ->errorBag($this->returnValidationError($validator))
//            ->redirectTo(null);
//    }


    public function messages()
    {
        return [

        ];
    }
}
