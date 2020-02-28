<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
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
                return [
                    'nome' => 'required|min:3|max:30',
                    'email' => 'required|email|unique:accounts',
                    'password' => 'required|min:8'
                ];
            case 'PUT':
                $id = array_get($this->route()[2], 'id', null);
                return [
                    'nome' => 'nullable|min:3|max:30',
                    'email' => ['email', Rule::unique('accounts')->ignore($id)],
                    'password' => 'nullable|min:8'
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
