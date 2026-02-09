<div class="space-y-4">
    @if($claims->isEmpty())
        <p class="text-gray-500">Žádná historie.</p>
    @else
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-2">Datum</th>
                    <th class="px-4 py-2">Akce</th>
                    <th class="px-4 py-2">Stav</th>
                </tr>
            </thead>
            <tbody>
                @foreach($claims as $claim)
                    <tr class="bg-white border-b">
                        <td class="px-4 py-2">{{ $claim->created_at->format('d.m.Y') }}</td>
                        <td class="px-4 py-2 font-medium">{{ $claim->event?->title ?? 'Neznámá akce' }}</td>
                        <td class="px-4 py-2">
                            @if($claim->redeemed_at)
                                <span class="text-green-600 font-bold">Uplatněno</span>
                            @else
                                <span class="text-yellow-600">Čeká</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-xs text-gray-400 mt-2">
            Celkem: {{ $claims->count() }} záznamů pro {{ $claims->first()->email }}
        </div>
    @endif
</div>
