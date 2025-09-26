@extends('etatCivil.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="{{asset('dasboard/etatCivil.css')}}">

<div class="dashboard-container">
  <h1 class="page-title">
    <i class="fas fa-file-alt me-2"></i>Rapports Détaillés - État Civil
  </h1>

  <!-- Filtres de période -->
  <div class="filters-section">
    
    <h4><i class="fas fa-calendar-alt me-2"></i>Période d'analyse</h4>
    <div class="filter-card">
      <div class="period-buttons">
        <a href="?periode=3mois" class="period-btn {{ $periode == '3mois' ? 'active' : '' }}">3 mois</a>
        <a href="?periode=6mois" class="period-btn {{ $periode == '6mois' ? 'active' : '' }}">6 mois</a>
        <a href="?periode=12mois" class="period-btn {{ $periode == '12mois' ? 'active' : '' }}">12 mois</a>
      </div>
        <button class="export-btn" onclick="exportReport()">
        <i class="fas fa-download me-2"></i>Exporter le rapport PDF
        </button>
    </div>
  </div>

  <!-- Statistiques globales -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-baby"></i>
      </div>
      <div class="stat-content">
        <h3>{{ number_format($statsDetaillees['naissances']['total']) }}</h3>
        <p>Naissances totales</p>
        <div class="stat-details">
          <span class="badge success">{{ $statsDetaillees['naissances']['termine'] }} terminées</span>
          <span class="badge warning">{{ $statsDetaillees['naissances']['en_cours'] }} en cours</span>
        </div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-cross"></i>
      </div>
      <div class="stat-content">
        <h3>{{ number_format($statsDetaillees['deces']['total']) }}</h3>
        <p>Décès totaux</p>
        <div class="stat-details">
          <span class="badge success">{{ $statsDetaillees['deces']['termine'] }} terminés</span>
          <span class="badge warning">{{ $statsDetaillees['deces']['en_cours'] }} en cours</span>
        </div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-ring"></i>
      </div>
      <div class="stat-content">
        <h3>{{ number_format($statsDetaillees['mariages']['total']) }}</h3>
        <p>Mariages totaux</p>
        <div class="stat-details">
          <span class="badge success">{{ $statsDetaillees['mariages']['termine'] }} terminés</span>
          <span class="badge warning">{{ $statsDetaillees['mariages']['en_cours'] }} en cours</span>
        </div>
      </div>
    </div>

    <div class="stat-card agents">
      <div class="stat-icon">
        <i class="fas fa-chart-line"></i>
      </div>
      <div class="stat-content">
        <h3>{{ number_format($statsDetaillees['naissances']['taux_completion'] + $statsDetaillees['deces']['taux_completion'] + $statsDetaillees['mariages']['taux_completion'] / 3, 1) }}%</h3>
        <p>Taux de complétion moyen</p>
      </div>
    </div>
  </div>

  <!-- Graphiques détaillés -->
  <div class="charts-grid">
    <div class="chart-card large">
      <div class="chart-header">
        <h3 class="chart-title"><i class="fas fa-chart-line"></i>Évolution des demandes sur {{ $months }} mois</h3>
      </div>
      <canvas id="evolutionChart" height="80"></canvas>
    </div>

    <div class="chart-card">
      <div class="chart-header">
        <h3 class="chart-title"><i class="fas fa-chart-pie"></i>Répartition par type</h3>
      </div>
      <canvas id="repartitionChart" height="200"></canvas>
    </div>
</div>
  <!-- Top agents -->
  <div class="agents-section">
    <div class="section-card">
      <div class="section-header">
        <h3><i class="fas fa-trophy me-2"></i>Top Agents</h3>
      </div>
      <div class="agents-list">
        @foreach($topAgents as $index => $agent)
        <div class="agent-item">
          <div class="agent-rank">{{ $index + 1 }}</div>
          <div class="agent-info">
            <h5>{{ $agent['name'] }}</h5>
            <p>{{ $agent['demandes_traitees'] }} demandes traitées</p>
          </div>
          <div class="agent-stats">
            <span class="satisfaction-rate">{{ $agent['taux_satisfaction'] }}%</span>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Graphique d'évolution
  const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
  new Chart(evolutionCtx, {
    type: 'line',
    data: {
      labels: {!! json_encode($donneesGraphiques['labels']) !!},
      datasets: {!! json_encode($donneesGraphiques['datasets']) !!}
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        }
      }
    }
  });

  // Graphique de répartition
  const repartitionCtx = document.getElementById('repartitionChart').getContext('2d');
  new Chart(repartitionCtx, {
    type: 'doughnut',
    data: {
      labels: ['Naissances', 'Décès', 'Mariages'],
      datasets: [{
        data: [
          {{ $statsDetaillees['naissances']['total'] }},
          {{ $statsDetaillees['deces']['total'] }},
          {{ $statsDetaillees['mariages']['total'] }}
        ],
        backgroundColor: ['#1977cc', '#6c757d', '#ff0000']
      }]
    }
  });

  // Graphique de taux de complétion
  const completionCtx = document.getElementById('completionChart').getContext('2d');
  new Chart(completionCtx, {
    type: 'bar',
    data: {
      labels: ['Naissances', 'Décès', 'Mariages'],
      datasets: [{
        label: 'Taux de complétion (%)',
        data: [
          {{ $statsDetaillees['naissances']['taux_completion'] }},
          {{ $statsDetaillees['deces']['taux_completion'] }},
          {{ $statsDetaillees['mariages']['taux_completion'] }}
        ],
        backgroundColor: ['#1977cc', '#6c757d', '#ff0000']
      }]
    }
  });
});

