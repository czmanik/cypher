@props(['event'])

<article class="group relative bg-white/5 border border-white/10 hover:border-cypher-gold transition-colors duration-300 flex flex-col h-full">
    
    <a href="{{ route('events.show', $event->slug) }}" class="absolute inset-0 z-10"></a>

    <div class="aspect-video overflow-hidden bg-gray-900 relative">
        @if($event->image_path)
            <img src="{{ asset('storage/' . $event->image_path) }}" 
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-80 group-hover:opacity-100" 
                 alt="{{ $event->title }}">
        @endif
        
        {{-- LOGIKA PRO KATEGORIE --}}
        @php
            $colors = [
                'kultura' => 'bg-purple-600',
                'gastro' => 'bg-orange-500',
                'piti' => 'bg-blue-500',
            ];
            $labels = [
                'kultura' => 'Kultura',
                'gastro' => 'Gastro',
                'piti' => 'Drink',
            ];
            // Pokud kategorie chybí, použijeme default
            $catKey = $event->category ?? 'kultura'; 
            $catColor = $colors[$catKey] ?? 'bg-gray-600';
            $catLabel = $labels[$catKey] ?? 'Akce';
        @endphp

        <div class="absolute top-4 left-4 {{ $catColor }} text-white text-xs font-bold px-2 py-1 uppercase tracking-wider rounded shadow-lg z-20">
            {{ $catLabel }}
        </div>
        
        <div class="absolute top-0 right-0 bg-cypher-gold text-black font-bold px-4 py-2 text-center leading-tight z-20">
            <span class="block text-xl">{{ $event->start_at->format('d') }}</span>
            <span class="block text-xs uppercase">{{ $event->start_at->format('M') }}</span>
        </div>
    </div>

    <div class="p-6 flex-grow flex flex-col">
        <div class="text-cypher-gold text-xs font-bold uppercase tracking-widest mb-2">
            {{ $event->start_at->format('l') }} • {{ $event->start_at->format('H:i') }}
            @if($event->end_at)
                – {{ $event->end_at->format('H:i') }}
            @endif
        </div>
        
        <h2 class="text-2xl font-bold mb-3 group-hover:text-cypher-gold transition-colors leading-tight">
            {{ $event->title }}
        </h2>
        
        <p class="text-gray-400 text-sm mb-6 line-clamp-3 flex-grow">
            {{ $event->perex }}
        </p>

        <span class="inline-block text-sm font-bold uppercase tracking-widest border-b border-cypher-gold pb-1 self-start group-hover:text-white text-gray-400">
            Detail akce &rarr;
        </span>
    </div>
</article>