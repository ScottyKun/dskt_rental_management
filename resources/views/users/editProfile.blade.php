@extends('layouts.dashboard')

@section('title', 'Modifier mes informations')

@section('dashboard-content')
<div class="max-w-xl mx-auto my-2 sm:my-6">
    <form action="{{ route('profile.update') }}" method="POST"
          class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5">
        @csrf
        @method('PUT')
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-user-edit text-blue-500 mr-2"></i>Modifier mes informations
        </h2>

        <div>
            <label class="block mb-1 font-semibold">Nom</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Prénom</label>
            <input type="text" name="surname" value="{{ old('surname', $user->surname) }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Téléphone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div>
            <label class="block mb-1 font-semibold">Adresse</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div>
            <label class="block mb-1 font-semibold">N° CNI</label>
            <input type="text" name="cni_number" value="{{ old('cni_number', $user->cni_number) }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div>
            <label class="block mb-1 font-semibold">Profession</label>
            <input type="text" name="profession" value="{{ old('profession', $user->profession) }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <button type="submit"
            class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fa-solid fa-paper-plane mr-2"></i>Mettre à jour
        </button>

        <a href="{{ route('password.change') }}"
           class="block text-center text-sm text-blue-600 hover:underline">
            <i class="fa-solid fa-key mr-1"></i>Changer mon mot de passe
        </a>
    </form>
</div>
@endsection