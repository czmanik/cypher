<x-layouts.app title="Program & Akce | Cypher93">

    <div class="bg-cypher-dark text-white py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80')] bg-cover bg-center opacity-20"></div>
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-5xl font-bold uppercase tracking-tighter mb-4">
                Program <span class="text-cypher-gold">&</span> Events
            </h1>
            
            <div class="flex flex-wrap justify-center gap-4 mt-8">
                <a href="{{ route('events.index') }}" 
                   class="px-6 py-2 border rounded-full text-sm font-bold uppercase tracking-widest transition-all {{ !$filter ? 'bg-cypher-gold text-black border-cypher-gold' : 'border-white/30 text-gray-400 hover:border-white hover:text-white' }}">
                   Vše
                </a>
                <a href="{{ route('events.index', ['category' => 'kultura']) }}" 
                   class="px-6 py-2 border rounded-full text-sm font-bold uppercase tracking-widest transition-all {{ $filter === 'kultura' ? 'bg-purple-600 text-white border-purple-600' : 'border-white/30 text-gray-400 hover:border-purple-500 hover:text-white' }}">
                   Kultura
                </a>
                <a href="{{ route('events.index', ['category' => 'gastro']) }}" 
                   class="px-6 py-2 border rounded-full text-sm font-bold uppercase tracking-widest transition-all {{ $filter === 'gastro' ? 'bg-orange-500 text-white border-orange-500' : 'border-white/30 text-gray-400 hover:border-orange-500 hover:text-white' }}">
                   Gastro
                </a>
                <a href="{{ route('events.index', ['category' => 'piti']) }}" 
                   class="px-6 py-2 border rounded-full text-sm font-bold uppercase tracking-widest transition-all {{ $filter === 'piti' ? 'bg-blue-500 text-white border-blue-500' : 'border-white/30 text-gray-400 hover:border-blue-500 hover:text-white' }}">
                   Drink & Bar
                </a>
            </div>
        </div>
    </div>

    <div class="bg-black min-h-screen py-16 text-white">
        <div class="container mx-auto px-4">

            {{-- 1. SEKCE: PRÁVĚ BĚŽÍ (To nejvíc důležité) --}}
            @if($runningEvents->isNotEmpty())
                <div class="mb-20">
                    <h2 class="flex items-center justify-center gap-3 text-3xl font-bold uppercase tracking-widest text-red-500 mb-8 pb-4 text-center animate-pulse">
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        Právě probíhá
                    </h2>
                    <div class="flex flex-wrap justify-center gap-8">
                        @foreach($runningEvents as $event)
                            <div class="w-full md:w-[calc(50%-2rem)] lg:w-[calc(33.33%-2rem)] max-w-md border-2 border-red-500/50 shadow-[0_0_30px_rgba(239,68,68,0.2)]">
                                <x-event-card :event="$event" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 2. SEKCE: CHYSTÁME --}}
            @if($upcomingEvents->isNotEmpty())
                <h2 class="text-3xl font-bold uppercase tracking-widest text-cypher-gold mb-8 border-b border-white/10 pb-4 text-center">
                    Brzy uvidíte
                </h2>
                <div class="flex flex-wrap justify-center gap-8 mb-20">
                    @foreach($upcomingEvents as $event)
                        <div class="w-full md:w-[calc(50%-2rem)] lg:w-[calc(33.33%-2rem)] max-w-md">
                            <x-event-card :event="$event" />
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- 3. SEKCE: ARCHIV --}}
            @if($pastEvents->isNotEmpty())
                <h2 class="text-2xl font-bold uppercase tracking-widest text-gray-500 mb-8 border-b border-white/10 pb-4 text-center">
                    Proběhlé akce
                </h2>
                <div class="flex flex-wrap justify-center gap-8 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                    @foreach($pastEvents as $event)
                        <div class="w-full md:w-[calc(50%-2rem)] lg:w-[calc(33.33%-2rem)] max-w-md">
                             <x-event-card :event="$event" />
                        </div>
                    @endforeach
                </div>
            @endif
            
            @if($runningEvents->isEmpty() && $upcomingEvents->isEmpty() && $pastEvents->isEmpty())
                <div class="text-center py-20 text-gray-500">
                    <p>Žádné akce v této kategorii.</p>
                </div>
            @endif

        </div>
    </div>
</x-layouts.app>