<?php


namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountChangePasswordRequest extends FormRequest
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
        switch($this->method()){
            case 'POST':

            case 'PUT':
                $account = Auth::user();

                return [
                    'new_password' => 'required|min:8',
                    'password' => ['required', 'min:8', function($attribute, $value, $fail) use ($account){
                        if(!Hash::check($value, $account->getAuthPassword())){
                            $fail('Senha incorreta!');
                        }
                    }]
                ];
            default:
                return null;
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

}
