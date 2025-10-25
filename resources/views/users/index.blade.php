@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">

    {{-- Statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-users text-3xl text-blue-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['total_users'] }}</h3>
                <p class="text-gray-600">Total Users</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-user text-3xl text-green-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['locataires'] }}</h3>
                <p class="text-gray-600">Locataires</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-hourglass-half text-3xl text-yellow-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['pending_validations'] }}</h3>
                <p class="text-gray-600">Validations en attente</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition flex items-center space-x-4">
            <i class="fa-solid fa-user-tie text-3xl text-purple-500"></i>
            <div>
                <h3 class="text-xl font-semibold">{{ $stats['gestionnaires'] }}</h3>
                <p class="text-gray-600">Gestionnaires</p>
            </div>
        </div>
    </div>

    {{-- Actions : Recherche + Ajouter --}}
    <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mt-4 space-y-3 md:space-y-0">
        <form method="GET" action="{{ route('users.index') }}" class="flex items-center space-x-2">
            <input type="text" name="search" placeholder="Rechercher..." 
                   value="{{ request('search') }}" 
                   class="px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Rechercher
            </button>
        </form>

        <a href="{{ route('users.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition flex items-center">
            <i class="fa-solid fa-plus mr-1"></i> Ajouter
        </a>
    </div>

    {{-- Tableau utilisateurs --}}
    <div class="overflow-x-auto mt-4 bg-white rounded-xl shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 cursor-pointer">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(0)">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(1)">Prénom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(2)">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(3)">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(4)">Telephone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(5)">Adresse</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" onclick="sortTable(6)">Gestionnaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->surname }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">{{ ucfirst($user->role) }}</td>
                    <td class="px-6 py-4">{{ $user->phone }}</td>
                    <td class="px-6 py-4">{{ $user->address }}</td>
                    <td class="px-6 py-4">
                        {{ $user->manager ? $user->manager->name . ' ' . $user->manager->surname : 'Non assigné' }}
                    </td>

                    <td class="px-6 py-4 flex space-x-2">
                        {{-- Actions : Editer, Supprimer, Valider, Désactiver --}}
                        <a href="{{ route('users.edit', $user->id) }}" class="text-blue-500 hover:text-blue-700" title="Éditer">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>

                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" title="Supprimer">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>

                        @if(!$user->is_validated)
                        <form method="POST" action="{{ route('users.validate', $user->id) }}" onsubmit="return confirm('Valider cet utilisateur ?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-green-500 hover:text-green-700" title="Valider">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('users.deactivate', $user->id) }}" onsubmit="return confirm('Désactiver cet utilisateur ?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-yellow-500 hover:text-yellow-700" title="Désactiver">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4 flex justify-end space-x-2">
        {{ $users->links() }}
    </div>

</div>

{{-- Tri simple côté front --}}
<script>
function sortTable(n) {
    const table = document.querySelector("table");
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
