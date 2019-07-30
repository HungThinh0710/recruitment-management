<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormatArticleRequest extends FormRequest
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
        switch ($this->method()) {
            case 'POST':
            case 'PUT':
                {
                    return [
                        "title" => "required|max:255",
                        "content" => "required"
                    ];
                }
            case 'DELETE' :
                {
                    return [
                        "formatId" => "required|array",
                        "status"   => "required|string"
                    ];
                }
            default:
                break;
        }
    }
}
