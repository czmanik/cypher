<x-layouts.app title="Rezervace | Cypher93">

    <div class="min-h-screen bg-cypher-dark flex items-center justify-center py-20 px-4 relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
             <div class="absolute -top-[20%] -right-[10%] w-[50%] h-[50%] bg-cypher-gold/10 rounded-full blur-[100px]"></div>
             <div class="absolute top-[40%] -left-[10%] w-[40%] h-[40%] bg-purple-900/20 rounded-full blur-[100px]"></div>
        </div>

        <div class="bg-white/5 backdrop-blur-lg border border-white/10 p-8 md:p-12 rounded-2xl max-w-2xl w-full relative z-10 shadow-2xl">
            
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-white uppercase tracking-wider mb-2">Rezervace Stolu</h1>
                <p class="text-gray-400">Zarezervujte si své místo včas.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-500/10 border border-red-500 text-red-200 p-4 rounded text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('reservations.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-cypher-gold text-xs font-bold uppercase tracking-widest mb-2">Jméno</label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                               class="w-full bg-black/30 border border-white/10 text-white px-4 py-3 rounded focus:outline-none focus:border-cypher-gold transition-colors"
                               placeholder="Jan Novák">
                    </div>

                    <div>
                        <label class="block text-cypher-gold text-xs font-bold uppercase tracking-widest mb-2">Telefon</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required 
                               class="w-full bg-black/30 border border-white/10 text-white px-4 py-3 rounded focus:outline-none focus:border-cypher-gold transition-colors"
                               placeholder="+420 777 ...">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-cypher-gold text-xs font-bold uppercase tracking-widest mb-2">Datum</label>
                        <input type="date" name="date" value="{{ old('date') }}" required min="{{ date('Y-m-d') }}"
                               class="w-full bg-black/30 border border-white/10 text-white px-4 py-3 rounded focus:outline-none focus:border-cypher-gold transition-colors scheme-dark">
                    </div>

                    <div>
                        <label class="block text-cypher-gold text-xs font-bold uppercase tracking-widest mb-2">Čas</label>
                        <input type="time" name="time" value="{{ old('time') }}" required 
                               class="w-full bg-black/30 border border-white/10 text-white px-4 py-3 rounded focus:outline-none focus:border-cypher-gold transition-colors scheme-dark">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-cypher-gold text-xs font-bold uppercase tracking-widest mb-2">Počet osob</label>
                        <select name="guests" required class="w-full bg-black/30 border border-white/10 text-white px-4 py-3 rounded focus:outline-none focus:border-cypher-gold transition-colors">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" class="text-black">{{ $i }} {{ $i == 1 ? 'osoba' : ($i < 5 ? 'osoby' : 'osob') }}</option>
                            @endfor
                            <option value="11" class="text-black">Více (napište do poznámky)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-500 text-xs font-bold uppercase tracking-widest mb-2">E-mail (volitelný)</label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="w-full bg-black/30 border border-white/10 text-white px-4 py-3 rounded focus:outline-none focus:border-cypher-gold transition-colors"
                               placeholder="email@example.com">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-500 text-xs font-bold uppercase tracking-widest mb-2">Poznámka</label>
                    <textarea name="note" rows="3" 
                              class="w-full bg-black/30 border border-white/10 text-white px-4 py-3 rounded focus:outline-none focus:border-cypher-gold transition-colors"
                              placeholder="Speciální přání, alergie, oslava..."></textarea>
                </div>

                <button type="submit" class="w-full bg-cypher-gold text-black font-bold uppercase tracking-widest py-4 rounded hover:bg-white transition-all transform hover:-translate-y-1 shadow-lg shadow-cypher-gold/20">
                    Odeslat Rezervaci
                </button>

            </form>
        </div>
    </div>

</x-layouts.app>