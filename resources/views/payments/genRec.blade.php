@extends('layouts.appLimited')

@section('title', 'Générer un reçu')

@section('content')
<div class="max-w-md mx-auto mt-6 bg-white p-6 rounded-xl shadow">
<h2 class="text-xl font-bold mb-4">Générer un reçu</h2>

<form method="POST" action="{{ route('receipts.generate',$payment->id) }}">
@csrf

<input type="date" name="start_date" class="w-full p-3 border rounded mb-3" required>
<input type="date" name="end_date" class="w-full p-3 border rounded mb-4" required>

<button class="bg-green-600 text-white px-4 py-2 rounded">
    Générer le reçu
</button>
</form>
</div>
@endsection