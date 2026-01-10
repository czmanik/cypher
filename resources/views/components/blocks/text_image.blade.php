<section class="container mx-auto py-16 px-4">
    <div class="grid md:grid-cols-2 gap-12 items-center">
        
        {{-- Logika pro prohození stran --}}
        <div class="@if($data['layout'] === 'right') order-2 @endif">
            @if(isset($data['image']))
                <img src="{{ Storage::url($data['image']) }}" class="rounded-lg shadow-xl">
            @endif
        </div>

        <div class="@if($data['layout'] === 'right') order-1 @endif">
            @if(!empty($data['title']))
                <h2 class="text-3xl font-bold mb-6 text-gray-800">{{ $data['title'] }}</h2>
            @endif
            <div class="prose prose-lg text-gray-600">
                {!! $data['text'] !!} {{-- RichText vrací HTML --}}
            </div>
        </div>
        
    </div>
</section>