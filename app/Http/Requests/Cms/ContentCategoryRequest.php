<?php

namespace App\Http\Requests\Cms;

use App\Enums\DesignLayoutType;
use App\Models\Content;
use App\Support\CurrentWebsite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContentCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;
        $websiteId = app(CurrentWebsite::class)->id();

        return [
            'menu_item_id' => ['nullable', Rule::exists('menu_items', 'id')->where(fn ($query) => $query->where('website_id', $websiteId))],
            'name' => ['required', 'string', 'max:120'],
            'visibility' => ['required', Rule::in(Content::VISIBILITY_OPTIONS)],
            'slug' => ['nullable', 'string', 'max:140', Rule::unique('content_categories', 'slug')->where(fn ($query) => $query->where('website_id', $websiteId))->ignore($categoryId)],
            'layout_type' => ['required', Rule::in(DesignLayoutType::values())],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}