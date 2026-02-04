@props(['data'])

@php
    $images = $data['images'] ?? [];
    $title = $data['title'] ?? null;
@endphp

@if(count($images) > 0)
<section class="py-12 bg-white" x-data="{
        lightboxOpen: false,
        lightboxImage: '',
        lightboxCaption: '',
        openLightbox(url, caption) {
            this.lightboxImage = url;
            this.lightboxCaption = caption;
            this.lightboxOpen = true;
            document.body.style.overflow = 'hidden';
        },
        closeLightbox() {
            this.lightboxOpen = false;
            this.lightboxImage = '';
            this.lightboxCaption = '';
            document.body.style.overflow = 'auto';
        }
    }"
    @keydown.escape.window="closeLightbox()"
>
    <div class="container mx-auto px-4">
        @if($title)
            <h2 class="text-3xl font-bold text-center mb-8">{{ $title }}</h2>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($images as $item)
                @php
                    $url = \Illuminate\Support\Facades\Storage::url($item['image']);
                    $caption = $item['caption'] ?? '';
                    $alt = $item['alt'] ?? $caption ?? 'Gallery Image';
                    // Bezpečné escapování pro JS
                    $jsCaption = json_encode($caption);
                @endphp
                <div class="group relative aspect-square overflow-hidden rounded-lg cursor-pointer bg-gray-100"
                     @click="openLightbox('{{ $url }}', {{ $jsCaption }})">
                    <img src="{{ $url }}"
                         alt="{{ $alt }}"
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">

                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                        </svg>
                    </div>

                    @if($caption)
                        <div class="absolute bottom-0 left-0 right-0 p-2 bg-black/50 text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity truncate">
                            {{ $caption }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Lightbox Modal --}}
    <template x-teleport="body">
        <div x-show="lightboxOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90 p-4"
             style="display: none;"
        >
            {{-- Close Button --}}
            <button @click="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Image Container --}}
            <div class="relative max-w-full max-h-full flex flex-col items-center justify-center" @click.outside="closeLightbox()">
                <img :src="lightboxImage" class="max-w-full max-h-[85vh] object-contain shadow-2xl rounded-sm" alt="Gallery Preview">

                {{-- Caption --}}
                <div x-show="lightboxCaption" class="mt-4 text-white text-center text-lg font-medium bg-black/50 px-4 py-2 rounded">
                    <span x-text="lightboxCaption"></span>
                </div>
            </div>
        </div>
    </template>
</section>
@endif
