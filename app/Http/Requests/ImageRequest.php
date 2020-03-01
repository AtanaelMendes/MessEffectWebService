<?php


namespace App\Http\Requests;


class ImageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(){
        switch($this->method()) {
            case 'POST':
                return [
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
                ];
        }
    }

    public function messages()
    {
        return [];
    }

}
