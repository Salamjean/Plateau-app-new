@extends('agent.layouts.template')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Notifications -->
    @if (Session::get('success1'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: '{{ Session::get('success1') }}',
                confirmButtonColor: '#1f4083',
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
                confirmButtonColor: '#1f4083',
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
                confirmButtonColor: '#1f4083',
                background: 'white'
            });
        </script>
    @endif

    <div class="modern-dashboard">
        <!-- Filtres Section -->
        <div class="filters-card">
            <div class="filters-header">
                <i class="fas fa-filter"></i>
                <h5>Filtrer les demandes par période</h5>
            </div>
            <form method="GET" action="{{ route('agent.dashboard') }}" class="filters-form">
                <div class="filter-group">
                    <label for="month" class="filter-label">
                        <i class="fas fa-calendar"></i> Mois
                    </label>
                    <select class="modern-select" id="month" name="month">
                        <option value="">Tous les mois</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromFormat('!m', $i)->locale('fr')->monthName }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="filter-group">
                    <label for="year" class="filter-label">
                        <i class="fas fa-calendar-week"></i> Année
                    </label>
                    <select class="modern-select" id="year" name="year">
                        <option value="">Toutes les années</option>
                        @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="filter-group">
                    <label for="type" class="filter-label">
                        <i class="fas fa-file-alt"></i> Type d'acte
                    </label>
                    <select class="modern-select" id="type" name="type">
                        <option value="">Tous les types</option>
                        <option value="naissance" {{ $selectedType == 'naissance' ? 'selected' : '' }}>Acte de naissance
                        </option>
                        <option value="deces" {{ $selectedType == 'deces' ? 'selected' : '' }}>Acte de décès</option>
                        <option value="mariage" {{ $selectedType == 'mariage' ? 'selected' : '' }}>Acte de mariage</option>
                    </select>
                </div>

                <div class="filter-group">
                    <button type="submit" class="apply-filter-btn">
                        <i class="fas fa-sync-alt"></i>
                        <span>Appliquer</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <!-- Naissances Card -->
            <div class="stat-card stat-card-birth">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <div class="stat-label">Actes de Naissances</div>
                        <div class="stat-value">{{ $naissancedash }}</div>
                        <div class="stat-meta">
                            <span class="stat-badge">
                                <i class="fas fa-arrow-up"></i> {{ number_format($naissancePercentage, 1) }}%
                            </span>
                            <span class="stat-text">du total des demandes</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <div class="icon-wrapper icon-birth">
                            <i class="fas fa-baby"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Décès Card -->
            <div class="stat-card stat-card-death">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <div class="stat-label">Actes de Décès</div>
                        <div class="stat-value">{{ $decesdash }}</div>
                        <div class="stat-meta">
                            <span class="stat-badge">
                                <i class="fas fa-arrow-up"></i> {{ number_format($decesPercentage, 1) }}%
                            </span>
                            <span class="stat-text">du total des demandes</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <div class="icon-wrapper icon-death">
                            <i class="fas fa-cross"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mariages Card -->
            <div class="stat-card stat-card-marriage">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <div class="stat-label">Actes de Mariages</div>
                        <div class="stat-value">{{ $mariagedash }}</div>
                        <div class="stat-meta">
                            <span class="stat-badge">
                                <i class="fas fa-arrow-up"></i> {{ number_format($mariagePercentage, 1) }}%
                            </span>
                            <span class="stat-text">du total des demandes</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <div class="icon-wrapper icon-marriage">
                            <i class="fas fa-heart"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Card -->
            <div class="stat-card stat-card-total">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <div class="stat-label">Total des demandes</div>
                        <div class="stat-value">{{ $totalData }}</div>
                        <div class="stat-meta">
                            <span class="stat-badge stat-badge-info">
                                <i class="fas fa-clock"></i> En attente
                            </span>
                            <span class="stat-text">demandes actuelles</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <div class="icon-wrapper icon-total">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toutes les demandes -->
        <div id="requests-table-container">
            @include('agent.partials.table_partial')
        </div>
    </div>

    <style>
        /* ========== MODERN DASHBOARD STYLES ========== */
        .modern-dashboard {
            padding: 24px;
            background: #f7fafc;
            min-height: 100vh;
        }

        /* Filters Card */
        .filters-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
        }

        .filters-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .filters-header i {
            color: #1f4083;
            font-size: 22px;
        }

        .filters-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
        }

        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-label i {
            color: #1f4083;
            font-size: 14px;
        }

        .modern-select {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            color: #2d3748;
            background: white;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .modern-select:focus {
            outline: none;
            border-color: #1f4083;
            box-shadow: 0 0 0 4px rgba(31, 64, 131, 0.1);
        }

        .apply-filter-btn {
            background: linear-gradient(135deg, #1f4083 0%, #2d5aa8 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(31, 64, 131, 0.3);
        }

        .apply-filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(31, 64, 131, 0.4);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-top: 4px solid;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.1) 100%);
            pointer-events: none;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        }

        .stat-card-birth {
            border-top-color: #1f4083;
        }

        .stat-card-death {
            border-top-color: #718096;
        }

        .stat-card-marriage {
            border-top-color: #e53e3e;
        }

        .stat-card-total {
            border-top-color: #2d5aa8;
        }

        .stat-card-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-info {
            flex: 1;
        }

        .stat-label {
            font-size: 14px;
            color: #718096;
            font-weight: 600;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 42px;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 12px;
            line-height: 1;
        }

        .stat-meta {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stat-badge {
            background: linear-gradient(135deg, #1f4083 0%, #2d5aa8 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .stat-badge-info {
            background: linear-gradient(135deg, #2d5aa8 0%, #4a72c0 100%);
        }

        .stat-text {
            font-size: 13px;
            color: #718096;
            font-weight: 500;
        }

        .stat-icon {
            flex-shrink: 0;
        }

        .icon-wrapper {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .icon-birth {
            background: linear-gradient(135deg, #1f4083 0%, #2d5aa8 100%);
        }

        .icon-death {
            background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
        }

        .icon-marriage {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        }

        .icon-total {
            background: linear-gradient(135deg, #2d5aa8 0%, #4a72c0 100%);
        }

        /* ========== MODERN TABLE STYLES ========== */
        .modern-table-wrapper {
            overflow-x: auto;
            background: white;
            border-radius: 12px;
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            font-size: 14px;
        }

        .modern-table thead {
            background: linear-gradient(135deg, #1f4083 0%, #2d5aa8 100%);
        }

        .modern-table thead th {
            padding: 16px 12px;
            color: white;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            border: none;
            white-space: nowrap;
        }

        .modern-table thead tr th:first-child {
            border-top-left-radius: 12px;
        }

        .modern-table thead tr th:last-child {
            border-top-right-radius: 12px;
        }

        .modern-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            background: white;
        }

        .modern-table tbody tr:hover {
            background: #f7fafc;
            box-shadow: 0 2px 8px rgba(31, 64, 131, 0.08);
            transform: scale(1.002);
        }

        .modern-table tbody tr:last-child {
            border-bottom: none;
        }

        .modern-table tbody td {
            padding: 14px 12px;
            color: #2d3748;
            vertical-align: middle;
            border: none;
            text-align: center;
        }

        /* Type Badge */
        .type-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .type-badge i {
            font-size: 14px;
        }

        .badge-naiss {
            background: linear-gradient(135deg, rgba(31, 64, 131, 0.1) 0%, rgba(45, 90, 168, 0.1) 100%);
            color: #1f4083;
            border: 2px solid #1f4083;
        }

        .badge-naiss:hover {
            background: linear-gradient(135deg, #1f4083 0%, #2d5aa8 100%);
            color: white;
        }

        .badge-deces {
            background: linear-gradient(135deg, rgba(113, 128, 150, 0.1) 0%, rgba(74, 85, 104, 0.1) 100%);
            color: #718096;
            border: 2px solid #718096;
        }

        .badge-deces:hover {
            background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
            color: white;
        }

        .badge-mariage {
            background: linear-gradient(135deg, rgba(229, 62, 62, 0.1) 0%, rgba(197, 48, 48, 0.1) 100%);
            color: #e53e3e;
            border: 2px solid #e53e3e;
        }

        .badge-mariage:hover {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
        }

        /* Demandeur Info */
        .demandeur-info {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 600;
            color: #2d3748;
            font-size: 13px;
        }

        .demandeur-info i {
            color: #1f4083;
            font-size: 16px;
        }

        /* Date & Time Info */
        .date-info,
        .time-info {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #4a5568;
            font-weight: 500;
            font-size: 13px;
        }

        .date-info i,
        .time-info i {
            color: #1f4083;
            font-size: 14px;
        }

        /* Action Button */
        .btn-modern-action {
            background: linear-gradient(135deg, #1f4083 0%, #2d5aa8 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(31, 64, 131, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .btn-modern-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(31, 64, 131, 0.5);
            background: linear-gradient(135deg, #2d5aa8 0%, #4a72c0 100%);
        }

        .btn-modern-action:active {
            transform: translateY(0);
        }

        .btn-modern-action i {
            font-size: 15px;
        }

        /* Empty State */
        .empty-table {
            padding: 0 !important;
            border: none !important;
        }

        .empty-content {
            padding: 80px 40px;
            text-align: center;
        }

        .empty-content i {
            font-size: 72px;
            color: #cbd5e0;
            margin-bottom: 20px;
            display: block;
        }

        .empty-content h5 {
            font-size: 20px;
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .empty-content p {
            font-size: 14px;
            color: #718096;
            margin: 0;
        }

        /* Pagination */
        .pagination {
            display: flex;
            gap: 6px;
            list-style: none;
            padding: 0;
            margin: 0;
            align-items: center;
        }

        .pagination li {
            list-style: none;
        }

        .pagination a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            min-width: 40px;
            background: #f7fafc;
            color: #2d3748;
            border: 1px solid #e2e8f0;
        }

        .pagination li.active a {
            background: linear-gradient(135deg, #1f4083 0%, #2d5aa8 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(31, 64, 131, 0.3);
            border-color: transparent;
        }

        .pagination li:not(.disabled):not(.active) a:hover {
            background: linear-gradient(135deg, #2d5aa8 0%, #4a72c0 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(31, 64, 131, 0.4);
            border-color: transparent;
        }

        .pagination li.disabled a {
            cursor: not-allowed;
            opacity: 0.5;
            background: #e9ecef;
            color: #aaa;
        }

        .text-muted {
            color: #718096;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .modern-table {
                font-size: 13px;
            }

            .modern-table thead th,
            .modern-table tbody td {
                padding: 14px 16px;
            }

            .type-badge {
                font-size: 12px;
                padding: 6px 12px;
            }

            .btn-modern-action {
                padding: 8px 16px;
                font-size: 12px;
            }
        }

        @media (max-width: 768px) {
            .modern-dashboard {
                padding: 16px;
            }

            .filters-form {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-value {
                font-size: 32px;
            }

            .icon-wrapper {
                width: 56px;
                height: 56px;
                font-size: 24px;
            }

            .modern-table-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .modern-table {
                min-width: 800px;
            }

            .modern-table thead th,
            .modern-table tbody td {
                padding: 12px 14px;
            }

            .type-badge {
                font-size: 11px;
                padding: 6px 10px;
            }

            .demandeur-info {
                font-size: 13px;
            }

            .btn-modern-action {
                padding: 8px 14px;
                font-size: 11px;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            function refreshTable(page = 1) {
                const month = $('select[name="month"]').val();
                const year = $('select[name="year"]').val();
                const type = $('select[name="type"]').val();

                $.ajax({
                    url: "{{ route('agent.dashboard.refresh') }}",
                    type: "GET",
                    data: {
                        month: month,
                        year: year,
                        type: type,
                        page: page
                    },
                    success: function(data) {
                        $('#requests-table-container').html(data);
                    },
                    error: function(xhr) {
                        console.log("Erreur lors du rafraîchissement du tableau");
                    }
                });
            }

            // Gestion de la pagination via AJAX
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const url = new URL($(this).attr('href'));
                const page = url.searchParams.get('page');
                refreshTable(page);
            });

            // Rafraîchir toutes les 60 secondes
            setInterval(function() {
                const currentPage = new URLSearchParams(window.location.search).get('page') || 1;
                if (currentPage == 1) refreshTable(1);
            }, 60000);
        });
    </script>
@endsection
