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
        return [
            "title" => ["required", "unique:articles,title"],
            "description" => ["required"],
        ];
    }

    public function messages()
    {
        return [
            "title.unique" => "Article with same name is already created",
            "title.required" => "Article's title is required",
            "description.required" => "Article's description is required",
        ];
    }
}