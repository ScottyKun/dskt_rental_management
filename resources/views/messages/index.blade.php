@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">

    {{-- Actions : Ajouter + Recherche si besoin --}}
    <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mt-4 space-y-3 md:space-y-0">
        <h2 class="text-2xl font-semibold text-gray-700">Messages</h2>
    </div>

    {{-- Tableau des messages --}}
    <div class="overflow-x-auto mt-4 bg-white rounded-xl shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 cursor-pointer">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">De</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($messages as $message)
                <tr class="{{ !$message->is_read ? 'bg-blue-50' : '' }}">
                    <td class="px-6 py-4">{{ $message->title }}</td>
                    <td class="px-6 py-4">{{ $message->sender->name ?? 'Système' }}</td>
                    <td class="px-6 py-4">
                        @if($message->is_read)
                            <span class="text-green-600 font-semibold">Lu</span>
                        @else
                            <span class="text-red-600 font-semibold">Non lu</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $message->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 flex space-x-2">
                        {{-- Consulter --}}
                        <a href="{{ route('messages.consult', $message->id) }}" class="text-blue-500 hover:text-blue-700" title="Consulter">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        {{-- Marquer comme lu --}}
                        @if(!$message->is_read)
                        <a href="{{ route('messages.read', $message->id) }}" class="text-green-500 hover:text-green-700" title="Marquer comme lu">
                            <i class="fa-solid fa-check"></i>
                        </a>
                        @endif

                        {{-- Supprimer --}}
                        <form method="POST" action="{{ route('messages.delete', $message->id) }}" onsubmit="return confirm('Voulez-vous vraiment supprimer ce message ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" title="Supprimer">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4 flex justify-end space-x-2">
        {{ $messages->links() }}
    </div>

</div>
@endsection
