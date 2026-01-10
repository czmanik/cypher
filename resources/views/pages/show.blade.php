<x-layouts.app :title="$page->title">
    
    {{-- Tady začíná obsah, který se vloží do {{ $slot }} v layoutu --}}
    
    <div class="page-content">
        @if($page->content)
            @foreach($page->content as $block)
                
                {{-- Načtení konkrétního bloku (Hero, Text atd.) --}}
                @include('components.blocks.' . $block['type'], ['data' => $block['data']])
                
            @endforeach
        @endif
    </div>

</x-layouts.app>