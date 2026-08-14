@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">

    {{-- Actions principales --}}
    <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mt-4 space-y-3 md:space-y-0">

        {{-- Recherche --}}
        <form method="GET" action="{{ route('payments.search') }}" class="flex items-center space-x-2">
            <input type="text" name="q" placeholder="Rechercher..."
                   value="{{ request('q') }}"
                   class="px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">

            <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Rechercher
            </button>
        </form>

        {{-- Bouton créer --}}
        @if(auth()->user()->role !== 'locataire')
            <a href="{{ route('payments.create') }}"
               class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition flex items-center">
                <i class="fa-solid fa-plus mr-1"></i> Nouveau paiement
            </a>
        @endif
    </div>

    {{-- Tableau --}}
    <div class="overflow-x-auto mt-8 bg-white rounded-xl shadow">
        <table id="paymentsTable" class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 cursor-pointer">Date</th>
                    <th class="px-6 py-3 cursor-pointer">Locataire</th>
                    <th class="px-6 py-3 cursor-pointer">Gestionnaire</th>
                    <th class="px-6 py-3">Motif</th>
                    <th class="px-6 py-3">Méthode</th>
                    <th class="px-6 py-3">Montant</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
            @forelse($payments as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        {{ $payment->paid_at?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $payment->tenant->name }} {{ $payment->tenant->surname }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $payment->manager->name }} {{ $payment->manager->surname }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $motifLabels = ['loyer' => 'Loyer', 'caution' => 'Caution', 'reparation' => 'Réparation', 'autre' => 'Autre'];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            {{ $motifLabels[$payment->motif] ?? '—' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        {{ $payment->paymentMethod->label }}
                    </td>
                    <td class="px-6 py-4 font-semibold">
                        {{ number_format($payment->amount, 2, ',', ' ') }} CFA
                    </td>

                    <td class="px-6 py-4 flex space-x-2">
                        <a href="{{ route('payments.show', $payment->id) }}"
                           class="text-gray-600 hover:text-gray-800" title="Voir">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        @if(auth()->user()->role !== 'locataire')
                            <a href="{{ route('payments.edit', $payment->id) }}"
                               class="text-blue-500 hover:text-blue-700" title="Modifier">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            @if(!$payment->receipts)
                                <a href="{{ route('receipts.periods', $payment->id) }}"
                                   class="text-blue-500 hover:text-blue-700" title="Générer un reçu">
                                    <i class="fa-solid fa-receipt"></i>
                                </a>
                            @else
                                <a href="{{ route('receipts.show', $payment->receipts->id) }}"
                                   class="text-green-600 hover:text-green-800" title="Voir le reçu existant">
                                    <i class="fa-solid fa-circle-check"></i>
                                </a>
                            @endif

                            <form action="{{ route('payments.destroy', $payment->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Supprimer ce paiement ?');">
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
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Aucun paiement trouvé.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $payments->links() }}
    </div>
</div>
@endsection