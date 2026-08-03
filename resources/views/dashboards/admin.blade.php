@extends('layouts.dashboard')

@section('dashboard-content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<div class="space-y-8">

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Bonjour {{ $user->name }} 👋</h1>
        <p class="text-sm text-gray-500">Vue d'ensemble de la plateforme</p>
    </div>

    {{-- Occupation --}}
    <div>
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3">Occupation</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            <x-kpi-card label="Taux d'occupation" :value="$kpis['occupation']['taux_occupation'] . '%'" color="blue" />
            <x-kpi-card label="Appartements occupés" :value="$kpis['occupation']['occupes']" />
            <x-kpi-card label="Disponibles" :value="$kpis['occupation']['disponibles']" color="green" />
            <x-kpi-card label="Total appartements" :value="$kpis['occupation']['total_appartements']" />
            <x-kpi-card label="Vacants +30j" :value="$kpis['occupation']['vacants_longue_duree']" color="amber" />
        </div>
    </div>

    {{-- Finance --}}
    <div>
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3">Finance</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            <x-kpi-card label="Revenus du mois" :value="number_format($kpis['finance']['revenus_du_mois'], 0, ',', ' ') . ' CFA'" color="blue" />
            <x-kpi-card label="Mois dernier" :value="number_format($kpis['finance']['revenus_mois_dernier'], 0, ',', ' ') . ' CFA'" />
            <x-kpi-card label="Taux de recouvrement" :value="$kpis['finance']['taux_recouvrement'] . '%'" color="green" />
            <x-kpi-card label="Montant impayé" :value="number_format($kpis['finance']['montant_impaye'], 0, ',', ' ') . ' CFA'" color="red" />
            <x-kpi-card label="Loyers en retard" :value="$kpis['finance']['nb_loyers_en_retard']" color="amber" />
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow p-4 lg:col-span-2">
            <h3 class="text-sm font-semibold text-gray-600 mb-3">Évolution des revenus (6 derniers mois)</h3>
            <canvas id="revenueTrendChart" height="220"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <h3 class="text-sm font-semibold text-gray-600 mb-3">Occupation du parc</h3>
            <canvas id="occupancyChart" height="220"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow p-4 lg:col-span-2">
            <h3 class="text-sm font-semibold text-gray-600 mb-3">Revenus du mois par gestionnaire</h3>
            <canvas id="revenueByManagerChart" height="220"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <h3 class="text-sm font-semibold text-gray-600 mb-3">Statut des contrats</h3>
            <canvas id="contractsChart" height="220"></canvas>
        </div>
    </div>

    {{-- Contrats --}}
    <div>
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3">Contrats</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            <x-kpi-card label="Contrats actifs" :value="$kpis['contrats']['actifs']" color="blue" />
            <x-kpi-card label="Expirent -30j" :value="$kpis['contrats']['expirant_30j']" color="amber" />
            <x-kpi-card label="Expirent -60j" :value="$kpis['contrats']['expirant_60j']" />
            <x-kpi-card label="Expirent -90j" :value="$kpis['contrats']['expirant_90j']" />
            <x-kpi-card label="Durée moyenne" :value="($kpis['contrats']['duree_moyenne_jours'] ?? '—') . ' j'" />
        </div>
    </div>

    {{-- Utilisateurs & documents --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3">Utilisateurs</h2>
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                <x-kpi-card label="Locataires actifs" :value="$kpis['utilisateurs']['locataires_actifs']" color="blue" />
                <x-kpi-card label="En attente de validation" :value="$kpis['utilisateurs']['locataires_en_attente']" color="amber" />
                <x-kpi-card label="Nouveaux (30j)" :value="$kpis['utilisateurs']['nouveaux_locataires_30j']" color="green" />
                <x-kpi-card label="Gestionnaires" :value="$kpis['utilisateurs']['gestionnaires']" />
            </div>
        </div>
        <div>
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3">Documents & messages</h2>
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                <x-kpi-card label="CNI validées" :value="$kpis['documents']['cni_validees']" color="green" />
                <x-kpi-card label="CNI en attente" :value="$kpis['documents']['cni_en_attente']" color="amber" />
                <x-kpi-card label="En attente +5j" :value="$kpis['documents']['cni_en_attente_longue']" color="red" />
                <x-kpi-card label="Messages non traités" :value="$kpis['messages']['non_traites']" color="amber" />
            </div>
        </div>
    </div>

    {{-- Attribution des propriétaires (parc par gestionnaire) --}}
    <div>
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3">Attribution des propriétaires</h2>

        @if($ressourcesOrphelines['immeubles'] > 0)
            <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm rounded-lg p-3 mb-3">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                {{ $ressourcesOrphelines['immeubles'] }} immeuble(s) sans gestionnaire assigné.
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded-xl shadow">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Gestionnaire</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Immeubles</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Occupation</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Revenus du mois</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attribution as $row)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $row['nom'] }}</td>
                            <td class="px-4 py-3">{{ $row['immeubles'] }}</td>
                            <td class="px-4 py-3">{{ $row['appartements_occupes'] }} / {{ $row['appartements_total'] }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ number_format($row['revenus_du_mois'], 0, ',', ' ') }} CFA</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Aucun gestionnaire pour l'instant.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Actions rapides --}}
    <div>
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3">Actions rapides</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            <a href="{{ route('immeubles.create') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-building text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Ajouter un immeuble</span>
            </a>
            <a href="{{ route('appartements.create') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-door-open text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Ajouter un appartement</span>
            </a>
            <a href="{{ route('users.create') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-user-plus text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Créer un utilisateur</span>
            </a>
            <a href="{{ route('contrats.create') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-file-contract text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Créer un contrat</span>
            </a>
            <a href="{{ route('payments.index') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-money-check-dollar text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Voir les paiements</span>
            </a>
            <a href="{{ route('users.index') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-users text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Gérer les utilisateurs</span>
            </a>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const revenueTrend = @json($revenueTrend);
    const occupancy = @json($occupancyDonut);
    const contracts = @json($contractsDonut);
    const revenueByManager = @json($revenueByManager);

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

    new Chart(document.getElementById('revenueByManagerChart'), {
        type: 'bar',
        data: {
            labels: revenueByManager.labels,
            datasets: [{ label: 'Revenus du mois (CFA)', data: revenueByManager.data, backgroundColor: '#2563eb' }]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } } }
    });
});
</script>
@endsection
