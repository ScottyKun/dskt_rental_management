@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow">

    <form method="POST" action="{{ route('messages.request.store') }}">

        @csrf
        <h2 class="text-3xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-envelope text-blue-500 mr-2"></i>Faire une demande
        </h2>
        <label class="block text-sm font-medium mb-2">Titre du message :</label>
        <input type="text" name="title" class="w-full p-3 border rounded-lg mb-4" placeholder="Ex: Demande de renouvellement du contrat" required/>

        <label class="block text-sm font-medium mb-2">Votre message :</label>
        <textarea name="content" rows="6" class="w-full p-3 border rounded-lg" required></textarea>

        <div class="flex justify-end mt-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Envoyer la demande
            </button>
        </div>
    </form>
</div>
@endsection
