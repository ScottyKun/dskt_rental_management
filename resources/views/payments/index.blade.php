@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-10">

    {{-- ================= PAIEMENTS ================= --}}
    <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mt-4 space-y-3 md:space-y-0">
        {{-- Recherche --}}
        <form method="GET" action="{{ route('payments.search') }}" class="flex items-center space-x-2">
            <input type="text" name="q" placeholder="Rechercher..."
                   value="{{ request('q') }}"
                   class="px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Rechercher
            </button>
        </form>

        {{-- Bouton Créer (interdit aux locataires) --}}
        @if(auth()->user()->role !== 'locataire')
            <div class="flex justify-between items-center mb-4">
                <a href="{{ route('payments.create') }}"
                class="bg-green-500 text-white px-4 py-2 rounded-lg">
                    <i class="fa-solid fa-plus mr-1"></i> Nouveau paiement
                </a>
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded-xl shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 cursor-pointer" onclick="sortTable(this,0)">Date</th>
                    <th class="px-6 py-3 cursor-pointer" onclick="sortTable(this,1)">Locataire</th>
                    <th class="px-6 py-3 cursor-pointer" onclick="sortTable(this,2)">Gestionnaire</th>
                    <th class="px-6 py-3">Méthode</th>
                    <th class="px-6 py-3">Montant</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                @foreach($payments as $payment)
                    <tr>
                        <td class="px-6 py-4">{{ $payment->paid_at?->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">{{ $payment->tenant->name }} {{ $payment->tenant->surname }}</td>
                        <td class="px-6 py-4">{{ $payment->manager->name }} {{ $payment->manager->surname }}</td>
                        <td class="px-6 py-4">{{ $payment->paymentMethod->label }}</td>
                        <td class="px-6 py-4 font-semibold">{{ $payment->amount }} €</td>
                        <td class="px-6 py-4 flex space-x-3">
                            <a href="{{ route('payments.show', $payment->id) }}" class="text-blue-500"><i class="fa-solid fa-eye"></i></a>
                            @if(auth()->user()->role !== 'locataire')
                                <a href="{{ route('payments.edit', $payment->id) }}" class="text-yellow-500"><i class="fa-solid fa-pen"></i></a>
                                <a href="{{ route('payments.destroy', $payment->id) }}" class="text-red-500" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')"><i class="fa-solid fa-trash"></i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $payments->links() }}</div>
    </div>

    <hr class="border-gray-300">

    {{-- ================= RECUS ================= --}}
    <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mt-4 space-y-3 md:space-y-0">
        <h2 class="text-xl font-bold text-gray-700 mb-4">Reçus</h2>

        {{-- Recherche --}}
        <form method="GET" action="{{ route('receipts.search') }}" class="flex items-center space-x-2">
            <input type="text" name="q" placeholder="Rechercher..."
                   value="{{ request('q') }}"
                   class="px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Rechercher
            </button>
        </form>

        <div class="overflow-x-auto bg-white rounded-xl shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 cursor-pointer" onclick="sortTable(this,0)">Numéro</th>
                    <th class="px-6 py-3 cursor-pointer" onclick="sortTable(this,1)">Locataire</th>
                    <th class="px-6 py-3">Période</th>
                    <th class="px-6 py-3">Montant</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                @foreach($receipts as $receipt)
                    <tr>
                        <td class="px-6 py-4">{{ $receipt->receipt_number }}</td>
                        <td class="px-6 py-4">{{ $receipt->tenant->name }} {{ $receipt->tenant->surname }}</td>
                        <td class="px-6 py-4">
                            {{ $receipt->periods->first()->start_date }}
                            →
                            {{ $receipt->periods->first()->end_date }}
                        </td>
                        <td class="px-6 py-4 font-semibold">{{ $receipt->total_amount }} €</td>
                        <td class="px-6 py-4 flex space-x-3">
                            <a href="{{ route('receipts.show', $receipt->id) }}" class="text-blue-500"><i class="fa-solid fa-eye"></i></a>
                            @if(auth()->user()->role !== 'locataire')
                                <a href="{{ route('receipts.edit', $receipt->id) }}" class="text-yellow-500"><i class="fa-solid fa-pen"></i></a>
                                <a href="{{ route('receipts.destroy', $receipt->id) }}" class="text-red-500" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce reçu ?')"><i class="fa-solid fa-trash"></i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $receipts->links() }}</div>
    </div>

</div>

<script>
function sortTable(th, n) {
    const table = th.closest('table');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const asc = th.dataset.dir !== 'asc';
    rows.sort((a,b)=>{
        return asc
            ? a.cells[n].innerText.localeCompare(b.cells[n].innerText)
            : b.cells[n].innerText.localeCompare(a.cells[n].innerText);
    });
    rows.forEach(r=>table.querySelector('tbody').appendChild(r));
    th.dataset.dir = asc ? 'asc' : 'desc';
}
</script>
@endsection