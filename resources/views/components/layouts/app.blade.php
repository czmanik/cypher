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
                <a href="#" class="hover:text-cypher-gold transition-colors">Menu</a>
                <a href="#" class="hover:text-cypher-gold transition-colors">Akce</a>
                <a href="#" class="hover:text-cypher-gold transition-colors">O nás</a>
                <a href="#" class="px-5 py-2 bg-black text-white font-bold rounded hover:bg-cypher-gold hover:text-black transition-all">
                    Rezervace
                </a>
            </nav>

            <button class="md:hidden text-2xl">☰</button>
        </div>
    </header>

    <main>
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