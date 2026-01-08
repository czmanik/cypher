<x-layouts.app title="Nabídka | Cypher93">

    <div class="bg-cypher-dark text-white py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/40 z-0"></div> 
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-5xl md:text-7xl font-bold uppercase tracking-tighter mb-4">
                Naše <span class="text-cypher-gold">Nabídka</span>
            </h1>
            <p class="text-gray-300 text-lg max-w-2xl mx-auto">
                Od výběrové kávy přes domácí limonády až po signature koktejly.
            </p>
        </div>
    </div>

    <div class="bg-white min-h-screen py-16">
        <div class="container mx-auto px-4 max-w-4xl">

            @if($categories->isEmpty())
                <div class="text-center py-20">
                    <p class="text-gray-500 text-xl">Zatím jsme do systému nic nenahráli.</p>
                    <p class="text-sm text-gray-400 mt-2">Jdi do /admin -> Produkty a přidej něco dobrého.</p>
                </div>
            @else
                
                @foreach($categories as $category)
                    <div class="mb-16 scroll-mt-24" id="{{ Str::slug($category->name) }}">
                        
                        <div class="flex items-center gap-4 mb-8">
                            <h2 class="text-3xl font-bold uppercase tracking-wide text-cypher-dark">
                                {{ $category->name }}
                            </h2>
                            <div class="h-px bg-gray-200 flex-grow"></div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            @foreach($category->products as $product)
                                <div class="flex justify-between items-start group">
                                    <div class="pr-4">
                                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-cypher-gold transition-colors uppercase">
                                            {{ $product->name }}
                                        </h3>
                                        @if($product->description)
                                            <p class="text-gray-500 text-sm mt-1 leading-relaxed">
                                                {{ $product->description }}
                                            </p>
                                        @endif
                                    </div>
                                    
                                    <div class="text-right shrink-0">
                                        <span class="text-lg font-bold text-cypher-dark">
                                            {{ number_format($product->price, 0, ',', ' ') }} Kč
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

            @endif

        </div>
    </div>

</x-layouts.app>