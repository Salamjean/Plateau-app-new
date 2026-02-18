@extends('comptable.layouts.template')

@section('content')
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Demandes en Ligne | Comptable</title>
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            :root {
                --primary: #4361ee;
                --primary-hover: #3a56d4;
                --secondary: #3f37c9;
                --success: #00b894;
                --warning: #f1c40f;
                --danger: #d63031;
                --light: #f8f9fa;
                --dark: #2d3436;
                --text-main: #2d3436;
                --text-muted: #636e72;
                --card-bg: #ffffff;
                --body-bg: #f4f7f6;
                --border-radius: 16px;
                --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            }

            body {
                font-family: 'Inter', sans-serif;
                background-color: var(--body-bg);
                color: var(--text-main);
            }

            .page-header {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                margin-bottom: 2rem;
                padding: 0 0.5rem;
            }

            @media (min-width: 768px) {
                .page-header {
                    flex-direction: row;
                    align-items: center;
                    justify-content: space-between;
                }
            }

            .page-title {
                font-size: 1.75rem;
                font-weight: 800;
                color: var(--text-main);
                margin: 0;
                letter-spacing: -0.5px;
            }

            .page-subtitle {
                font-size: 0.95rem;
                color: var(--text-muted);
                margin-top: 0.25rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            /* Stats Cards */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 1.5rem;
                margin-bottom: 2rem;
            }

            .stat-card {
                background: var(--card-bg);
                border-radius: var(--border-radius);
                padding: 1.5rem;
                box-shadow: var(--card-shadow);
                display: flex;
                align-items: center;
                gap: 1rem;
                transition: var(--transition);
                border: 1px solid rgba(0, 0, 0, 0.02);
            }

            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                flex-shrink: 0;
            }

            .stat-info {
                display: flex;
                flex-direction: column;
            }

            .stat-label {
                font-size: 0.85rem;
                font-weight: 600;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .stat-value {
                font-size: 1.5rem;
                font-weight: 800;
                color: var(--text-main);
                line-height: 1.2;
            }

            /* Main Card table */
            .card-modern {
                background: var(--card-bg);
                border-radius: var(--border-radius);
                box-shadow: var(--card-shadow);
                overflow: hidden;
                border: none;
            }

            .toolbar {
                padding: 1.5rem;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            @media (min-width: 768px) {
                .toolbar {
                    flex-direction: row;
                    justify-content: space-between;
                    align-items: center;
                }
            }

            .search-box {
                position: relative;
                width: 100%;
                max-width: 400px;
            }

            .search-input {
                width: 100%;
                padding: 0.75rem 1rem 0.75rem 2.8rem;
                border: 1px solid #e0e0e0;
                border-radius: 10px;
                font-size: 0.95rem;
                transition: var(--transition);
                font-family: 'Inter', sans-serif;
            }

            .search-input:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            }

            .search-icon {
                position: absolute;
                left: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: var(--text-muted);
            }

            /* Table */
            .table-modern {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
            }

            .table-modern th {
                background: #fcfcfc;
                padding: 1rem 1.5rem;
                font-size: 0.8rem;
                font-weight: 700;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border-bottom: 1px solid #eee;
                white-space: nowrap;
            }

            .table-modern td {
                padding: 1.25rem 1.5rem;
                vertical-align: middle;
                border-bottom: 1px solid #f5f5f5;
                font-size: 0.95rem;
                transition: background 0.2s;
            }

            .table-modern tr:hover td {
                background-color: #fcfdff;
            }

            .table-modern tr:last-child td {
                border-bottom: none;
            }

            /* Custom Badges */
            .badge-custom {
                padding: 0.4em 0.8em;
                font-size: 0.75rem;
                font-weight: 700;
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                letter-spacing: 0.3px;
            }

            .badge-naissance {
                background: #e3f2fd;
                color: #1565c0;
            }

            .badge-deces {
                background: #f3e5f5;
                color: #7b1fa2;
            }

            .badge-mariage {
                background: #fce4ec;
                color: #c2185b;
            }

            .badge-status-success {
                background: rgba(0, 184, 148, 0.1);
                color: #00b894;
            }

            .badge-status-pending {
                background: rgba(255, 159, 67, 0.1);
                color: #ff9f43;
            }

            /* Action Button */
            .btn-validate {
                background: var(--primary);
                color: white;
                border: none;
                padding: 0.6rem 1.2rem;
                border-radius: 10px;
                font-size: 0.9rem;
                font-weight: 600;
                cursor: pointer;
                transition: var(--transition);
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
            }

            .btn-validate:hover {
                background: var(--primary-hover);
                transform: translateY(-2px);
                box-shadow: 0 6px 12px rgba(67, 97, 238, 0.3);
            }

            .btn-done {
                background: transparent;
                color: var(--success);
                border: 1px solid rgba(0, 184, 148, 0.2);
                padding: 0.5rem 1rem;
                border-radius: 10px;
                font-weight: 600;
                cursor: default;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
            }

            /* Empty state */
            .empty-state {
                text-align: center;
                padding: 4rem 1rem;
                color: var(--text-muted);
            }

            .empty-icon {
                font-size: 3rem;
                color: #e0e0e0;
                margin-bottom: 1rem;
            }
        </style>
    </head>

    <body>

        <div class="page-header">
            <div>
                <h1 class="page-title">Demandes en Ligne</h1>
                <div class="page-subtitle">
                    <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('D MMMM YYYY') }}
                </div>
            </div>

            <!-- Statistiques Rapides -->
            <!-- Cette div peut être utilisée pour des actions globales si besoin -->
        </div>

        <!-- Cards Stats -->
        <div class="stats-grid">
            <!-- Solde -->
            <div class="stat-card" style="border-left: 5px solid #ffa502;">
                <div class="stat-icon" style="background: #fff0d4; color: #ffa502;">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Solde Disponible</span>
                    <span class="stat-value">{{ number_format($montantRestant ?? 0, 0, ',', ' ') }} <small
                            style="font-size:0.8rem; font-weight:600;">FCFA</small></span>
                </div>
            </div>

            <!-- Total Demandes -->
            <!-- <div class="stat-card">
                <div class="stat-icon" style="background: #e3f2fd; color: #4361ee;">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Demandes en cours</span>
                    <span class="stat-value">{{ $allDemandes->count() }}</span>
                </div>
            </div> -->

            <!-- En attente -->
            <div class="stat-card">
                <div class="stat-icon" style="background: #e3f2fd; color: #4361ee;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Timbre Non Récupéré</span>
                    <span class="stat-value">{{ $allDemandes->where('timbre_recupere', 0)->count() }}</span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert-modern alert-success"
                style="background: #d1e7dd; color: #0f5132; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid #badbcc;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert-modern alert-error"
                style="background: #f8d7da; color: #842029; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid #f5c2c7;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="card-modern">

            <div class="toolbar">
                <h2 style="font-size: 1.2rem; font-weight: 700; margin: 0; color: var(--text-main);">Liste des Livraisons
                </h2>

                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Rechercher (Nom, contact, Ref, Type, )...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-modern" id="demandesTable">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Type de Demande</th>
                            <th style="text-align: center;">Demandeur</th>
                            <th style="text-align: center;">Date & Heure</th>
                            <th style="text-align: center;">Nombre de Timbre</th>
                            <th style="text-align: center;">État Timbre</th>
                            <th style="text-align: center;">Action requise</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allDemandes as $demande)
                            <tr>
                                <td style="text-align: center;">
                                    @if($demande->type_demande == 'naissance')
                                        <span class="badge-custom badge-naissance"><i class="fas fa-baby"></i> Naissance</span>
                                    @elseif($demande->type_demande == 'deces')
                                        <span class="badge-custom badge-deces"><i class="fas fa-cross"></i> Décès</span>
                                    @elseif($demande->type_demande == 'mariage')
                                        <span class="badge-custom badge-mariage"><i class="fas fa-heart"></i> Mariage</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div style="font-weight: 600; color: var(--text-main);">{{ $demande->demandeur_nom }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $demande->contact }}</div>
                                </td>
                                <td style="text-align: center;">
                                    <div style="font-weight: 500;">
                                        {{ \Carbon\Carbon::parse($demande->created_at)->locale('fr')->isoFormat('D MMMM YYYY') }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        {{ \Carbon\Carbon::parse($demande->created_at)->format('H:i') }}</div>
                                </td>
                                <td style="text-align: center;">
                                    <div style="font-weight: 500;">
                                        {{ $demande->quantite ?? '1' }}</div>
                                </td>
                                <td style="text-align: center;">
                                    @if($demande->timbre_recupere)
                                        <span class="badge-custom badge-status-success"><i class="fas fa-check-circle"></i>
                                            Récupéré</span>
                                    @else
                                        <span class="badge-custom badge-status-pending"><i class="fas fa-history"></i> En
                                            attente</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if(!$demande->timbre_recupere)
                                        <form
                                            action="{{ route('comptable.demandes.recover', ['type' => $demande->type_demande, 'id' => $demande->id]) }}"
                                            method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn-validate">
                                                <span>Valider Sortie</span>
                                                <i class="fas fa-arrow-right"></i>
                                            </button>
                                        </form>
                                    @else
                                        <div class="btn-done">
                                            <i class="fas fa-check-double"></i> Traité
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="fas fa-inbox empty-icon"></i>
                                    <h3>Aucune demande récente</h3>
                                    <p>Toutes les demandes de livraison ont été traitées.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Recherche en temps réel
            document.getElementById('searchInput').addEventListener('keyup', function () {
                let filter = this.value.toUpperCase();
                let table = document.getElementById('demandesTable');
                let tr = table.getElementsByTagName('tr');

                for (let i = 1; i < tr.length; i++) {
                    let txtValue = tr[i].textContent || tr[i].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            });

            // Interception des formulaires de validation
            document.addEventListener('DOMContentLoaded', function () {
                var forms = document.querySelectorAll('form[action*="/demandes-en-ligne/recover/"]');
                forms.forEach(function (form) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault(); // Empêcher l'envoi immédiat

                        Swal.fire({
                            title: 'Confirmer la sortie ?',
                            text: "Le timbre sera marqué comme récupéré et le solde sera débité.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#4361ee',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Oui, valider !',
                            cancelButtonText: 'Annuler',
                            reverseButtons: true,
                            focusConfirm: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit(); // Soumettre le formulaire si confirmé
                            }
                        });
                    });
                });
            });
        </script>
    </body>

    </html>
@endsection