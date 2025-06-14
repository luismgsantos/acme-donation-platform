<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonateCampaignRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<string,string>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1',
        ];
    }
}
