<div class="my-8">
    
    {{-- 1. STAV: ÚSPĚCH - ZOBRAZENÍ QR KÓDU --}}
    @if($success)
        <div class="bg-green-50 border border-green-200 rounded-xl p-8 text-center animate-pulse-once">
            <h3 class="text-2xl font-bold text-green-800 mb-2">Voucher aktivován!</h3>
            <p class="text-green-600 mb-6">Ukažte tento kód obsluze na baru.</p>
            
            <div class="bg-white p-4 inline-block rounded-lg shadow-lg">
                {!! $qrCodeSvg !!}
            </div>

            @if($claimCode)
                <div class="mt-4">
                    <p class="text-sm text-gray-500 mb-1">Kód pro obsluhu:</p>
                    <div class="font-mono text-4xl font-bold text-gray-900 bg-white px-8 py-4 rounded-lg inline-block border-2 border-dashed border-gray-300 tracking-widest">
                        {{ $claimCode }}
                    </div>
                </div>
            @endif

            <p class="text-xs text-gray-400 mt-4 uppercase tracking-widest">Platné pro 1 osobu</p>
        </div>

    {{-- 2. STAV: VÝZVA K AKCI --}}
    @else
        <div class="bg-gray-100 rounded-xl p-6 border border-gray-200">
            
            {{-- POČÍTADLO VOUCHERŮ --}}
            @if($event->capacity_limit)
                <div class="mb-4">
                    <div class="flex justify-between text-sm font-bold uppercase tracking-widest mb-1">
                        <span class="text-gray-500">Zbývá voucherů</span>
                        <span class="{{ $event->remaining_capacity < 10 ? 'text-red-500' : 'text-cypher-gold' }}">
                            {{ $event->remaining_capacity }} / {{ $event->capacity_limit }}
                        </span>
                    </div>
                    {{-- Progress Bar --}}
                    <div class="w-full bg-gray-300 rounded-full h-2.5">
                        <div class="bg-cypher-gold h-2.5 rounded-full transition-all duration-1000" 
                             style="width: {{ ($event->remaining_capacity / $event->capacity_limit) * 100 }}%"></div>
                    </div>
                </div>
            @endif

            {{-- HLAVNÍ TLAČÍTKO --}}
            @if($event->remaining_capacity > 0)
                <button wire:click="openModal" 
                        class="w-full bg-black text-white text-xl font-bold uppercase py-4 rounded hover:bg-cypher-gold hover:text-black transition-all shadow-lg transform hover:-translate-y-1">
                    Využít akci
                </button>
            @else
                <button disabled class="w-full bg-gray-300 text-gray-500 text-xl font-bold uppercase py-4 rounded cursor-not-allowed">
                    Kapacita vyčerpána
                </button>
            @endif
        </div>
    @endif

    {{-- 3. MODÁLNÍ OKNO (FORMULÁŘ) --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden">
                
                <div class="bg-gray-100 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-lg">Získat slevu</h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-500 hover:text-black text-2xl">&times;</button>
                </div>

                <div class="p-6">
                    <p class="text-sm text-gray-500 mb-4">Pro vygenerování voucheru prosím vyplňte kontaktní údaje.</p>

                    <form wire:submit.prevent="submit" class="space-y-4">
                        
                        {{-- Zobrazit pole jen pokud jsou vyžadována --}}
                        @if(in_array('email', $event->required_fields ?? []))
                            <div>
                                <label class="block text-sm font-bold mb-1">Email</label>
                                <input wire:model="email" type="email" class="w-full border-2 border-gray-400 rounded focus:border-cypher-gold focus:ring-cypher-gold">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        @if(in_array('phone', $event->required_fields ?? []))
                            <div>
                                <label class="block text-sm font-bold mb-1">Telefon</label>
                                <input wire:model="phone" type="text" class="w-full border-2 border-gray-400 rounded focus:border-cypher-gold focus:ring-cypher-gold">
                                @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        @if(in_array('instagram', $event->required_fields ?? []))
                            <div>
                                <label class="block text-sm font-bold mb-1">Instagram (@profil)</label>
                                <input wire:model="instagram" type="text" class="w-full border-2 border-gray-400 rounded focus:border-cypher-gold focus:ring-cypher-gold">
                                @error('instagram') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div class="flex items-start gap-2 pt-2">
                             <input type="checkbox" wire:model="gdpr_consent" id="gdpr_consent" class="mt-1 border-2 border-gray-400 rounded text-cypher-gold focus:ring-cypher-gold">
                             <label for="gdpr_consent" class="text-sm text-gray-600 leading-tight">
                                 Souhlasím se zpracováním osobních údajů pro marketingové účely.
                             </label>
                        </div>
                        @error('gdpr_consent') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror


                        @error('capacity') <span class="text-red-500 text-center block font-bold">{{ $message }}</span> @enderror

                        <button type="submit" class="w-full bg-black text-white font-bold py-3 rounded hover:bg-cypher-gold hover:text-black transition-colors mt-4">
                            <span wire:loading.remove>Získat QR Kód</span>
                            <span wire:loading>Zpracovávám...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
