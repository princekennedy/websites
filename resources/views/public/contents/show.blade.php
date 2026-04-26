<x-layouts.site :title="$content->title.' | '.data_get($publicSite ?? [], 'brand.name', 'Brandly')">
  @include('designs.content.'.$content->normalizedLayoutType())
</x-layouts.site>
