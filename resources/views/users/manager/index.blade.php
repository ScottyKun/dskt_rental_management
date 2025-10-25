@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-10">
    {{-- Statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-users text-3xl text-blue-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['total'] }}</h3>
                <p class="text-gray-600">Mes locataires</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-hourglass-half text-3xl text-yellow-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['pending'] }}</h3>
                <p class="text-gray-600">Validations en attente</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-user-check text-3xl text-green-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['active'] }}</h3>
                <p class="text-gray-600">Locataires actifs</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-user-xmark text-3xl text-red-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['inactive'] }}</h3>
                <p class="text-gray-600">Locataires désactivés</p>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mt-4 space-y-3 md:space-y-0">
        {{-- Formulaire de recherche --}}
        <form method="GET" action="{{ route('manager.search') }}" class="flex items-center space-x-2">
            <input type="text" name="q" placeholder="Rechercher un locataire..."
                   value="{{ request('q') }}"
                   class="px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Rechercher
            </button>
        </form>

        {{-- Bouton d’ajout --}}
        <a href="{{ route('manager.create') }}"
           class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition flex items-center">
            <i class="fa-solid fa-plus mr-1"></i> Ajouter un locataire
        </a>
    </div>

    {{--  Tableau des locataires actifs --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-700 mb-2">Liste des locataires</h2>

        <div class="overflow-x-auto bg-white rounded-xl shadow">
            <table class="min-w-full divide-y divide-gray-200" id="locatairesTable">
                <thead class="bg-gray-50 cursor-pointer">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(0)">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(1)">Prénom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(2)">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(3)">Téléphone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(4)">Adresse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($locataires as $locataire)
                    <tr>
                        <td class="px-6 py-4">{{ $locataire->name }}</td>
                        <td class="px-6 py-4">{{ $locataire->surname }}</td>
                        <td class="px-6 py-4">{{ $locataire->email }}</td>
                        <td class="px-6 py-4">{{ $locataire->phone }}</td>
                        <td class="px-6 py-4">{{ $locataire->address }}</td>
                        <td class="px-6 py-4 flex space-x-3">

                            {{-- Éditer --}}
                            <a href="{{ route('manager.edit', $locataire->id) }}" class="text-blue-500 hover:text-blue-700" title="Modifier">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            {{-- Supprimer --}}
                            <form method="POST" action="{{ route('manager.delete', $locataire->id) }}" 
                                  onsubmit="return confirm('Supprimer ce locataire ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" title="Supprimer">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                            @if(!$locataire->is_validated)
                            {{-- Valider --}}
                            <form method="POST" action="{{ route('manager.activate', $locataire->id) }}" 
                                  onsubmit="return confirm('Valider ce locataire ?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-500 hover:text-green-700" title="Valider">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                            @else
                            {{-- Désactiver --}}
                            <form method="POST" action="{{ route('manager.deactivate', $locataire->id) }}"
                                  onsubmit="return confirm('Désactiver ce locataire ?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-yellow-500 hover:text-yellow-700" title="Désactiver">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            </form>
                             @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun locataire trouvé.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 flex justify-end">
            {{ $locataires->links() }}
        </div>
    </div>

    {{--  Tableau des locataires en attente --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-700 mb-2">En attente de validation</h2>

        <div class="overflow-x-auto bg-white rounded-xl shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prénom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adresse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pending as $p)
                    <tr>
                        <td class="px-6 py-4">{{ $p->name }}</td>
                        <td class="px-6 py-4">{{ $p->surname }}</td>
                        <td class="px-6 py-4">{{ $p->email }}</td>
                        <td class="px-6 py-4">{{ $p->phone }}</td>
                        <td class="px-6 py-4">{{ $p->address }}</td>
                        <td class="px-6 py-4 flex space-x-3">
                            {{-- Valider --}}
                            <form method="POST" action="{{ route('manager.activate', $p->id) }}" 
                                  onsubmit="return confirm('Valider ce locataire ?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-500 hover:text-green-700" title="Valider">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>

                            {{-- Supprimer --}}
                            <form method="POST" action="{{ route('manager.delete', $p->id) }}" 
                                  onsubmit="return confirm('Supprimer ce locataire ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" title="Supprimer">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun locataire en attente.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Script de tri dynamique --}}
<script>
function sortTable(n) {
    const table = document.getElementById("locatairesTable");
    let rows = Array.from(table.rows).slice(1);
    let asc = table.getAttribute("data-sort-dir") === "asc" ? false : true;
    rows.sort((a, b) => {
        let x = a.cells[n].innerText.toLowerCase();
        let y = b.cells[n].innerText.toLowerCase();
        return asc ? x.localeCompare(y) : y.localeCompare(x);
    });
    rows.forEach(row => table.appendChild(row));
    table.setAttribute("data-sort-dir", asc ? "asc" : "desc");
}
</script>
@endsection
