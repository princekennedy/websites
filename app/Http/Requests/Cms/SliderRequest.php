<?php

namespace App\Http\Requests\Cms;

use App\Enums\DesignLayoutType;
use App\Support\CurrentWebsite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sliderId = $this->route('slider')?->id;
        $websiteId = app(CurrentWebsite::class)->id();

        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('sliders', 'slug')->where(fn ($query) => $query->where('website_id', $websiteId))->ignore($sliderId)],
            'kicker' => ['nullable', 'string', 'max:120'],
            'layout_type' => ['required', Rule::in(DesignLayoutType::values())],
            'caption' => ['nullable', 'string'],
            'primary_button_text' => ['nullable', 'string', 'max:80'],
            'primary_button_link' => ['nullable', 'string', 'max:255'],
            'secondary_button_text' => ['nullable', 'string', 'max:80'],
            'secondary_button_link' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image_upload' => ['nullable', 'image', 'max:5120'],
        ];
    }
}