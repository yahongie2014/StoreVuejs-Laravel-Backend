<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserValdation extends FormRequest
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
            'phone' => 'required|unique:users|min:11|max:13',
            'password' => 'required',
            'name_ar' => 'required|string|regex:/\p{Arabic}/u',
            'name' => 'required',
            'date' => 'required',
        ];
    }
}
