@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Pièces d'identité</h1>
    </div>

    {{-- Recherche + filtre --}}
    <form method="GET" action="{{ route('contrats.documents') }}"
          class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="q" placeholder="Rechercher un locataire..."
               value="{{ request('q') }}"
               class="px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 w-full sm:w-64">

        <select name="status" class="px-3 py-2 border rounded-lg w-full sm:w-56">
            <option value="">— Tous les statuts —</option>
            <option value="en_attente" {{ request('status') === 'en_attente' ? 'selected' : '' }}>En attente</option>
            <option value="valide" {{ request('status') === 'valide' ? 'selected' : '' }}>Validée</option>
            <option value="refuse" {{ request('status') === 'refuse' ? 'selected' : '' }}>Refusée</option>
        </select>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition w-full sm:w-auto">
            <i class="fa-solid fa-magnifying-glass mr-1"></i> Filtrer
        </button>

        @if(request('q') || request('status'))
            <a href="{{ route('contrats.documents') }}" class="text-sm text-gray-500 self-center hover:underline">Réinitialiser</a>
        @endif
    </form>

    {{-- Tableau --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Locataire</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Contrat</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fichier</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Statut</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Transmis le</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($documents as $document)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{ $document->contrat->tenant->name ?? '—' }} {{ $document->contrat->tenant->surname ?? '' }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('contrats.consult', $document->contrat_id) }}" class="text-blue-600 hover:underline">
                                #{{ $document->contrat_id }}
                            </a>
                        </td>
                        <td class="px-4 py-3 max-w-[200px] truncate">{{ $document->original_name }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'px-2 py-1 rounded-full text-xs font-medium',
                                'bg-amber-100 text-amber-700' => $document->status === 'en_attente',
                                'bg-green-100 text-green-700' => $document->status === 'valide',
                                'bg-red-100 text-red-700' => $document->status === 'refuse',
                            ])>
                                {{ str_replace('_', ' ', $document->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $document->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('contrats.document.view', [$document->contrat_id, $document->id]) }}"
                               target="_blank" class="text-blue-600 hover:underline mr-3">
                                <i class="fa-solid fa-eye mr-1"></i>Voir
                            </a>
                            <a href="{{ route('contrats.document.download', [$document->contrat_id, $document->id]) }}"
                               class="text-gray-600 hover:underline">
                                <i class="fa-solid fa-download mr-1"></i>Télécharger
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Aucune pièce transmise pour l'instant.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $documents->links() }}</div>

</div>
@endsection
