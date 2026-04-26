<x-layouts.app title="New Service" eyebrow="CMS Referrals" heading="Create service directory entry" subheading="Add a youth-friendly facility or referral destination.">
    <form method="POST" action="{{ route('cms.services.store') }}">
        @csrf
        @include('cms.services._form', ['submitLabel' => 'Create service'])
    </form>
</x-layouts.app>