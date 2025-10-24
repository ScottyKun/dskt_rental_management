@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow p-6 mt-6">
    <h2 class="text-2xl font-semibold mb-4">{{ $message->title }}</h2>
    <p class="text-gray-500 mb-2">
        De : {{ $message->sender->name ?? 'Système' }} |
        Date : {{ $message->created_at->format('d/m/Y H:i') }}
    </p>

    <div class="border-t pt-4 text-gray-700 mb-6">
        {!! nl2br(e($message->content)) !!}
    </div>

    <div class="flex space-x-4">
        {{-- Retour --}}
        <a href="{{ route('messages.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">
            Retour
        </a>

        {{-- Marquer comme lu --}}
        @if(!$message->is_read)
        <a href="{{ route('messages.read', $message->id) }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
            Marquer comme lu
        </a>
        @endif
    </div>
</div>
@endsection
