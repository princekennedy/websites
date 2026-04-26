<?php

namespace App\Http\Requests\Cms;

use App\Enums\MenuLayoutType;
use App\Models\Menu;
use App\Support\CurrentWebsite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
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
        $menuId = $this->route('menu')?->id;
        $websiteId = app(CurrentWebsite::class)->id();

        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140', Rule::unique('menus', 'slug')->where(fn ($query) => $query->where('website_id', $websiteId))->ignore($menuId)],
            'description' => ['nullable', 'string'],
            'layout_type' => ['required', Rule::in(MenuLayoutType::values())],
            'location' => ['nullable', 'string', 'max:255'],
            'visibility' => ['required', Rule::in(Menu::VISIBILITY_OPTIONS)],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}