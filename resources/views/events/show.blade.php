<x-layouts.app :model="$event">

    <div class="relative h-[60vh] min-h-[400px] flex items-end">
        <div class="absolute inset-0">
            @if($event->image_path)
                <img src="{{ asset('storage/' . $event->image_path) }}" class="w-full h-full object-cover" alt="{{ $event->title }}">
            @else
                <div class="w-full h-full bg-cypher-dark"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10 pb-12 md:pb-20">
            <a href="{{ route('events.index') }}" class="inline-flex items-center text-gray-400 hover:text-white mb-6 text-sm uppercase tracking-widest transition-colors">
                &larr; Zpět na program
            </a>
            
            <div class="flex flex-col md:flex-row gap-6 md:items-end">
                <div class="bg-cypher-gold text-black p-4 text-center min-w-[100px]">
                    <span class="block text-4xl font-bold">{{ $event->start_at->format('d') }}</span>
                    <span class="block text-lg font-bold uppercase">{{ $event->start_at->format('M') }}</span>
                </div>

                <div>
                    <h1 class="text-4xl md:text-6xl font-bold text-white mb-2 leading-none">
                        {{ $event->title }}
                    </h1>
                    <p class="text-xl text-gray-300">
                        {{ $event->start_at->format('l, H:i') }}
                        @if($event->end_at)
                            – {{ $event->end_at->format('H:i') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white py-16 min-h-[50vh]">
        <div class="container mx-auto px-4 max-w-3xl">
            
            <div class="prose prose-lg prose-headings:font-bold prose-a:text-cypher-gold hover:prose-a:text-black mx-auto">
                <p class="lead font-bold text-xl text-gray-800 border-l-4 border-cypher-gold pl-4 italic">
                    {{ $event->perex }}
                </p>
                
                <div class="mt-8">
                    {!! $event->description !!}
                </div>

                <div class="mt-8">
                    {{-- Zobrazit formulář jen pro komerční akce --}}
                    @if($event->is_commercial)
                        <livewire:event-claim-form :event="$event" />
                    @endif
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-200 flex justify-between items-center">
                <span class="text-gray-500 text-sm font-bold uppercase">Sdílet akci</span>
                <div class="flex gap-4">
                   <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="text-gray-400 hover:text-black transition-colors">Facebook</a>
                   <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($event->title) }}" target="_blank" class="text-gray-400 hover:text-black transition-colors">Twitter</a>
                   <a href="https://wa.me/?text={{ urlencode($event->title . ' ' . request()->fullUrl()) }}" target="_blank" class="text-gray-400 hover:text-black transition-colors">WhatsApp</a>
                </div>
            </div>

        </div>
    </div>

</x-layouts.app>