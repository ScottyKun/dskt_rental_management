@extends('layouts.appLimited')

@section('title', 'Vérification en 2 étapes')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-gray-100">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5">
        <div class="text-center">
            <i class="fa-solid fa-envelope-open-text text-4xl text-blue-500 mb-3"></i>
            <h2 class="text-2xl font-bold text-gray-800">Vérification en 2 étapes</h2>
            <p class="text-sm text-gray-500 mt-2">
                Un code à 6 chiffres a été envoyé à<br>
                <span class="font-semibold">{{ $maskedEmail }}</span>
            </p>
        </div>

        <form action="{{ route('mfa.verify') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block mb-1 font-semibold">Code de vérification</label>
                <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                    autocomplete="one-time-code" autofocus
                    class="w-full p-3 border rounded text-center text-2xl tracking-[0.5em] focus:ring-2 focus:ring-blue-500 outline-none"
                    placeholder="------" required>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white p-3 rounded hover:bg-blue-700 transition">
                Valider
            </button>
        </form>

        <form action="{{ route('mfa.resend') }}" method="POST" class="text-center">
            @csrf
            <button type="submit" class="text-sm text-blue-600 hover:underline">
                Je n'ai pas reçu de code, renvoyer
            </button>
        </form>

        <p class="text-center text-xs text-gray-400">Le code expire au bout de 10 minutes.</p>
    </div>
</div>
@endsection
