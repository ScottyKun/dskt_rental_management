@extends('layouts.dashboard')

@section('dashboard-content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<div class="space-y-8">

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Bonjour {{ $user->name }} 👋</h1>
        <p class="text-sm text-gray-500">Vue d'ensemble de votre parc</p>
    </div>

    {{-- Occupation & contrats --}}
    <div>
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3">Mon parc</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            <x-kpi-card label="Immeubles" :value="$kpis['immeubles']" />
            <x-kpi-card label="Taux d'occupation" :value="$kpis['taux_occupation'] . '%'" color="blue" />
            <x-kpi-card label="Occupés" :value="$kpis['appartements_occupes']" />
            <x-kpi-card label="Disponibles" :value="$kpis['appartements_disponibles']" color="green" />
            <x-kpi-card label="Locataires actifs" :value="$kpis['locataires_actifs']" />
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow p-4 lg:col-span-2">
            <h3 class="text-sm font-semibold text-gray-600 mb-3">Évolution de mes revenus (6 derniers mois)</h3>
            <canvas id="revenueTrendChart" height="220"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <h3 class="text-sm font-semibold text-gray-600 mb-3">Occupation de mon parc</h3>
            <canvas id="occupancyChart" height="220"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow p-4">
            <h3 class="text-sm font-semibold text-gray-600 mb-3">Statut de mes contrats</h3>
            <canvas id="contractsChart" height="220"></canvas>
        </div>
    </div>

    {{-- Finance & suivi --}}
    <div>
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3">Finance & suivi</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            <x-kpi-card label="Revenus du mois" :value="number_format($kpis['revenus_du_mois'], 0, ',', ' ') . ' CFA'" color="blue" />
            <x-kpi-card label="Montant impayé" :value="number_format($kpis['montant_impaye'], 0, ',', ' ') . ' CFA'" color="red" />
            <x-kpi-card label="Loyers en retard" :value="$kpis['nb_loyers_en_retard']" color="amber" />
            <x-kpi-card label="Contrats expirent -30j" :value="$kpis['contrats_expirant_30j']" color="amber" />
            <x-kpi-card label="CNI en attente" :value="$kpis['cni_en_attente']" color="amber" />
        </div>
    </div>

    {{-- Actions rapides --}}
    <div>
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3">Actions rapides</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            <a href="{{ route('manager.create') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-user-plus text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Ajouter un locataire</span>
            </a>
            <a href="{{ route('contrats.create') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-file-contract text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Créer un contrat</span>
            </a>
            <a href="{{ route('payments.create') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-money-check-dollar text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Enregistrer un paiement</span>
            </a>
            <a href="{{ route('appartements.index') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-door-open text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Mes appartements</span>
            </a>
            <a href="{{ route('manager.index') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-users text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Mes locataires</span>
            </a>
            <a href="{{ route('messages.index') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-envelope text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Messages</span>
            </a>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const revenueTrend = @json($revenueTrend);
    const occupancy = @json($occupancyDonut);
    const contracts = @json($contractsDonut);

    new Chart(document.getElementById('revenueTrendChart'), {
        type: 'line',
        data: {
            labels: revenueTrend.labels,
            datasets: [{
                label: 'Revenus (CFA)',
                data: revenueTrend.data,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                tension: 0.3,
                fill: true,
            }]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('occupancyChart'), {
        type: 'doughnut',
        data: {
            labels: occupancy.labels,
            datasets: [{ data: occupancy.data, backgroundColor: ['#2563eb', '#22c55e'] }]
        },
        options: { responsive: true, maintainAspectRatio: true }
    });

    new Chart(document.getElementById('contractsChart'), {
        type: 'doughnut',
        data: {
            labels: contracts.labels,
            datasets: [{ data: contracts.data, backgroundColor: ['#2563eb', '#ef4444'] }]
        },
        options: { responsive: true, maintainAspectRatio: true }
    });
});
</script>
@endsection
