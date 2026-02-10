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
            {{-- Ovládání kamery --}}
            <div class="text-center">
                <div x-show="!cameraActive" class="py-8 bg-gray-100 rounded-xl border-2 border-dashed border-gray-300">
                    <button @click="startScanner" class="bg-black text-white px-6 py-3 rounded-lg font-bold hover:bg-gray-800 transition shadow-lg inline-flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Zapnout kameru
                    </button>
                    <p class="text-xs text-gray-400 mt-2" x-text="libraryStatus"></p>
                </div>
            </div>

            {{-- Oblast pro kameru (Hidden by default until activated) --}}
            <div x-show="cameraActive" x-cloak class="relative rounded-xl overflow-hidden shadow-inner bg-black" style="min-height: 300px; background-color: #000;">
                <div id="reader" style="width: 100%; height: 100%;"></div>

                {{-- Overlay --}}
                <div class="absolute inset-0 pointer-events-none border-[40px] border-black/50 z-10"></div>
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                     <div class="w-64 h-64 border-2 border-cypher-gold/80 rounded-lg relative">
                         <div class="absolute top-0 left-0 w-4 h-4 border-t-4 border-l-4 border-cypher-gold"></div>
                         <div class="absolute top-0 right-0 w-4 h-4 border-t-4 border-r-4 border-cypher-gold"></div>
                         <div class="absolute bottom-0 left-0 w-4 h-4 border-b-4 border-l-4 border-cypher-gold"></div>
                         <div class="absolute bottom-0 right-0 w-4 h-4 border-b-4 border-r-4 border-cypher-gold"></div>
                     </div>
                </div>

                {{-- Stop Button --}}
                <button @click="stopScanner" class="absolute top-4 right-4 z-30 bg-white/20 hover:bg-white/40 text-white rounded-full p-2 backdrop-blur-sm transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Manuální zadání --}}
            <form wire:submit.prevent="submitManual" class="bg-gray-50 p-4 rounded-xl border border-gray-200 mt-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nebo zadejte kód ručně</label>
                <div class="flex gap-2">
                    <input type="text" wire:model="manualCode" placeholder="Např. A1B2"
                           class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-3 text-lg font-mono uppercase focus:border-cypher-gold focus:ring-cypher-gold bg-white text-black">
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
            cameraActive: false,
            libraryStatus: 'Načítám knihovnu...',
            init() {
                // Check library loading
                let checkLibrary = setInterval(() => {
                    if (typeof Html5Qrcode !== 'undefined') {
                        clearInterval(checkLibrary);
                        this.libraryStatus = 'Knihovna připravena.';
                    } else {
                        this.libraryStatus = 'Chyba: Knihovna skeneru se nenačetla.';
                    }
                }, 500);

                // Stop scanner on destroy
                this.$watch('$wire.scannedType', (value) => {
                    if (value) {
                        this.stopScanner();
                    }
                });
            },
            startScanner() {
                if (typeof Html5Qrcode === 'undefined') {
                    alert('Knihovna pro skenování se nepodařilo načíst. Zkontrolujte připojení k internetu.');
                    return;
                }

                this.cameraActive = true;

                // Wait for DOM update
                this.$nextTick(() => {
                    if (this.scanner) return; // Already running?

                    const html5QrCode = new Html5Qrcode("reader");
                    this.scanner = html5QrCode;

                    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                    html5QrCode.start({ facingMode: "environment" }, config,
                        (decodedText, decodedResult) => {
                            console.log("QR Code scanned:", decodedText);
                            this.$wire.checkCode(decodedText);
                            this.stopScanner();
                        },
                        (errorMessage) => {
                            // Ignorujeme chyby čtení při běhu
                        }
                    ).catch(err => {
                        console.error("Error starting scanner", err);
                        alert("Nepodařilo se spustit kameru: " + err);
                        this.cameraActive = false;
                    });
                });
            },
            stopScanner() {
                if (this.scanner) {
                    this.scanner.stop().then(() => {
                        this.scanner.clear();
                        this.scanner = null;
                        this.cameraActive = false;
                    }).catch((err) => {
                        console.error("Failed to stop scanner", err);
                        this.cameraActive = false;
                    });
                } else {
                    this.cameraActive = false;
                }
            },
            destroy() {
                this.stopScanner();
            }
        }));
    });
</script>
