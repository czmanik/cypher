@props(['title' => null, 'model' => null])
@inject('footerSettings', 'App\Settings\FooterSettings')
@php
    $openingHours = \App\Models\OpeningHour::orderBy('day_of_week')->get();
    $daysMap = [1 => 'Po', 2 => 'Út', 3 => 'St', 4 => 'Čt', 5 => 'Pá', 6 => 'So', 7 => 'Ne'];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Měřící kódy (Google Analytics atd.) --}}
    @if($footerSettings->measuring_code)
        {!! $footerSettings->measuring_code !!}
    @endif

    {{-- SEO Komponenta --}}
    <x-seo-head :model="$model ?? null" :title="$title ?? null" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cypher-light text-gray-900 antialiased font-sans flex flex-col min-h-screen">

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

    <main class="flex-grow">
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

    <footer class="bg-cypher-dark text-white py-12 mt-auto border-t border-white/10">
        <div class="container mx-auto px-4">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12 text-center md:text-left">

                {{-- LEVÝ SLOUPEC --}}
                <div class="flex flex-col items-center md:items-start">
                    @if($footerSettings->column_left_type === 'text')
                        <div class="prose prose-invert prose-sm">
                            {!! $footerSettings->column_left_text !!}
                        </div>
                    @elseif($footerSettings->column_left_type === 'opening_hours')
                         <h4 class="text-lg font-bold mb-4 uppercase tracking-wider text-cypher-gold">Otevírací doba</h4>
                         <ul class="text-sm space-y-2 text-gray-300">
                            @foreach($openingHours as $oh)
                                <li class="flex justify-between w-full max-w-[200px]">
                                    <span class="font-bold w-8">{{ $daysMap[$oh->day_of_week] ?? '' }}</span>
                                    <span>
                                        @if($oh->is_closed)
                                            <span class="text-red-400">Zavřeno</span>
                                        @else
                                            {{ \Carbon\Carbon::parse($oh->bar_open)->format('H:i') }} - {{ \Carbon\Carbon::parse($oh->bar_close)->format('H:i') }}
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                         </ul>
                    @elseif($footerSettings->column_left_type === 'socials')
                        <h4 class="text-lg font-bold mb-4 uppercase tracking-wider text-cypher-gold">Sledujte nás</h4>
                        <div class="flex gap-4">
                            @foreach($footerSettings->social_links as $link)
                                <a href="{{ $link['url'] }}" target="_blank" class="hover:text-cypher-gold transition-colors" title="{{ $link['label'] ?? $link['network'] }}">
                                    @if($link['network'] === 'facebook')
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    @elseif($link['network'] === 'instagram')
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.073-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    @elseif($link['network'] === 'tiktok')
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.46-.54 2.94-1.5 4.14-1.42 1.79-3.8 2.88-6.1 2.54-2.83-.42-5.04-2.8-5.32-5.71-.3-3.12 1.68-6.11 4.61-6.92 1.09-.3 2.24-.31 3.36-.06v3.91c-.6-.18-1.25-.19-1.85.03-1.07.4-1.76 1.54-1.57 2.67.19 1.14 1.25 1.94 2.41 1.83 1.17-.11 2.15-1.06 2.18-2.24.03-3.08.01-6.15.01-9.23.01-3.69.01-7.39.01-11.08-.91-.2-1.87-.27-2.81-.17v-3.95z"/></svg>
                                    @else
                                        <span class="font-bold">{{ $link['label'] ?? 'Link' }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- PROSTŘEDNÍ SLOUPEC --}}
                <div class="flex flex-col items-center">
                    @if($footerSettings->column_center_type === 'text')
                        <div class="prose prose-invert prose-sm">
                            {!! $footerSettings->column_center_text !!}
                        </div>
                    @elseif($footerSettings->column_center_type === 'opening_hours')
                         <h4 class="text-lg font-bold mb-4 uppercase tracking-wider text-cypher-gold">Otevírací doba</h4>
                         <ul class="text-sm space-y-2 text-gray-300">
                            @foreach($openingHours as $oh)
                                <li class="flex justify-between w-full max-w-[200px]">
                                    <span class="font-bold w-8">{{ $daysMap[$oh->day_of_week] ?? '' }}</span>
                                    <span>
                                        @if($oh->is_closed)
                                            <span class="text-red-400">Zavřeno</span>
                                        @else
                                            {{ \Carbon\Carbon::parse($oh->bar_open)->format('H:i') }} - {{ \Carbon\Carbon::parse($oh->bar_close)->format('H:i') }}
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                         </ul>
                    @elseif($footerSettings->column_center_type === 'socials')
                        <h4 class="text-lg font-bold mb-4 uppercase tracking-wider text-cypher-gold">Sledujte nás</h4>
                         <div class="flex gap-4">
                            @foreach($footerSettings->social_links as $link)
                                <a href="{{ $link['url'] }}" target="_blank" class="hover:text-cypher-gold transition-colors" title="{{ $link['label'] ?? $link['network'] }}">
                                    @if($link['network'] === 'facebook')
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    @elseif($link['network'] === 'instagram')
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.073-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    @elseif($link['network'] === 'tiktok')
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.46-.54 2.94-1.5 4.14-1.42 1.79-3.8 2.88-6.1 2.54-2.83-.42-5.04-2.8-5.32-5.71-.3-3.12 1.68-6.11 4.61-6.92 1.09-.3 2.24-.31 3.36-.06v3.91c-.6-.18-1.25-.19-1.85.03-1.07.4-1.76 1.54-1.57 2.67.19 1.14 1.25 1.94 2.41 1.83 1.17-.11 2.15-1.06 2.18-2.24.03-3.08.01-6.15.01-9.23.01-3.69.01-7.39.01-11.08-.91-.2-1.87-.27-2.81-.17v-3.95z"/></svg>
                                    @else
                                        <span class="font-bold">{{ $link['label'] ?? 'Link' }}</span>
                                    @endif
                                </a>
                            @endforeach
                         </div>
                    @endif
                </div>

                {{-- PRAVÝ SLOUPEC --}}
                <div class="flex flex-col items-center md:items-end">
                    @if($footerSettings->column_right_type === 'text')
                        <div class="prose prose-invert prose-sm">
                            {!! $footerSettings->column_right_text !!}
                        </div>
                    @elseif($footerSettings->column_right_type === 'opening_hours')
                         <h4 class="text-lg font-bold mb-4 uppercase tracking-wider text-cypher-gold">Otevírací doba</h4>
                         <ul class="text-sm space-y-2 text-gray-300">
                            @foreach($openingHours as $oh)
                                <li class="flex justify-between w-full max-w-[200px]">
                                    <span class="font-bold w-8">{{ $daysMap[$oh->day_of_week] ?? '' }}</span>
                                    <span>
                                        @if($oh->is_closed)
                                            <span class="text-red-400">Zavřeno</span>
                                        @else
                                            {{ \Carbon\Carbon::parse($oh->bar_open)->format('H:i') }} - {{ \Carbon\Carbon::parse($oh->bar_close)->format('H:i') }}
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                         </ul>
                    @elseif($footerSettings->column_right_type === 'socials')
                        <h4 class="text-lg font-bold mb-4 uppercase tracking-wider text-cypher-gold">Sledujte nás</h4>
                         <div class="flex gap-4">
                            @foreach($footerSettings->social_links as $link)
                                <a href="{{ $link['url'] }}" target="_blank" class="hover:text-cypher-gold transition-colors" title="{{ $link['label'] ?? $link['network'] }}">
                                    @if($link['network'] === 'facebook')
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    @elseif($link['network'] === 'instagram')
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.073-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    @elseif($link['network'] === 'tiktok')
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.46-.54 2.94-1.5 4.14-1.42 1.79-3.8 2.88-6.1 2.54-2.83-.42-5.04-2.8-5.32-5.71-.3-3.12 1.68-6.11 4.61-6.92 1.09-.3 2.24-.31 3.36-.06v3.91c-.6-.18-1.25-.19-1.85.03-1.07.4-1.76 1.54-1.57 2.67.19 1.14 1.25 1.94 2.41 1.83 1.17-.11 2.15-1.06 2.18-2.24.03-3.08.01-6.15.01-9.23.01-3.69.01-7.39.01-11.08-.91-.2-1.87-.27-2.81-.17v-3.95z"/></svg>
                                    @else
                                        <span class="font-bold">{{ $link['label'] ?? 'Link' }}</span>
                                    @endif
                                </a>
                            @endforeach
                         </div>
                    @endif
                </div>

            </div>

            <div class="text-center pt-8 border-t border-white/10">
                <p class="text-gray-500 text-xs">{{ $footerSettings->copyright_text }}</p>
            </div>
        </div>
    </footer>

</body>
</html>
