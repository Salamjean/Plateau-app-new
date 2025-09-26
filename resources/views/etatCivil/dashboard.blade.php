@extends('etatCivil.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="{{asset('dasboard/etatCivil.css')}}">

<div class="dashboard-container">
  <h1 class="page-title">
    <i class="fas fa-tachometer-alt me-2"></i>Tableau de Bord - État Civil
  </h1>

  <!-- Cartes de statistiques -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-baby"></i>
      </div>
      <div class="stat-content">
        <h3>{{ number_format($totalNaissances) }}</h3>
        <p>Demandes d'extraits de naissance</p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-cross"></i>
      </div>
      <div class="stat-content">
        <h3>{{ number_format($totalDeces) }}</h3>
        <p>Demandes d'extraits de décès</p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-ring"></i>
      </div>
      <div class="stat-content">
        <h3>{{ number_format($totalMariages) }}</h3>
        <p>Demandes d'extraits de mariage</p>
      </div>
    </div>

    <div class="stat-card agents">
      <div class="stat-icon">
        <i class="fas fa-users"></i>
      </div>
      <div class="stat-content">
        <h3>{{ number_format($totalAgents) }}</h3>
        <p>Agents d'Etat civil</p>
      </div>
    </div>
  </div>

  <!-- Actions rapides -->
  <div class="quick-actions">
    <div class="action-card">
      <div class="action-icon">
        <i class="fas fa-file-alt"></i>
      </div>
      <h4 class="action-title">Rapports Mensuels</h4>
      <p class="action-description">Générez et consultez les rapports d'activité</p>
      <a href="{{ route('etatCivil.rapports') }}" class="action-btn">Voir rapports</a>
    </div>

    <div class="action-card">
      <div class="action-icon">
        <i class="fas fa-chart-line"></i>
      </div>
      <h4 class="action-title">Statistiques des 12 derniers mois</h4>
      <p class="action-description">Analyses détaillées des demandes et des travaux des agents</p>
      <a href="{{ route('etatCivil.rapports.preview') }}?periode=${12}" target="_blank" class="action-btn">Voir stats</a>
    </div>
  </div>

  <!-- Grille principale -->
  <div class="dashboard-grid">
    <!-- Graphique -->
    <div class="chart-card">
      <div class="chart-header">
        <h3 class="chart-title"><i class="fas fa-chart-bar"></i>Évolution des demandes</h3>
      </div>
      <canvas id="demandesChart" height="110"></canvas>
    </div>

    <!-- Activité récente -->
    <div class="recent-activity">
      <div class="activity-header">
        <h3 class="activity-title"><i class="fas fa-history"></i>Activité récente</h3>
        <a href="#" class="view-all-btn">Tout voir <i class="fas fa-arrow-right"></i></a>
      </div>
      <ul class="activity-list">
        @forelse($activiteRecente as $activite)
        <li class="activity-item">
          <div class="activity-icon">
            <i class="{{ $activite['icon'] }}"></i>
          </div>
          <div class="activity-content">
            <p class="activity-text">{{ $activite['message'] }}</p>
            <span class="activity-time">{{ $activite['time'] }}</span>
          </div>
        </li>
        @empty
        <li class="activity-item">
          <div class="activity-content">
            <p class="activity-text">Aucune activité récente</p>
          </div>
        </li>
        @endforelse
      </ul>
    </div>
  </div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Graphique des demandes
    const demandesCtx = document.getElementById('demandesChart').getContext('2d');
    new Chart(demandesCtx, {
      type: 'line',
      data: {
        labels: {!! json_encode($demandesParMois['mois']) !!},
        datasets: [
          {
            label: 'Naissances',
            data: {!! json_encode($demandesParMois['naissances']) !!},
            borderColor: '#1977cc',
            backgroundColor: 'rgba(255, 136, 0, 0.1)',
            tension: 0.4,
            fill: true
          },
          {
            label: 'Décès',
            data: {!! json_encode($demandesParMois['deces']) !!},
            borderColor: '#6c757d',
            backgroundColor: 'rgba(108, 117, 125, 0.1)',
            tension: 0.4,
            fill: true
          },
          {
            label: 'Mariages',
            data: {!! json_encode($demandesParMois['mariages']) !!},
            borderColor: '#ff0000',
            backgroundColor: 'rgba(0, 126, 0, 0.1)',
            tension: 0.4,
            fill: true
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    });

    // Graphique des statuts
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
      type: 'doughnut',
      data: {
        labels: ['Terminées', 'En cours', 'En attente'],
        datasets: [{
          data: [
            {{ $statutsNaissance['termine'] }},
            {{ $statutsNaissance['en_cours'] }},
            {{ $statutsNaissance['en_attente'] }}
          ],
          backgroundColor: [
            '#1977cc',
            '#6c757d',
            '#ff0000'
          ],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });

    // Graphique des temps de traitement
    const timeCtx = document.getElementById('timeChart').getContext('2d');
    new Chart(timeCtx, {
      type: 'bar',
      data: {
        labels: ['Naissances', 'Décès', 'Mariages'],
        datasets: [{
          label: 'Jours moyens',
          data: [
            {{ number_format($tempsTraitement['naissances'], 1) }},
            {{ number_format($tempsTraitement['deces'], 1) }},
            {{ number_format($tempsTraitement['mariages'], 1) }}
          ],
          backgroundColor: '#ff8800',
          borderRadius: 5
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    });
  });
</script>
@endsection