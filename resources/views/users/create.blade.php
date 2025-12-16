@extends('layouts.appLimited')

@section('title', 'Ajouter un utilisateur')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-gray-100">
    <form action="{{ route('users.store') }}" method="POST" 
          class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5"
          x-data="{ role: '{{ old('role', '') }}' }">
        @csrf
        <h2 class="text-3xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-user-plus text-blue-500 mr-2"></i>Ajouter un utilisateur
        </h2>

        <!-- Nom -->
        <div>
            <label class="block mb-1 font-semibold">Nom</label>
            <input type="text" name="name" value="{{ old('name') }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        <!-- Prénom -->
        <div>
            <label class="block mb-1 font-semibold">Prénom</label>
            <input type="text" name="surname" value="{{ old('surname') }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        <!-- Email -->
        <div>
            <label class="block mb-1 font-semibold">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        <!-- Mot de passe -->
        <div x-data="{ show: false }">
            <label class="block mb-1 font-semibold">Mot de passe</label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password"
                    class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 text-gray-500">
                    <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                </button>
            </div>
        </div>

        <!-- Confirmer mot de passe -->
        <div>
            <label class="block mb-1 font-semibold">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        <!-- Role -->
        <div>
            <label class="block mb-1 font-semibold">Rôle</label>
            <select name="role" x-model="role"
                    class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
                <option value="">-- Sélectionner un rôle --</option>
                <option value="locataire">Locataire</option>
                <option value="gestionnaire">Gestionnaire</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <!-- Gestionnaire (visible uniquement si rôle = locataire) -->
        <div x-show="role === 'locataire'" x-transition>
            <label class="block mb-1 font-semibold">Gestionnaire</label>
            <select name="manager_id" x-ref="manager"
                    class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">-- Sélectionner un gestionnaire --</option>
                @foreach($managers as $manager)
                    <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                        {{ $manager->name }} {{ $manager->surname }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Téléphone -->
        <div>
            <label class="block mb-1 font-semibold">Téléphone</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <!-- Adresse -->
        <div>
            <label class="block mb-1 font-semibold">Adresse</label>
            <input type="text" name="address" value="{{ old('address') }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <!-- Submit -->
        <button type="submit"
            class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fa-solid fa-paper-plane mr-2"></i>Ajouter
        </button>
    </form>
</div>
@endsection
