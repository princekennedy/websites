<x-layouts.app title="Edit Service" eyebrow="CMS Referrals" heading="Edit service directory entry" subheading="Update service availability, contacts, and audience targeting.">
    <form method="POST" action="{{ route('cms.services.update', $service) }}">
        @csrf
        @method('PUT')
        @include('cms.services._form', ['submitLabel' => 'Update service'])
    </form>
</x-layouts.app>