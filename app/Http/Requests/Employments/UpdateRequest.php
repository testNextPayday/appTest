<?php

namespace App\Http\Requests\Employments;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (auth('affiliate')->check() || auth('staff')->check() || auth('admin')->check())
            return true;
            
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'employment_letter' => 'nullable|file|mimes:jpeg,jpg,png|max:10240',
            'confirmation_letter' => 'nullable|file|mimes:jpeg,jpg,png|max:10240',
            'work_id_card' => 'nullable|file|mimes:jpeg,jpg,png|max:10240'
        ];
    }
}
