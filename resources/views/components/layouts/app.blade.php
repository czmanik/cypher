<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cypher93' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cypher-light text-gray-900 antialiased font-sans">

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <a href="/" class="text-2xl font-bold tracking-tighter uppercase">
                Cypher<span class="text-cypher-gold">93</span>
            </a>
            
            <nav class="hidden md:flex gap-6 font-medium text-sm uppercase tracking-wide">
                <a href="/" class="hover:text-cypher-gold transition-colors">Domů</a>
                <a href="{{ route('menu') }}" class="hover:text-cypher-gold transition-colors">Menu</a>
                <a href="{{ route('events.index') }}" class="hover:text-cypher-gold transition-colors">Akce</a>
                <a href="#" class="hover:text-cypher-gold transition-colors">O nás</a>
                <a href="{{ route('reservations.create') }}" class="px-5 py-2 bg-black text-white font-bold rounded hover:bg-cypher-gold hover:text-black transition-all">
                    Rezervace
                </a>
            </nav>

            <button class="md:hidden text-2xl">☰</button>
        </div>
    </header>

    <main>
        @if (session('success'))
            <div x-data="{ show: true }" 
                x-show="show" 
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