@props(['data'])

<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4 max-w-4xl text-center">
        <blockquote class="text-2xl md:text-3xl font-serif italic text-gray-800 leading-relaxed">
            "{{ $data['text'] }}"
        </blockquote>

        @if(!empty($data['author']))
            <div class="mt-6 text-lg font-medium text-gray-600">
                — {{ $data['author'] }}
            </div>
        @endif
    </div>
</section>
