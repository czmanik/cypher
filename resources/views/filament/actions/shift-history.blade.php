<div class="space-y-4">
    @forelse($logs as $log)
        <div class="flex items-start gap-3 text-sm border-b border-gray-100 pb-3 last:border-0">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">
                    {{ substr($log->user->name ?? '?', 0, 2) }}
                </div>
            </div>
            <div class="flex-1">
                <div class="flex justify-between items-center">
                    <p class="font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</p>
                    <span class="text-xs text-gray-500">{{ $log->created_at->format('d.m. H:i') }}</span>
                </div>
                <p class="text-gray-600">
                    @switch($log->action)
                        @case('created') Vytvořil směnu @break
                        @case('claimed') Přihlásil se o směnu @break
                        @case('approved') Schválil přihlášku @break
                        @case('rejected') Zamítl přihlášku @break
                        @case('bonus_added') Přidal bonus @break
                        @default {{ $log->action }}
                    @endswitch
                </p>
                @if($log->payload)
                    <pre class="text-xs text-gray-400 mt-1">{{ json_encode($log->payload) }}</pre>
                @endif
            </div>
        </div>
    @empty
        <p class="text-center text-gray-500">Žádná historie.</p>
    @endforelse
</div>
