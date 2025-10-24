@extends('layouts.appLimited')

@section('title', 'Changer le mot de passe')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-tr from-gray-100 to-blue-100">
    <form method="POST" action="{{ route('password.update') }}" 
          class="bg-white shadow-lg p-8 rounded-2xl w-full max-w-md space-y-6">
        @csrf
        <h2 class="text-2xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-lock text-blue-500 mr-2"></i>Changer le mot de passe
        </h2>

        <div x-data="{ show: false }">
            <label class="block mb-1 font-semibold">Nouveau mot de passe</label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password" 
                       class="w-full border p-3 rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
                <button type="button" @click="show = !show" 
                        class="absolute inset-y-0 right-3 text-gray-500">
                    <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                </button>
            </div>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" 
                   class="w-full border p-3 rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fa-solid fa-key mr-2"></i>Mettre à jour
        </button>
    </form>
</div>
@endsection
