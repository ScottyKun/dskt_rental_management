@extends('layouts.appLimited')

@section('title', 'Modifier un utilisateur')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-gray-100">
    <form action="{{ route('users.update', $user->id) }}" method="POST" 
          class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5">
        @csrf
        @method('PUT')
        <h2 class="text-3xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-user-edit text-blue-500 mr-2"></i>Modifier l'utilisateur
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

        <div x-data="{ show: false }">
            <label class="block mb-1 font-semibold">Mot de passe</label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password" 
                    class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 text-gray-500">
                    <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                </button>
            </div>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div>
            <label class="block mb-1 font-semibold">Rôle</label>
            <select name="role" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
                <option value="locataire" {{ old('role', $user->role) === 'locataire' ? 'selected' : '' }}>Locataire</option>
                <option value="gestionnaire" {{ old('role', $user->role) === 'gestionnaire' ? 'selected' : '' }}>Gestionnaire</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
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

        <button type="submit"
            class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fa-solid fa-paper-plane mr-2"></i>Mettre à jour
        </button>
    </form>
</div>
@endsection
