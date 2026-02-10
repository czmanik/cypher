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
