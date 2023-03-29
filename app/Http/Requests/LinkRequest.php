<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;


class LinkRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $user = Auth::user();
        return [
            'link' => 
            [
                'required',
                'url',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->links()->count() >= 5) {
                        $fail("You can't create more than 5 links.");
                    }
                },
            ]
        ];
    }
}
