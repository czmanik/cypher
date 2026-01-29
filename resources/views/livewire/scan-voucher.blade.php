<div
    class="p-4 max-w-md mx-auto"
    x-data="qrScanner"
>
    <h2 class="text-xl font-bold mb-4 text-center">Načíst Voucher</h2>

    {{-- Zobrazovač chyb/úspěchů --}}
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50">
            {{ session('error') }}
        </div>
    @endif

    {{-- Oblast pro kameru --}}
    <div wire:ignore>
        <div id="reader" width="600px" class="bg-black rounded-lg overflow-hidden mb-4"></div>
    </div>

    {{-- Manuální zadání (kdyby nešla kamera) --}}
    <form wire:submit="checkCode(manualCode)" class="flex gap-2">
        <input type="text" wire:model="manualCode" placeholder="Nebo zadej kód ručně..." 
               class="w-full border rounded px-3 py-2 text-black">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">OK</button>
    </form>
</div>

{{-- Načteme knihovnu --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('qrScanner', () => ({
            scanner: null,
            init() {
                // Počkáme až se načte knihovna (pokud je async)
                let checkLibrary = setInterval(() => {
                    if (typeof Html5QrcodeScanner !== 'undefined') {
                        clearInterval(checkLibrary);
                        this.startScanner();
                    }
                }, 100);
            },
            startScanner() {
                // Pokud už běží, nic nedělat
                if (this.scanner) return;

                // Kontrola existence elementu
                if (!document.getElementById('reader')) return;

                this.scanner = new Html5QrcodeScanner(
                    "reader",
                    { fps: 10, qrbox: {width: 250, height: 250} },
                    /* verbose= */ false
                );

                this.scanner.render(
                    (decodedText, decodedResult) => {
                        // Úspěch
                        this.$wire.checkCode(decodedText);
                    },
                    (error) => {
                        // Chyba čtení (běžné, když kód není vidět)
                    }
                );
            },
            destroy() {
                if (this.scanner) {
                    try {
                        this.scanner.clear();
                    } catch (e) {
                        console.error('Failed to clear scanner', e);
                    }
                    this.scanner = null;
                }
            }
        }));
    });
</script>
