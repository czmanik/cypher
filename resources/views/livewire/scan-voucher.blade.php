<div class="p-4 max-w-lg mx-auto" x-data="qrScanner">

    {{-- HLAVIČKA --}}
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold">Načíst Voucher</h2>
        <p class="text-gray-500 text-sm">Naskenujte QR kód nebo zadejte 4-místný kód</p>
    </div>

    {{-- ALERT MESSAGES --}}
    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    @error('manualCode')
        <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ $message }}
        </div>
    @enderror

    {{-- 1. MOD: POTVRZENÍ (Zobrazeno po nalezení kódu) --}}
    @if($scannedType)
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-cypher-gold p-4 text-center">
                <h3 class="font-bold text-black text-xl">{{ $scannedData['title'] }}</h3>
                <p class="text-black/80 font-mono text-lg mt-1">{{ $scannedData['code'] }}</p>
            </div>

            <div class="p-6 space-y-4">
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-800">{{ $scannedData['subtitle'] }}</p>
                    <p class="text-gray-500 mt-2">{{ $scannedData['info'] }}</p>
                </div>

                @if($scannedType === 'claim')
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Poznámka personálu</label>
                        <textarea wire:model="staffNote" rows="2" class="w-full border-gray-300 rounded focus:border-cypher-gold focus:ring-cypher-gold" placeholder="Např. VIP stůl, Alergie..."></textarea>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button wire:click="cancelRedemption" class="px-4 py-3 bg-gray-200 text-gray-800 font-bold rounded hover:bg-gray-300 transition">
                        Zrušit
                    </button>
                    <button wire:click="confirmRedemption" class="px-4 py-3 bg-green-600 text-white font-bold rounded hover:bg-green-700 shadow transition flex justify-center items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        UPLATNIT
                    </button>
                </div>
            </div>
        </div>

    {{-- 2. MOD: SKENER A VSTUP (Zobrazeno defaultně) --}}
    @else
        <div class="space-y-6">
            {{-- Kamera --}}
            <div wire:ignore class="relative bg-black rounded-xl overflow-hidden shadow-inner min-h-[300px]">
                <div id="reader" class="w-full h-full"></div>
                <div class="absolute inset-0 pointer-events-none border-[40px] border-black/50 z-10"></div>
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                     <div class="w-64 h-64 border-2 border-cypher-gold/80 rounded-lg relative">
                         <div class="absolute top-0 left-0 w-4 h-4 border-t-4 border-l-4 border-cypher-gold"></div>
                         <div class="absolute top-0 right-0 w-4 h-4 border-t-4 border-r-4 border-cypher-gold"></div>
                         <div class="absolute bottom-0 left-0 w-4 h-4 border-b-4 border-l-4 border-cypher-gold"></div>
                         <div class="absolute bottom-0 right-0 w-4 h-4 border-b-4 border-r-4 border-cypher-gold"></div>
                     </div>
                </div>
                <p class="absolute bottom-4 left-0 right-0 text-center text-white/80 z-20 text-sm">Namiřte kameru na QR kód</p>
            </div>

            {{-- Manuální zadání --}}
            <form wire:submit.prevent="checkCode(manualCode)" class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nebo zadejte kód ručně</label>
                <div class="flex gap-2">
                    <input type="text" wire:model.live="manualCode" placeholder="Např. A1B2"
                           class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-3 text-lg font-mono uppercase focus:border-cypher-gold focus:ring-cypher-gold">
                    <button type="submit" class="bg-black text-white px-6 py-3 rounded-lg font-bold hover:bg-gray-800 transition shadow-lg">
                        OK
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>

{{-- Skript pro skener --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('qrScanner', () => ({
            scanner: null,
            init() {
                // Sledujeme změny ve Livewire, pokud se změní stav (zobrazení/skrytí), restartujeme skener
                this.$watch('$wire.scannedType', (value) => {
                    if (!value) {
                         // Pokud jsme se vrátili do režimu skenování, počkáme na DOM a spustíme
                         setTimeout(() => this.startScanner(), 100);
                    } else {
                        // Pokud potvrzujeme, vypneme kameru
                        this.stopScanner();
                    }
                });

                this.startScanner();
            },
            startScanner() {
                if (this.scanner) return; // Už běží
                if (!document.getElementById('reader')) return; // Není element

                // Inicializace Html5Qrcode (ne Scanner UI, ale čisté API pro vlastní design)
                const html5QrCode = new Html5Qrcode("reader");
                this.scanner = html5QrCode;

                const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                // Zkusíme zadní kameru
                html5QrCode.start({ facingMode: "environment" }, config,
                    (decodedText, decodedResult) => {
                        // Úspěšné načtení
                        console.log("QR Code scanned:", decodedText);
                        this.$wire.checkCode(decodedText);
                        // Pauza po úspěchu, aby neskenoval 100x za vteřinu
                        this.stopScanner();
                    },
                    (errorMessage) => {
                        // Chyba čtení (ignorujeme, děje se každým snímkem, když tam není kód)
                    }
                ).catch(err => {
                    console.error("Error starting scanner", err);
                    // Můžeme zobrazit chybu uživateli, pokud chceme
                });
            },
            stopScanner() {
                if (this.scanner) {
                    this.scanner.stop().then((ignore) => {
                        // QR Code scanning is stopped.
                        this.scanner.clear();
                        this.scanner = null;
                    }).catch((err) => {
                        // Stop failed, handle it.
                        console.error("Failed to stop scanner", err);
                    });
                }
            },
            destroy() {
                this.stopScanner();
            }
        }));
    });
</script>
