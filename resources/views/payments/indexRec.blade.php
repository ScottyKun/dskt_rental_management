@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">

    {{-- Actions principales --}}
    <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mt-4 space-y-3 md:space-y-0">

        {{-- Recherche --}}
        <form method="GET" action="{{ route('receipts.search') }}" class="flex items-center space-x-2">
            <input type="text" name="q" placeholder="Rechercher..."
                   value="{{ request('q') }}"
                   class="px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">

            <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Rechercher
            </button>
        </form>
    </div>

    {{-- Tableau --}}
    <div class="overflow-x-auto mt-8 bg-white rounded-xl shadow">
        <table id="receiptsTable" class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3">Numéro</th>
                    <th class="px-6 py-3">Locataire</th>
                    <th class="px-6 py-3">Période</th>
                    <th class="px-6 py-3">Montant</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
            @forelse($receipts as $receipt)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">
                        {{ $receipt->receipt_number }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $receipt->tenant->name }} {{ $receipt->tenant->surname }}
                    </td>
                    <td class="px-6 py-4">
                        @foreach ($receipt->periods as $period)
                            {{ $period->period_start->format('d/m/Y') }}
                            →
                            {{ $period->period_end->format('d/m/Y') }}
                    </p>
                @endforeach
                    </td>
                    <td class="px-6 py-4 font-semibold">
                        {{ number_format($receipt->total_amount, 2, ',', ' ') }} CFA
                    </td>

                    <td class="px-6 py-4 flex space-x-2">
                        <a href="{{ route('receipts.show', $receipt->id) }}"
                           class="text-gray-600 hover:text-gray-800" title="Voir">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        @if(auth()->user()->role !== 'locataire')
                            <a href="{{ route('receipts.edit', $receipt->id) }}"
                               class="text-blue-500 hover:text-blue-700" title="Modifier">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <form action="{{ route('receipts.destroy', $receipt->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Supprimer ce reçu ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-500 hover:text-red-700" title="Supprimer">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        Aucun reçu trouvé.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $receipts->links() }}
    </div>
</div>
@endsection