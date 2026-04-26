<?php

namespace App\Http\Controllers\Cms;

use App\Enums\DesignLayoutType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\SliderRequest;
use App\Models\Slider;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SliderController extends Controller
{
    public function index(): View
    {
        return view('cms.sliders.index', [
            'sliders' => Slider::query()
                ->orderBy('sort_order')
                ->orderBy('title')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('cms.sliders.create', [
            'slider' => new Slider(),
            'layoutOptions' => DesignLayoutType::options(),
        ]);
    }

    public function store(SliderRequest $request): RedirectResponse
    {
        $slider = Slider::create($this->validatedPayload($request));

        $this->syncMedia($request, $slider);

        return redirect()
            ->route('cms.sliders.index')
            ->with('status', 'Slider entry created.');
    }

    public function edit(Slider $slider): View
    {
        return view('cms.sliders.edit', [
            'slider' => $slider,
            'layoutOptions' => DesignLayoutType::options(),
        ]);
    }

    public function update(SliderRequest $request, Slider $slider): RedirectResponse
    {
        $slider->update($this->validatedPayload($request, $slider));

        $this->syncMedia($request, $slider);

        return redirect()
            ->route('cms.sliders.index')
            ->with('status', 'Slider entry updated.');
    }

    public function destroy(Slider $slider): RedirectResponse
    {
        $slider->delete();

        return redirect()
            ->route('cms.sliders.index')
            ->with('status', 'Slider entry deleted.');
    }

    private function validatedPayload(SliderRequest $request, ?Slider $slider = null): array
    {
        $payload = $request->validated();
        $userId = $request->user()?->id;

        unset($payload['image_upload']);

        $payload['created_by'] = $slider?->created_by ?? $userId;
        $payload['updated_by'] = $userId;

        return [
            ...$payload,
            'sort_order' => $request->integer('sort_order'),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function syncMedia(SliderRequest $request, Slider $slider): void
    {
        if ($request->hasFile('image_upload')) {
            $slider
                ->addMediaFromRequest('image_upload')
                ->toMediaCollection('slide_image');
        }
    }
}