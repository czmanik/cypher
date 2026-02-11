<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail směny - {{ $record->user->name }} - {{ $record->start_at->format('d.m.Y') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-white text-gray-900 font-sans p-8">

    <div class="max-w-4xl mx-auto border p-8 shadow-sm print:shadow-none print:border-none">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-8 border-b pb-4">
            <div>
                <h1 class="text-2xl font-bold uppercase tracking-wider">Detail směny</h1>
                <p class="text-gray-500">ID: #{{ $record->id }}</p>
            </div>
            <div class="text-right">
                <div class="text-xl font-bold">{{ $record->user->name }}</div>
                <div class="text-gray-600">{{ $record->start_at->format('d.m.Y') }}</div>
            </div>
        </div>

        {{-- Main Info --}}
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="text-sm font-bold uppercase text-gray-500 mb-2">Časový přehled</h3>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-1 text-gray-600">Začátek:</td>
                        <td class="py-1 font-medium text-right">{{ $record->start_at->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 text-gray-600">Konec:</td>
                        <td class="py-1 font-medium text-right">{{ $record->end_at ? $record->end_at->format('H:i') : '---' }}</td>
                    </tr>
                    <tr class="border-t">
                        <td class="py-1 font-bold">Celkem hodin:</td>
                        <td class="py-1 font-bold text-right">{{ number_format($record->total_hours, 2, ',', ' ') }} h</td>
                    </tr>
                </table>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase text-gray-500 mb-2">Stav směny</h3>
                <div class="inline-block px-3 py-1 rounded text-sm font-bold
                    {{ match($record->status) {
                        'active' => 'bg-blue-100 text-blue-800',
                        'pending_approval' => 'bg-yellow-100 text-yellow-800',
                        'approved' => 'bg-indigo-100 text-indigo-800',
                        'paid' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800'
                    } }}">
                    {{ match($record->status) {
                        'active' => 'Běží',
                        'pending_approval' => 'Ke kontrole',
                        'approved' => 'Schváleno (K úhradě)',
                        'paid' => 'Proplaceno',
                        'rejected' => 'Zamítnuto',
                        default => $record->status
                    } }}
                </div>
                @if($record->status === 'paid')
                    <p class="text-xs text-gray-500 mt-2">
                        Způsob úhrady: {{ $record->payment_method === 'cash' ? 'Hotově' : 'Na účet' }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Financials --}}
        <div class="mb-8">
            <h3 class="text-sm font-bold uppercase text-gray-500 mb-4 border-b pb-2">Vyúčtování mzdy</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="pb-2">Položka</th>
                        <th class="pb-2">Poznámka</th>
                        <th class="pb-2 text-right">Částka</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="py-2">Základní mzda</td>
                        <td class="py-2 text-gray-500 italic">Sazba x Hodiny</td>
                        <td class="py-2 text-right font-medium">{{ number_format($record->calculated_wage, 2, ',', ' ') }} Kč</td>
                    </tr>
                    @if($record->bonus != 0)
                    <tr>
                        <td class="py-2 text-green-600">Bonus (+)</td>
                        <td class="py-2">{{ $record->bonus_note ?? '-' }}</td>
                        <td class="py-2 text-right font-medium text-green-600">+{{ number_format($record->bonus, 2, ',', ' ') }} Kč</td>
                    </tr>
                    @endif
                    @if($record->penalty != 0)
                    <tr>
                        <td class="py-2 text-red-600">Malus / Pokuta (-)</td>
                        <td class="py-2">{{ $record->penalty_note ?? '-' }}</td>
                        <td class="py-2 text-right font-medium text-red-600">-{{ number_format($record->penalty, 2, ',', ' ') }} Kč</td>
                    </tr>
                    @endif
                    @if($record->advance_amount != 0)
                    <tr>
                        <td class="py-2 text-blue-600">Záloha (vyplaceno) (-)</td>
                        <td class="py-2"></td>
                        <td class="py-2 text-right font-medium text-blue-600">-{{ number_format($record->advance_amount, 2, ',', ' ') }} Kč</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-300">
                        <td class="py-4 font-bold text-lg">Celkem k výplatě</td>
                        <td class="py-4"></td>
                        <td class="py-4 font-bold text-lg text-right">{{ number_format($record->final_payout, 2, ',', ' ') }} Kč</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Notes --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-gray-50 p-4 rounded">
                <h4 class="font-bold text-sm mb-2">Poznámka zaměstnance:</h4>
                <p class="text-sm italic text-gray-700">{{ $record->general_note ?? '---' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <h4 class="font-bold text-sm mb-2">Interní poznámka manažera:</h4>
                <p class="text-sm italic text-gray-700">{{ $record->manager_note ?? '---' }}</p>
            </div>
        </div>

        {{-- Print Button (No Print) --}}
        <div class="no-print mt-8 text-center">
            <button onclick="window.print()" class="bg-black text-white px-6 py-2 rounded hover:bg-gray-800">
                Tisk (Ctrl+P)
            </button>
            <button onclick="window.close()" class="ml-4 text-gray-600 underline">
                Zavřít
            </button>
        </div>

    </div>

</body>
</html>
