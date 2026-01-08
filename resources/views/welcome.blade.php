<x-layouts.app title="Cypher93 | Home">
    
    <section class="relative h-screen min-h-[600px] flex items-center justify-center overflow-hidden">
        
        <div class="absolute inset-0 z-0">
            @if($heroBlock && $heroBlock->image_path)
                <img src="{{ asset('storage/' . $heroBlock->image_path) }}" 
                     class="w-full h-full object-cover {{ $isNight ? 'opacity-40' : 'opacity-80' }}" 
                     alt="Hero Background">
            @else
                <img src="{{ $isNight 
                    ? 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?q=80'
                    : 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?q=80'
                    }}" 
                    class="w-full h-full object-cover {{ $isNight ? 'opacity-40' : 'opacity-90' }}" 
                    alt="Atmosphere">
            @endif
            
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/20 to-cypher-dark"></div>
        </div>

        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto mt-[-5vh]">
            
            <div class="inline-flex items-center gap-2 px-4 py-2 mb-8 border border-white/20 backdrop-blur-md rounded-full">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $isOpen ? 'bg-green-400' : 'bg-red-400' }}"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 {{ $isOpen ? 'bg-green-500' : 'bg-red-500' }}"></span>
                </span>
                <span class="text-xs font-bold uppercase tracking-widest text-white">
                    {{ $isOpen ? 'Právě máme otevřeno' : 'Zavřeno' }} 
                    @if($today && !$today->is_closed)
                        <span class="opacity-60 ml-1 font-normal">({{ substr($today->bar_open, 0, 5) }} - {{ substr($today->bar_close, 0, 5) }})</span>
                    @endif
                </span>
            </div>
            
            <h1 class="text-6xl md:text-8xl font-bold mb-6 tracking-tighter text-white leading-[0.9]">
                @if($heroBlock)
                    {!! $heroBlock->title !!}
                @else
                    CYPHER<span class="text-cypher-gold">93</span>
                @endif
            </h1>
            
            <div class="text-lg md:text-2xl text-gray-200 mb-10 max-w-2xl mx-auto prose prose-invert">
                @if($heroBlock)
                    {!! $heroBlock->content !!}
                @else
                    Prostor, kde se denní káva mění v noční umění.
                @endif
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('menu') }}" class="px-8 py-4 bg-cypher-gold text-black font-bold uppercase tracking-wide hover:bg-white transition-all transform hover:-translate-y-1">
                    {{ $isNight ? 'Drink Menu' : 'Nabídka Kávy' }}
                </a>
                <a href="{{ route('reservations.create') }}" class="px-8 py-4 border border-white/40 text-white font-bold uppercase tracking-wide hover:bg-white hover:text-black transition-all backdrop-blur-sm">
                    Rezervovat stůl
                </a>
            </div>
        </div>
    </section>

    <section class="py-24 bg-white text-cypher-dark">
        <div class="container mx-auto px-4 grid md:grid-cols-2 gap-12 items-center">
            
            <div class="prose prose-lg max-w-none">
                <h2 class="text-4xl font-bold mb-6 text-black uppercase tracking-tight">
                    {{ $aboutBlock->title ?? 'O nás' }}
                </h2>
                <div class="text-gray-600">
                    {!! $aboutBlock->content ?? 'Doplňte text v administraci pod klíčem <code>about_us</code>.' !!}
                </div>
            </div>

            <div class="relative h-[500px] bg-gray-100 rounded overflow-hidden">
                @if($aboutBlock && $aboutBlock->image_path)
                     <img src="{{ asset('storage/' . $aboutBlock->image_path) }}" class="w-full h-full object-cover" alt="O nás">
                @else
                    <div class="flex items-center justify-center h-full text-gray-400">
                        (Zde se objeví obrázek z sekce O nás)
                    </div>
                @endif
            </div>

        </div>
    </section>

    @if($counts['all'] > 0)
    <section class="py-24 bg-cypher-dark text-white border-t border-white/10" x-data="{ activeTab: 'all' }">
        <div class="container mx-auto px-4">
            
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold uppercase tracking-widest mb-8">
                    Nadcházející <span class="text-cypher-gold">Akce</span>
                </h2>

                <div class="inline-flex flex-wrap justify-center gap-2 md:gap-4 border border-white/10 rounded-full p-2 bg-black/20 backdrop-blur-sm">
                    
                    {{-- Tlačítko VŠE --}}
                    <button @click="activeTab = 'all'"
                            :class="activeTab === 'all' ? 'bg-cypher-gold text-black border-cypher-gold' : 'text-gray-400 border-transparent hover:text-white'"
                            class="px-4 py-2 rounded-full text-xs md:text-sm font-bold uppercase tracking-wider border transition-all">
                        Vše <span class="opacity-60 text-[10px] align-top ml-1">({{ $counts['all'] }})</span>
                    </button>

                    {{-- Tlačítko KULTURA --}}
                    @if($counts['kultura'] > 0)
                    <button @click="activeTab = 'kultura'"
                            :class="activeTab === 'kultura' ? 'bg-purple-600 text-white border-purple-600' : 'text-gray-400 border-transparent hover:text-purple-400'"
                            class="px-4 py-2 rounded-full text-xs md:text-sm font-bold uppercase tracking-wider border transition-all">
                        Kultura <span class="opacity-60 text-[10px] align-top ml-1">({{ $counts['kultura'] }})</span>
                    </button>
                    @endif

                    {{-- Tlačítko GASTRO --}}
                    @if($counts['gastro'] > 0)
                    <button @click="activeTab = 'gastro'"
                            :class="activeTab === 'gastro' ? 'bg-orange-500 text-white border-orange-500' : 'text-gray-400 border-transparent hover:text-orange-400'"
                            class="px-4 py-2 rounded-full text-xs md:text-sm font-bold uppercase tracking-wider border transition-all">
                        Gastro <span class="opacity-60 text-[10px] align-top ml-1">({{ $counts['gastro'] }})</span>
                    </button>
                    @endif

                    {{-- Tlačítko PITÍ --}}
                    @if($counts['piti'] > 0)
                    <button @click="activeTab = 'piti'"
                            :class="activeTab === 'piti' ? 'bg-blue-500 text-white border-blue-500' : 'text-gray-400 border-transparent hover:text-blue-400'"
                            class="px-4 py-2 rounded-full text-xs md:text-sm font-bold uppercase tracking-wider border transition-all">
                        Drink <span class="opacity-60 text-[10px] align-top ml-1">({{ $counts['piti'] }})</span>
                    </button>
                    @endif
                </div>
            </div>

            <div class="min-h-[300px]">
                
                {{-- 1. SEZNAM: VŠE --}}
                <div x-show="activeTab === 'all'" x-transition.opacity.duration.300ms>
                    <div class="grid md:grid-cols-3 gap-8">
                        @foreach($eventsLists['all'] as $event)
                            <x-event-card :event="$event" />
                        @endforeach
                    </div>
                </div>

                {{-- 2. SEZNAM: KULTURA --}}
                <div x-show="activeTab === 'kultura'" x-transition.opacity.duration.300ms style="display: none;">
                    <div class="grid md:grid-cols-3 gap-8">
                        @foreach($eventsLists['kultura'] as $event)
                            <x-event-card :event="$event" />
                        @endforeach
                    </div>
                </div>

                {{-- 3. SEZNAM: GASTRO --}}
                <div x-show="activeTab === 'gastro'" x-transition.opacity.duration.300ms style="display: none;">
                    <div class="grid md:grid-cols-3 gap-8">
                        @foreach($eventsLists['gastro'] as $event)
                            <x-event-card :event="$event" />
                        @endforeach
                    </div>
                </div>

                {{-- 4. SEZNAM: PITÍ --}}
                <div x-show="activeTab === 'piti'" x-transition.opacity.duration.300ms style="display: none;">
                    <div class="grid md:grid-cols-3 gap-8">
                        @foreach($eventsLists['piti'] as $event)
                            <x-event-card :event="$event" />
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="text-center mt-12">
                <a :href="'{{ route('events.index') }}' + (activeTab !== 'all' ? '?category=' + activeTab : '')"
                   class="inline-flex items-center gap-2 border-b border-cypher-gold pb-1 text-sm font-bold uppercase tracking-widest text-cypher-gold hover:text-white hover:border-white transition-colors">
                   <span>Zobrazit kompletní program</span>
                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
                   </svg>
                </a>
            </div>

        </div>
    </section>
    @endif

</x-layouts.app>