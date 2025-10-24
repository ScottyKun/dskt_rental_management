@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">
    {{-- Statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-building text-3xl text-blue-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['total'] }}</h3>
                <p class="text-gray-600">Total immeubles</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-house-circle-check text-3xl text-green-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['actifs'] }}</h3>
                <p class="text-gray-600">Immeubles actifs</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-screwdriver-wrench text-3xl text-yellow-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['maintenance'] }}</h3>
                <p class="text-gray-600">En maintenance</p>
            </div>
        </div>
    </div>

    {{-- Actions : Ajouter + Recherche --}}
    <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mt-4 space-y-3 md:space-y-0">
        <form method="GET" action="{{ route('immeubles.search') }}" class="flex items-center space-x-2">
            <input type="text" name="q" placeholder="Rechercher..." 
                   value="{{ request('q') }}" 
                   class="px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Rechercher
            </button>
        </form>

        <a href="{{ route('immeubles.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition flex items-center">
            <i class="fa-solid fa-plus mr-1"></i> Ajouter
        </a>
    </div>

    {{-- Tableau des immeubles --}}
    <div class="overflow-x-auto mt-4 bg-white rounded-xl shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(this, 0)">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(this, 1)">Adresse</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(this, 2)">Ville</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(this, 3)">Gestionnaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(this, 4)">Créé le</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(this, 5)">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($immeubles as $immeuble)
                <tr>
                    <td class="px-6 py-4 font-semibold text-gray-700">{{ $immeuble->name }}</td>
                    <td class="px-6 py-4">{{ $immeuble->address }}</td>
                    <td class="px-6 py-4">{{ $immeuble->town }}</td>
                    <td class="px-6 py-4">
                        {{ $immeuble->manager ? $immeuble->manager->name . ' ' . $immeuble->manager->surname : '—' }}

                    </td>
                    <td class="px-6 py-4">{{ $immeuble->created_at->format('d/m/Y') }}</td>

                    {{-- Actions --}}
                    <td class="px-6 py-4 flex space-x-3">
                        {{-- Consulter --}}
                        <a href="{{ route('immeubles.show', $immeuble->id) }}" 
                           class="text-blue-500 hover:text-blue-700" title="Voir détails">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        {{-- Modifier  --}}
                        <a href="{{ route('immeubles.edit', $immeuble->id) }}" 
                           class="text-yellow-500 hover:text-yellow-700" title="Modifier">
                            <i class="fa-solid fa-pen"></i>
                        </a>

                        {{-- Supprimer --}}
                        @if(auth()->user()->role === 'admin')
                        <form method="POST" 
                              action="{{ route('immeubles.delete', $immeuble->id) }}" 
                              onsubmit="return confirm('Voulez-vous vraiment supprimer cet immeuble ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" title="Supprimer">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Aucun immeuble trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4 flex justify-end space-x-2">
        {{ $immeubles->links() }}
    </div>

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
