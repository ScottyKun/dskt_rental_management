@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">

    {{-- Actions principales --}}
    <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mt-4 space-y-3 md:space-y-0">
        
        {{-- Recherche --}}
        <form method="GET" action="{{ route('contrats.search') }}" class="flex items-center space-x-2">
            <input type="text" name="q" placeholder="Rechercher..."
                   value="{{ request('q') }}"
                   class="px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Rechercher
            </button>
        </form>

        {{-- Bouton Créer (interdit aux locataires) --}}
        @if(auth()->user()->role !== 'locataire')
        <a href="{{ route('contrats.create') }}" 
           class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition flex items-center">
            <i class="fa-solid fa-plus mr-1"></i> Ajouter un Contrat
        </a>
        @else
        {{-- Locataire : demande au gestionnaire/admin --}}
        <a href="{{ route('messages.request.create') }}"
            class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition flex items-center"
            title="Demande de modification/renouvellement">
            <i class="fa-solid fa-envelope mr-1"></i> Demande
        </a>
        @endif
    </div>

    {{-- Tableau des contrats --}}
    <div class="overflow-x-auto mt-8 bg-white rounded-xl shadow">
        <table id="contratsTable" class="min-w-full divide-y divide-gray-200 text-sm cursor-pointer">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left" data-column="tenant">Locataire</th>
                    <th class="px-6 py-3 text-left" data-column="appartement">Appartement</th>
                    <th class="px-6 py-3 text-left" data-column="rent_amount">Loyer</th>
                    <th class="px-6 py-3 text-left" data-column="start_date">Début</th>
                    <th class="px-6 py-3 text-left" data-column="end_date">Fin</th>
                    <th class="px-6 py-3 text-left" data-column="status">Status</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">

                @forelse($contrats as $contrat)
                @php
                    $today = now()->startOfDay();
                    $end = \Carbon\Carbon::parse($contrat->end_date)->startOfDay();
                    $renewable = $end < $today;

                    // Alerte expiration
                    $daysDiff =$today->diffInDays($end, false);
                @endphp

                <tr class="hover:bg-gray-50">

                    <td class="px-6 py-4">{{ $contrat->tenant->name }} {{ $contrat->tenant->surname }}</td>
                    <td class="px-6 py-4">{{ $contrat->appartement->name }}</td>
                    <td class="px-6 py-4">{{ number_format($contrat->rent_amount, 2, ',', ' ') }} CFA</td>
                    <td class="px-6 py-4">{{ $contrat->start_date }}</td>

                    {{-- Indicateur expiration visuel --}}
                    <td class="px-6 py-4">
                        @if($daysDiff < 0)
                            {{-- Contrat expiré --}}
                            <span class="text-red-600 font-semibold">{{ $contrat->end_date }}</span>
                        @elseif($daysDiff == 0)
                            {{-- Expiration aujourd'hui --}}
                            <span class="text-orange-500 font-semibold">{{ $contrat->end_date }}</span>
                        @elseif($daysDiff == 1)
                            {{-- Expiration demain --}}
                            <span class="text-yellow-500 font-semibold">{{ $contrat->end_date }}</span>
                        @else
                            <span class="text-green-600 font-semibold">{{ $contrat->end_date }}</span>
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        {{ ucfirst($contrat->status) }}

                        {{-- Badge renouvelable --}}
                        @if($renewable)
                            <span class="ml-2 px-2 py-1 bg-green-100 text-green-700 text-xs rounded">
                                Renouvelable
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 flex space-x-2">

                        {{-- Toujours visible (tous rôles) --}}
                        <a href="{{ route('contrats.consult', $contrat->id) }}" 
                           class="text-gray-600 hover:text-gray-800" title="Voir">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        {{-- Actions Admin + Manager --}}
                        @if(auth()->user()->role !== 'locataire')

                            <a href="{{ route('contrats.edit', $contrat->id) }}" 
                               class="text-blue-500 hover:text-blue-700" title="Modifier">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <form action="{{ route('contrats.destroy', $contrat->id) }}" 
                                  method="POST" onsubmit="return confirm('Supprimer ce contrat ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" title="Supprimer">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                            @if($contrat->status === 'actif')
                                <form action="{{ route('contrats.terminate', $contrat->id) }}" 
                                      method="POST" onsubmit="return confirm('Résilier ce contrat ?');">
                                    @csrf
                                    <button type="submit" 
                                            class="text-yellow-500 hover:text-yellow-700" 
                                            title="Résilier">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            @endif

                            {{-- Renouvellement (admin + manager) --}}
                            @if($renewable)
                            <a href="{{ route('contrats.renewForm', $contrat->id) }}"
                               class="text-green-600 hover:text-green-800" 
                               title="Renouveler le contrat">
                                <i class="fa-solid fa-rotate-right"></i>
                            </a>
                            @endif
                        @endif

                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        Aucun contrat trouvé.
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $contrats->links() }}
    </div>
</div>

{{-- JS Tri des colonnes --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;

    const comparer = (idx, asc) => (a, b) => ((v1, v2) =>
        !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
    )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

    document.querySelectorAll('#contratsTable th[data-column]')
        .forEach(th => th.addEventListener('click', (() => {
            const table = th.closest('table');
            Array.from(table.querySelectorAll('tbody tr'))
                .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
                .forEach(tr => table.querySelector('tbody').appendChild(tr));
        })));
});
</script>

@endsection