function exportReport() {
    const periode = new URLSearchParams(window.location.search).get('periode') || '12mois';
    
    Swal.fire({
        title: 'Exporter le rapport',
        html: `
            <div style="text-align: left;">
                <p><strong>Options d'export :</strong></p>
                <div style="margin: 10px 0;">
                    <label>
                        <input type="radio" name="exportType" value="download" checked> 
                        Télécharger le PDF
                    </label>
                </div>
                <div style="margin: 10px 0;">
                    <label>
                        <input type="radio" name="exportType" value="preview"> 
                        Aperçu avant impression
                    </label>
                </div>
                <div style="margin-top: 15px; font-size: 0.9em; color: #666;">
                    Période sélectionnée : ${periode.replace('mois', ' mois')}
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1977cc',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Exporter',
        cancelButtonText: 'Annuler',
        preConfirm: () => {
            const exportType = document.querySelector('input[name="exportType"]:checked').value;
            return { exportType };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { exportType } = result.value;
            
            if (exportType === 'download') {
                // Téléchargement direct
                window.location.href = `{{ route('etatCivil.rapports.export') }}?periode=${periode}`;
                Swal.fire('Succès!', 'Le téléchargement du PDF a débuté.', 'success');
            } else {
                // Aperçu
                window.open(`{{ route('etatCivil.rapports.preview') }}?periode=${periode}`, '_blank');
                Swal.fire('Aperçu', 'Aperçu dans un autre onglet.', 'info');
            }
        }
    });
}
</script>

<style>
.filters-section {
  margin-bottom: 30px;
  width: 100%;
}

.filter-card {
  background: white;
  border-radius: var(--border-radius);
  padding: 20px;
  box-shadow: var(--box-shadow);
  display: flex;
  justify-content: space-between;
}

.period-buttons {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

.period-btn {
  padding: 8px 20px;
  border: 2px solid var(--primary-color);
  border-radius: 20px;
  color: var(--primary-color);
  text-decoration: none;
  transition: var(--transition);
}

.period-btn.active,
.period-btn:hover {
  background: var(--primary-color);
  color: white;
}

.charts-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 25px;
  margin-bottom: 30px;
}

.chart-card.large {
  grid-column: span 2;
}

.stat-details {
  margin-top: 10px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.8rem;
  margin-right: 5px;
}

.badge.success {
  background: rgba(0, 126, 0, 0.1);
  color: var(--secondary-color);
}

.badge.warning {
  background: rgba(255, 136, 0, 0.1);
  color: var(--primary-color);
}

.agents-section {
  margin-bottom: 30px;
  width: 100%
}

.section-card {
  background: white;
  border-radius: var(--border-radius);
  padding: 25px;
  box-shadow: var(--box-shadow);
}

.section-header {
  border-bottom: 2px solid rgba(0, 126, 0, 0.1);
  padding-bottom: 15px;
  margin-bottom: 20px;
}

.agent-item {
  display: flex;
  align-items: center;
  padding: 15px 0;
  border-bottom: 1px solid rgba(0, 126, 0, 0.1);
}

.agent-item:last-child {
  border-bottom: none;
}

.agent-rank {
  width: 30px;
  height: 30px;
  background: var(--primary-color);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  margin-right: 15px;
}

.agent-info {
  flex: 1;
}

.agent-info h5 {
  margin: 0;
  color: var(--dark-color);
}

.agent-info p {
  margin: 0;
  color: var(--gray-color);
  font-size: 0.9rem;
}

.satisfaction-rate {
  background: var(--secondary-color);
  color: white;
  padding: 5px 10px;
  border-radius: 15px;
  font-weight: bold;
}

.export-section {
  text-align: center;
  width: 100%
}

.export-btn {
  background: var(--secondary-color);
  color: white;
  border: none;
  align-items: center;
  border-radius: 25px;
  margin-top: 10px;
  padding: 12px 30px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
}

.export-btn:hover {
  background: var(--secondary-dark);
  transform: translateY(-2px);
}

@media (max-width: 1200px) {
  .charts-grid {
    grid-template-columns: 1fr;
  }
  
  .chart-card.large {
    grid-column: span 1;
  }
}
</style>
@endsection