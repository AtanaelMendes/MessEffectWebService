<?php


namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountInfoRequest extends FormRequest
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
                    'nome' => 'nullable|min:3|max:30',
                    'email' => ['nullable', 'min:8', 'email', Rule::unique('accounts')->ignore($account->id)],
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
        return [
            'email.unique' => 'Email já existente!'
        ];
    }

}
