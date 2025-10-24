@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">
    {{-- Statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

        {{-- Total Appartements --}}
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-house-chimney text-3xl text-blue-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['total'] }}</h3>
                <p class="text-gray-600">Total Appartements</p>
            </div>
        </div>

        {{-- Appartements Disponibles --}}
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-door-open text-3xl text-green-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['disponibles'] }}</h3>
                <p class="text-gray-600">Disponibles</p>
            </div>
        </div>

        {{-- Appartements Occupés --}}
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-person-shelter text-3xl text-orange-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['occupes'] }}</h3>
                <p class="text-gray-600">Occupés</p>
            </div>
        </div>

        {{-- Appartements en Rénovation --}}
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-hammer text-3xl text-yellow-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['renovation'] }}</h3>
                <p class="text-gray-600">En Rénovation</p>
            </div>
        </div>

    </div>

    {{--  Actions principales --}}
    <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mt-4 space-y-3 md:space-y-0">
        <form method="GET" action="{{ route('appartements.search') }}" class="flex items-center space-x-2">
            <input type="text" name="q" placeholder="Rechercher..." 
                   value="{{ request('q') }}" 
                   class="px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Rechercher
            </button>
        </form>

        <a href="{{ route('appartements.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition flex items-center">
            <i class="fa-solid fa-plus mr-1"></i> Ajouter
        </a>
    </div>

    {{--  Tableau des appartements par immeuble --}}
    @foreach($immeubles as $nomImmeuble => $appartements)
    <div class="overflow-x-auto mt-8 bg-white rounded-xl shadow">
        {{-- En-tête de l’immeuble --}}
        <div class="bg-gray-100 p-4 rounded-t-xl flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center space-x-2">
                <i class="fa-solid fa-building text-blue-500"></i>
                <span>{{ $nomImmeuble }}</span>
            </h2>
            
        </div>

        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 cursor-pointer">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(this, 0)">Numéro</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(this, 1)">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(this, 2)">Surface (m²)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(this, 3)">Loyer (CFA)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(this, 4)">Locataire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(this, 5)">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($appartements as $appartement)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $appartement->name }}</td>
                    <td class="px-6 py-4">{{ $appartement->type }}</td>
                    <td class="px-6 py-4">{{ $appartement->area }}</td>
                    <td class="px-6 py-4">{{ number_format($appartement->rent ?? 0, 2, ',', ' ') }}</td>
                    <td class="px-6 py-4">
                        {{ $appartement->locataire ? $appartement->locataire->name . ' ' . $appartement->locataire->surname : '— Non attribué —' }}
                    </td>
                    <td class="px-6 py-4">{{ $appartement->status }}</td>
                    <td class="px-6 py-4 flex space-x-3">
                        <a href="{{ route('appartements.edit', $appartement->id) }}" class="text-blue-500 hover:text-blue-700" title="Modifier">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>

                        <form method="POST" action="{{ route('appartements.destroy', $appartement->id) }}" onsubmit="return confirm('Supprimer cet appartement ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" title="Supprimer">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>

                        <a href="{{ route('appartements.consult', $appartement->id) }}" class="text-gray-600 hover:text-gray-800" title="Détails">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun appartement enregistré pour cet immeuble.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endforeach

</div>

{{--  Tri simple côté front --}}
<script>
function sortTable(th, n) {
    const table = th.closest('table');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const asc = th.getAttribute('data-sort-dir') !== 'asc';
    rows.sort((a, b) => {
        const x = a.cells[n].innerText.toLowerCase();
        const y = b.cells[n].innerText.toLowerCase();
        return asc ? x.localeCompare(y) : y.localeCompare(x);
    });
    rows.forEach(row => table.querySelector('tbody').appendChild(row));
    th.setAttribute('data-sort-dir', asc ? 'asc' : 'desc');
}
</script>
@endsection
