<section class="relative h-[60vh] flex items-center justify-center text-white">
    {{-- Pozadí --}}
    @if(isset($data['image']))
        <div class="absolute inset-0">
            <img src="{{ Storage::url($data['image']) }}" class="w-full h-full object-cover brightness-50">
        </div>
    @endif
    
    {{-- Text --}}
    <div class="relative z-10 text-center px-4">
        <h1 class="text-5xl font-bold mb-4">{{ $data['headline'] }}</h1>
        @if(!empty($data['subheadline']))
            <p class="text-xl opacity-90">{{ $data['subheadline'] }}</p>
        @endif
    </div>
</section>