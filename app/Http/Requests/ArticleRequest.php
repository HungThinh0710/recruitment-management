<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            case 'GET':
                {return [];}
            case 'PUT':{return[];}
            case 'POST':
            {
                return [
                    'title'   =>'required|max:255',
                    'content' =>'required',
                    'image'   =>'max:255',
                    'jobId'   =>'required|integer|exists:jobs,id',
                    'catId'   =>'required|integer|exists:categories,id',
                ];
            }
            case 'DELETE': {
                return [
                    "articleId" => "required|array"
                ];
            }
                break;
            default:
                break;
        }
    }
}
