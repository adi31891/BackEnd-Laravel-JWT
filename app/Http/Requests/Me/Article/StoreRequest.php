<?php

namespace App\Http\Requests\Me\Article;

use App\Models\Category;
use Illuminate\Validation\Rule;
use App\Traits\ErrorResponseJson;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    use ErrorResponseJson;

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
        return [
            'category_id' => 'required|' . Rule::in(Category::class::pluck('id')),
            'title' => 'required|string|max:190',
            'content' =>'required|string',
            'featured_image' => 'required|image|mimes:png,jpg,jpeg.bmp',
        ];
    }

    public function attributes()
    {
        return [
            'category_id' => 'category',
        ];
    }
}