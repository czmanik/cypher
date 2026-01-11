<div class="p-4 max-w-md mx-auto">
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
    <div id="reader" width="600px" class="bg-black rounded-lg overflow-hidden mb-4"></div>

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
    document.addEventListener('livewire:initialized', () => {

        // Funkce co se zavolá po úspěšném skenu
        function onScanSuccess(decodedText, decodedResult) {
            // Pošleme kód do PHP metody checkCode()
            @this.call('checkCode', decodedText);

            // Volitelné: Pauznout skener po úspěchu, aby to nečte 10x za sebou
            // html5QrcodeScanner.clear();
        }

        function onScanFailure(error) {
            // Čtečka skenuje furt, tohle hází chyby když nevidí kód, většinou ignorovat
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false);

        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    });
</script>