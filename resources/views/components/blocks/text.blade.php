@props(['data'])

<section class="py-12 bg-white">
    <div class="container mx-auto px-4 max-w-4xl">
        @if(!empty($data['title']))
            <h2 class="text-3xl font-bold mb-6 text-gray-900">{{ $data['title'] }}</h2>
        @endif

        <div class="prose max-w-none text-gray-700">
            {!! $data['content'] !!}
        </div>
    </div>
</section>
