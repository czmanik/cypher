<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cypher93' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cypher-light text-gray-900 antialiased font-sans">

    {{-- HLAVIČKA s Alpine.js pro ovládání mobilního menu --}}
    <header class="bg-white shadow-sm sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <a href="/" class="text-2xl font-bold tracking-tighter uppercase z-50 relative">
                Cypher<span class="text-cypher-gold">93</span>
            </a>
            
            <nav class="hidden md:flex gap-6 font-medium text-sm uppercase tracking-wide items-center">
                
                @if(isset($globalMenu))
                    @foreach($globalMenu as $item)
                        {{-- Pokud je to stránka a není aktivní, přeskočíme --}}
                        @if(!$item->is_visible) @continue @endif

                        <a href="{{ $item->url }}" 
                           class="hover:text-cypher-gold transition-colors {{ request()->fullUrl() === $item->url ? 'text-cypher-gold' : '' }}"
                           @if($item->new_tab) target="_blank" @endif
                        >
                            {{ $item->label }}
                        </a>
                    @endforeach
                @endif

                <a href="{{ route('reservations.create') }}" class="px-5 py-2 bg-black text-white font-bold rounded hover:bg-cypher-gold hover:text-black transition-all">
                    Rezervace
                </a>
            </nav>

            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-3xl z-50 relative focus:outline-none">
                <span x-show="!mobileMenuOpen">☰</span>
                <span x-show="mobileMenuOpen" x-cloak>✕</span>
            </button>
        </div>

        <div x-show="mobileMenuOpen" 
             @click.outside="mobileMenuOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-5"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-5"
             class="md:hidden absolute top-16 left-0 w-full bg-white shadow-lg border-t border-gray-100 py-6 px-4 flex flex-col gap-4 text-center z-40"
             x-cloak>
            
            @if(isset($globalMenu))
                @foreach($globalMenu as $item)
                    @if(!$item->is_visible) @continue @endif

                    <a href="{{ $item->url }}" 
                       @click="mobileMenuOpen = false"
                       class="text-lg font-medium uppercase py-2 border-b border-gray-50 hover:text-cypher-gold {{ request()->fullUrl() === $item->url ? 'text-cypher-gold' : '' }}"
                       @if($item->new_tab) target="_blank" @endif
                    >
                        {{ $item->label }}
                    </a>
                @endforeach
            @endif

            <a href="{{ route('reservations.create') }}" @click="mobileMenuOpen = false" class="mt-2 px-5 py-3 bg-black text-white font-bold rounded uppercase">
                Rezervace
            </a>
        </div>
    </header>

    <main>
        @if (session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition
                 x-init="setTimeout(() => show = false, 5000)"
                 class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-4 rounded shadow-2xl z-50 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        {{ $slot }}
    </main>

    <footer class="bg-cypher-dark text-white py-12 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <h3 class="text-xl font-bold mb-4">Cypher<span class="text-cypher-gold">93</span></h3>
            <p class="text-gray-400 text-sm mb-6">Koněvova, Praha 3 - Žižkov</p>
            <p class="text-gray-600 text-xs">© {{ date('Y') }} Cypher93. Všechna práva vyhrazena.</p>
        </div>
    </footer>

</body>
</html>