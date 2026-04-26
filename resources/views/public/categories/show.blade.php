<x-layouts.site :title="$category->name.' | '.data_get($publicSite ?? [], 'brand.name', 'SRHR Connect')">
  @include('designs.content-categories.'.$category->normalizedLayoutType())
</x-layouts.site>
