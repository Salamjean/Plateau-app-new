@extends('agent.layouts.template')

@section('content')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="{{asset('dasboard/agent.css')}}">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <div class="dashboard-container">
    <!-- Notifications -->
    @if (Session::get('success1'))
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Succès',
          text: '{{ Session::get('success1') }}',
          confirmButtonColor: '#007e00',
          background: 'white'
        });
      </script>
    @endif

    @if (Session::get('success'))
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Succès',
          text: '{{ Session::get('success') }}',
          confirmButtonColor: '#007e00',
          background: 'white'
        });
      </script>
    @endif

    @if (Session::get('error'))
      <script>
        Swal.fire({
          icon: 'error',
          title: 'Erreur',
          text: '{{ Session::get('error') }}',
          confirmButtonColor: '#007e00',
          background: 'white'
        });
      </script>
    @endif

    <h1 class="dashboard-title">
      <i class="fas fa-tachometer-alt me-2"></i>Tableau de Bord Agent
      <p>Vous verez ici toutes les demandes d'extraits effectués</p>
    </h1>
    <!-- Filtres -->
    <div class="filter-section">
      <h5 class="filter-title"><i class="fas fa-filter"></i>Filtrer les demandes par période</h5>
      <form class="filter-form" method="GET" action="{{ route('agent.dashboard') }}">
        <div class="form-group">
          <label class="form-label">Mois</label>
          <select class="form-select" name="month">
            @for ($i = 1; $i <= 12; $i++)
              <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                {{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
            @endfor
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Année</label>
          <select class="form-select" name="year">
            @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
              <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
          </select>
        </div>

        <button type="submit" class="btn-filter">
          <i class="fas fa-check me-1"></i>Appliquer
        </button>
      </form>
    </div>
    <!-- Statistiques -->
    <div class="stats-container">
      <div class="stat-card">
        <i class="fas fa-baby stat-icon"></i>
        <div class="stat-number">{{ $naissancedash }}</div>
        <div class="stat-label">Naissances ce mois</div>
        <span class="stat-percentage">{{ number_format($naissancePercentage, 1) }}% du total</span>
      </div>

      <div class="stat-card">
        <i class="fas fa-cross stat-icon"></i>
        <div class="stat-number">{{ $decesdash }}</div>
        <div class="stat-label">Décès ce mois</div>
        <span class="stat-percentage">{{ number_format($decesPercentage, 1) }}% du total</span>
      </div>

      <div class="stat-card secondary">
        <i class="fas fa-ring stat-icon"></i>
        <div class="stat-number">{{ $mariagedash }}</div>
        <div class="stat-label">Mariages ce mois</div>
        <span class="stat-percentage">{{ number_format($mariagePercentage, 1) }}% du total</span>
      </div>

      <div class="stat-card secondary">
        <i class="fas fa-file-alt stat-icon"></i>
        <div class="stat-number">{{ $totalData }}</div>
        <div class="stat-label">Total des demandes</div>
        <span class="stat-percentage">Ce mois</span>
      </div>
    </div>

    <!-- Toutes les demandes -->
    <div class="row">
      <div class="col-12">
        <div class="dashboard-card">
          <div class="card-header">
            <h5><i class="fas fa-list me-2"></i>Toutes les demandes en attente</h5>
            <span class="badge bg-light text-dark" id="total-badge">{{ $allRequests->count() }} demandes</span>
          </div>
          <div class="card-body" id="requests-table-container">
            @include('agent.partials.table_partial')
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function () {
      // Adaptation pour mobile
      function adaptForMobile() {
        if (window.innerWidth <= 768) {
          // Ajout des data-labels pour l'affichage mobile
          $('#requests-table-container table thead th').each(function () {
            const headerText = $(this).text();
            const columnIndex = $(this).index();
            $('#requests-table-container table tbody tr td:nth-child(' + (columnIndex + 1) + ')').attr('data-label', headerText);
          });
        }
      }

      function refreshTable() {
        const month = $('select[name="month"]').val();
        const year = $('select[name="year"]').val();

        $.ajax({
          url: "{{ route('agent.dashboard.refresh') }}",
          type: "GET",
          data: {
            month: month,
            year: year
          },
          success: function (data) {
            $('#requests-table-container').html(data);

            // Update total badge count
            const count = $('#requests-table-container table tbody tr:not(:has(.empty-state))').length;
            if ($('#requests-table-container .empty-state').length > 0) {
              $('#total-badge').text('0 demandes');
            } else {
              $('#total-badge').text(count + ' demandes');
            }

            // Re-apply mobile adaptations
            adaptForMobile();
          },
          error: function (xhr) {
            console.log("Erreur lors du rafraîchissement de la table");
          }
        });
      }

      // Exécuter au chargement et lors du redimensionnement
      adaptForMobile();
      $(window).resize(adaptForMobile);

      // Rafraîchir toutes les 15 secondes
      setInterval(refreshTable, 15000);
    });
  </script>
@endsection