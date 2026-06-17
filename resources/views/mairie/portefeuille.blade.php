@extends('mairie.layouts.template')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('dasboard/mairie.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .portefeuille-page {
            background: #f8fafc;
            min-height: 100vh;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary: #1f4083;
            --accent-gold: #d4af37;
            --accent-violet: #8a2be2;
            --success: #2ec4b6;
            --danger: #e71d36;
            --dark-bg: #0b132b;
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
        }

        /* Container and global animations */
        .animate-up {
            animation: fadeInUp 0.8s cubic-bezier(0.25, 0.8, 0.25, 1) both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Premium Glassmorphic Card */
        .glass-card {
            background: linear-gradient(135deg, rgba(31, 64, 131, 0.85), rgba(13, 110, 253, 0.7));
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            color: white;
            position: relative;
            overflow: hidden;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease;
        }

        .glass-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 45px rgba(13, 110, 253, 0.35);
        }

        /* Reflective shine effect on card hover */
        .glass-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: -150%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transform: skewX(-25deg);
            transition: 0.75s ease;
        }

        .glass-card:hover::after {
            left: 150%;
        }

        /* RFID / SIM Chip styling */
        .card-chip {
            width: 48px;
            height: 38px;
            background: linear-gradient(135deg, #ffd700, #b8860b);
            border-radius: 6px;
            position: relative;
            box-shadow: inset 0 1px 3px rgba(255, 255, 255, 0.5);
        }

        .card-chip::before {
            content: '';
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 4px;
        }

        .card-number {
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 3px;
            font-size: 1.25rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .gold-badge {
            background: linear-gradient(135deg, var(--accent-gold), #b8860b);
            color: #000;
            font-weight: 700;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
        }

        /* Statistics Card */
        .stat-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
        }

        /* Glow Button */
        .glow-btn {
            background: linear-gradient(135deg, var(--accent-gold), #b8860b);
            border: none;
            color: #000 !important;
            font-weight: 700;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
        }

        .glow-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.6);
        }

        .glow-btn:disabled {
            background: #e2e8f0 !important;
            color: #94a3b8 !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
            cursor: not-allowed;
            transform: none !important;
            pointer-events: none;
        }

        /* Unified Transactions Feed */
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .badge-credit {
            background-color: rgba(46, 196, 182, 0.15) !important;
            color: var(--success) !important;
            font-weight: 600;
        }

        .badge-debit {
            background-color: rgba(231, 29, 54, 0.12) !important;
            color: var(--danger) !important;
            font-weight: 600;
        }

        .badge-pending {
            background-color: rgba(255, 159, 67, 0.15) !important;
            color: #ff9f43 !important;
            font-weight: 600;
        }

        /* Confetti canvas */
        #confetti-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 9999;
            display: none;
        }

        /* Modal Transitions */
        .modal.fade .modal-dialog {
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }
    </style>
    <div class="portefeuille-page">
        <canvas id="confetti-canvas"></canvas>

        <div class="container-fluid py-4">
            <!-- Header avec le titre de la section -->
            <div class="d-flex justify-content-between align-items-center mb-4 animate-up" style="animation-delay: 0.1s;">
                <div>
                    <h2 class="fw-bold text-dark mb-1"><i class="fas fa-wallet text-primary me-2"></i>Portefeuille en Ligne
                    </h2>
                    <p class="text-muted mb-0">Gestion et reversement sécurisé des timbres perçus électroniquement.</p>
                </div>
                <div class="gold-badge">
                    <i class="fas fa-shield-halved me-1"></i> TrésorPay E-Wallet
                </div>
            </div>

            <div class="row g-4 mb-4">
                <!-- La carte Bancaire Virtuelle Premium -->
                <div class="col-lg-5 animate-up" style="animation-delay: 0.2s;">
                    <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="card-chip"></div>
                                <span class="fw-bold text-uppercase tracking-wider small">E-TIMBRE COLLECTE</span>
                            </div>
                            <h6 class="text-white-50 text-uppercase small mb-1">CUMUL DU MOIS EN COURS</h6>
                            <h1 class="display-5 fw-bold mb-3" style="font-feature-settings: 'tnum';">
                                <span id="wallet-balance" data-target="{{ $soldePortefeuille }}">0</span> <span
                                    class="fs-4">FCFA</span>
                            </h1>
                        </div>
                        <div>
                            <div class="mb-4">
                                <p class="card-number mb-0">TP-PLATEAU-2026-8492</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-end">
                                <div>
                                    <small class="text-white-50 d-block small">MAIRIE DU</small>
                                    <span class="fw-bold text-uppercase">{{ $mairie->name }}</span>
                                </div>
                                <img src="{{ asset('assets/assets/img/logo plateau.png') }}" alt="Logo Plateau"
                                    style="height: 38px; filter: brightness(0) invert(1);">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'Action & Synthèse des données -->
                <div class="col-lg-7 animate-up" style="animation-delay: 0.3s;">
                    <div class="card stat-card p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-vault me-2 text-primary"></i>Synthèse du
                                Portefeuille</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 bg-light">
                                        <small class="text-muted d-block text-uppercase small">Total timbres perçus</small>
                                        <h4 class="fw-bold text-dark mb-0">
                                            {{ number_format($totalPerçuEnLigne, 0, ',', ' ') }} FCFA</h4>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 bg-light">
                                        <small class="text-muted d-block text-uppercase small">Total timbres transférés
                                            TrésorPay</small>
                                        <h4 class="fw-bold text-success mb-0">
                                            {{ number_format($totalReversements, 0, ',', ' ') }} FCFA</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 alert alert-info border-0 rounded-3 d-flex align-items-center">
                                <i class="fas fa-circle-info fa-2x me-3 text-info"></i>
                                <span class="small">
                                    Ce tableau de bord comptabilise en temps réel les montants des timbres collectés en
                                    ligne. Avec le système de paiement direct, chaque fois qu'un citoyen effectue un
                                    paiement de timbre par Wave, la somme est immédiatement et automatiquement créditée sur
                                    le compte de TrésorPay.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Les statistiques détaillées par type d'acte -->
                <div class="row g-4 mb-4 animate-up" style="animation-delay: 0.4s;">
                    <div class="col-md-4">
                        <div class="card stat-card p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block text-uppercase small">Timbres Actes Naissance</small>
                                    <h3 class="fw-bold text-dark mt-1 mb-0">
                                        {{ number_format($totalNaissance, 0, ',', ' ') }} XOF</h3>
                                </div>
                                <div class="p-3 rounded-circle"
                                    style="background-color: rgba(31, 64, 131, 0.1); color: var(--primary);">
                                    <i class="fas fa-baby fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block text-uppercase small">Timbres Actes Mariage</small>
                                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalMariage, 0, ',', ' ') }}
                                        XOF</h3>
                                </div>
                                <div class="p-3 rounded-circle"
                                    style="background-color: rgba(138, 43, 226, 0.1); color: var(--accent-violet);">
                                    <i class="fas fa-ring fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block text-uppercase small">Timbres Actes Décès</small>
                                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalDeces, 0, ',', ' ') }}
                                        XOF</h3>
                                </div>
                                <div class="p-3 rounded-circle"
                                    style="background-color: rgba(231, 29, 54, 0.1); color: var(--danger);">
                                    <i class="fas fa-spa fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historique à gauche & Comptabilisation Mensuelle à droite -->
                <div class="row g-4 animate-up" style="animation-delay: 0.5s;">
                    <!-- Historique unifié des Transactions -->
                    <div class="col-lg-8">
                        <div class="table-container h-100">
                            <div class="table-header p-4 d-flex justify-content-between align-items-center"
                                style="background: linear-gradient(120deg, var(--primary), #0d6efd); color: white;">
                                <h5 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i>Historique des Transferts</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('mairie.portefeuille.historique') }}"
                                        class="btn btn-sm btn-light text-primary fw-bold rounded-pill px-3 me-2">
                                        <i class="fas fa-expand me-1"></i> Voir tout l'historique
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-center">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-3 text-secondary text-center">DATE & HEURE</th>
                                            <th class="py-3 text-secondary text-center">RÉFÉRENCE</th>
                                            <th class="py-3 text-secondary text-center">DESTINATAIRE (TRÉSORPAY)</th>
                                            <th class="py-3 text-secondary text-center">MONTANT</th>
                                            <th class="py-3 text-secondary text-center">STATUT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $t)
                                            <tr>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <div class="p-2 bg-light rounded-3 me-2">
                                                            <i class="fas fa-calendar-days text-muted"></i>
                                                        </div>
                                                        <div class="text-center">
                                                            <span
                                                                class="fw-bold text-dark d-block">{{ $t->date->format('d M Y') }}</span>
                                                            <small
                                                                class="text-muted">{{ $t->date->format('H:i') }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <code class="text-secondary fw-semibold">{{ $t->reference }}</code>
                                                </td>
                                                <td class="text-center">
                                                    <span class="small fw-bold">{{ $t->destinataire }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="fw-bold text-success">
                                                        {{ number_format($t->montant, 0, ',', ' ') }} FCFA
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge rounded-pill px-3 py-2 badge-credit">
                                                        <i class="fas fa-circle-check me-1"></i> {{ $t->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-5 text-center">
                                                    <div class="py-4 text-center">
                                                        <i class="fas fa-receipt fa-3x text-light mb-3"></i>
                                                        <h5 class="text-muted">Aucun transfert effectué</h5>
                                                        <p class="text-muted small mb-0">Les transferts vers TrésorPay
                                                            s'afficheront ici en temps réel au fil des paiements des
                                                            citoyens.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Comptabilisation Mensuelle TrésorPay -->
                    <div class="col-lg-4">
                        <div class="card stat-card h-100 p-0 overflow-hidden"
                            style="border: none; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05); border-radius: 15px;">
                            <div class="p-4"
                                style="background: linear-gradient(120deg, #d4af37, #b8860b); color: black;">
                                <h5 class="mb-0 fw-bold"><i class="fas fa-chart-bar me-2"></i>Reçu par TrésorPay
                                    ({{ $currentYear }})</h5>
                                <p class="mb-0 small text-black-50 fw-bold">Comptabilisation mensuelle automatique</p>
                            </div>
                            <div class="p-3" style="max-height: 420px; overflow-y: auto;">
                                <ul class="list-group list-group-flush">
                                    @foreach ($comptabiliteMensuelle as $num => $m)
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 border-bottom border-light">
                                            <div class="d-flex align-items-center">
                                                <div class="p-2 bg-light rounded-circle me-3 text-center d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px;">
                                                    <span class="fw-bold text-muted small"
                                                        style="font-size: 0.8rem;">{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}</span>
                                                </div>
                                                <span class="fw-bold text-dark"
                                                    style="font-size: 0.9rem;">{{ $m['nom'] }}</span>
                                            </div>
                                            <span
                                                class="badge bg-success-light text-success rounded-pill px-3 py-2 fw-bold"
                                                style="font-size: 0.85rem; background-color: rgba(46, 196, 182, 0.12);">
                                                {{ number_format($m['montant'], 0, ',', ' ') }} FCFA
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL INTERACTIF DE REVERSEMENT TRESORPAY -->
            <div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 shadow">
                        <div class="modal-header border-0 pb-0"
                            style="background: linear-gradient(120deg, var(--primary), #0d6efd); color: white; border-radius: 12px 12px 0 0;">
                            <h5 class="modal-title fw-bold" id="transferModalLabel"><i
                                    class="fas fa-paper-plane me-2"></i>Reversement TrésorPay</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div id="transfer-form-container">
                                <form id="remittance-form">
                                    <div class="mb-3">
                                        <label class="form-label text-uppercase text-muted fw-bold small">Montant à
                                            reverser (FCFA)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i
                                                    class="fas fa-money-bill-wave text-success"></i></span>
                                            <input type="number" name="montant" id="remittance-amount"
                                                class="form-control bg-light border-0 py-3 fw-bold text-dark"
                                                max="{{ $soldePortefeuille }}" min="1"
                                                value="{{ $soldePortefeuille }}" required>
                                        </div>
                                        <div class="form-text text-end">Solde max disponible :
                                            <strong>{{ number_format($soldePortefeuille, 0, ',', ' ') }} XOF</strong></div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label text-uppercase text-muted fw-bold small">Identifiant
                                            Credential TrésorPay</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i
                                                    class="fas fa-key text-muted"></i></span>
                                            <input type="text" name="credential_id"
                                                class="form-control bg-light border-0 py-3" value="gtvB04rzE_wkvb4S2"
                                                readonly required style="font-family: monospace;">
                                        </div>
                                        <div class="form-text">Ce compte partenaire recevra les recettes de timbres de la
                                            Mairie du Plateau.</div>
                                    </div>
                                    <div class="alert alert-warning border-0 small mb-4">
                                        <i class="fas fa-triangle-exclamation me-2 text-warning"></i>
                                        Cette transaction va initier le virement électronique vers le compte bancaire
                                        public. Cette action est irréversible.
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3"
                                        id="btn-confirm-remittance">
                                        <i class="fas fa-vault me-2"></i> Confirmer le virement
                                    </button>
                                </form>
                            </div>

                            <!-- Écran de chargement et succès animé -->
                            <div id="transfer-loading-container" class="text-center py-5" style="display: none;">
                                <div class="spinner-border text-primary" role="status"
                                    style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Traitement...</span>
                                </div>
                                <h5 class="fw-bold mt-4 text-dark">Connexion sécurisée TrésorPay...</h5>
                                <p class="text-muted small">Veuillez ne pas fermer cette fenêtre pendant le transfert.</p>
                            </div>

                            <div id="transfer-success-container" class="text-center py-4" style="display: none;">
                                <div class="p-3 bg-success-light text-success rounded-circle d-inline-flex justify-content-center align-items-center mb-3"
                                    style="width: 80px; height: 80px; background-color: rgba(46, 196, 182, 0.15);">
                                    <i class="fas fa-circle-check fa-4x animate__animated animate__bounceIn"></i>
                                </div>
                                <h4 class="fw-bold text-dark">Transfert Réussi !</h4>
                                <p class="text-muted small px-3">Les fonds de timbres en ligne ont été reversés avec succès
                                    vers le Trésor Public.</p>
                                <div class="p-3 bg-light border rounded-3 mb-4 mx-3">
                                    <span class="small text-muted d-block">Montant Transféré</span>
                                    <h3 class="fw-bold text-success mb-0" id="success-transferred-amount">0 FCFA</h3>
                                </div>
                                <button type="button" class="btn btn-dark px-4 py-2" data-bs-dismiss="modal"
                                    id="btn-success-close">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // 1. Animation d'incrémentation progressive du solde
                    const balanceEl = document.getElementById('wallet-balance');
                    const targetValue = parseInt(balanceEl.getAttribute('data-target'), 10);

                    if (targetValue > 0) {
                        let start = 0;
                        const duration = 1200; // ms
                        const stepTime = 15;
                        const steps = duration / stepTime;
                        const increment = targetValue / steps;

                        const timer = setInterval(() => {
                            start += increment;
                            if (start >= targetValue) {
                                balanceEl.textContent = targetValue.toLocaleString('fr-FR');
                                clearInterval(timer);
                            } else {
                                balanceEl.textContent = Math.floor(start).toLocaleString('fr-FR');
                            }
                        }, stepTime);
                    } else {
                        balanceEl.textContent = '0';
                    }

                    // 2. Gestionnaire du Formulaire de Reversement
                    const remittanceForm = document.getElementById('remittance-form');
                    const formContainer = document.getElementById('transfer-form-container');
                    const loadingContainer = document.getElementById('transfer-loading-container');
                    const successContainer = document.getElementById('transfer-success-container');

                    remittanceForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        const amountInput = document.getElementById('remittance-amount');
                        const amount = parseFloat(amountInput.value);

                        if (amount <= 0 || isNaN(amount)) {
                            alert('Veuillez saisir un montant valide.');
                            return;
                        }

                        // Passer à l'écran de chargement
                        formContainer.style.display = 'none';
                        loadingContainer.style.display = 'block';

                        // Simuler un appel AJAX avec un délai de 2 secondes
                        const formData = new FormData(remittanceForm);

                        fetch("{{ route('mairie.portefeuille.reverser') }}", {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    setTimeout(() => {
                                        loadingContainer.style.display = 'none';
                                        successContainer.style.display = 'block';
                                        document.getElementById('success-transferred-amount')
                                            .textContent = amount.toLocaleString('fr-FR') + ' FCFA';

                                        // Déclencher les confettis
                                        startConfetti();
                                    }, 1800);
                                } else {
                                    alert(data.message || 'Une erreur est survenue.');
                                    formContainer.style.display = 'block';
                                    loadingContainer.style.display = 'none';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Une erreur de réseau est survenue.');
                                formContainer.style.display = 'block';
                                loadingContainer.style.display = 'none';
                            });
                    });

                    // Recharger la page à la fermeture du modal succès pour actualiser les soldes
                    document.getElementById('btn-success-close').addEventListener('click', function() {
                        window.location.reload();
                    });

                    document.getElementById('transferModal').addEventListener('hidden.bs.modal', function() {
                        if (successContainer.style.display === 'block') {
                            window.location.reload();
                        }
                    });

                    // 3. Script d'animation de Confettis Vanilla JS (Sans librairie externe)
                    const canvas = document.getElementById('confetti-canvas');
                    const ctx = canvas.getContext('2d');
                    let pieces = [];
                    let numberOfPieces = 150;
                    let active = false;

                    function updateCanvasSize() {
                        canvas.width = window.innerWidth;
                        canvas.height = window.innerHeight;
                    }

                    window.addEventListener('resize', updateCanvasSize);

                    function randomColor() {
                        const colors = ['#f4a261', '#e76f51', '#2a9d8f', '#e9c46a', '#457b9d', '#e63946', '#8a2be2',
                            '#ffd700'
                        ];
                        return colors[Math.floor(Math.random() * colors.length)];
                    }

                    class ConfettiPiece {
                        constructor() {
                            this.x = Math.random() * canvas.width;
                            this.y = Math.random() * canvas.height - canvas.height;
                            this.size = Math.random() * 8 + 6;
                            this.color = randomColor();
                            this.speed = Math.random() * 4 + 3;
                            this.rotation = Math.random() * 360;
                            this.rotationSpeed = Math.random() * 4 - 2;
                        }

                        update() {
                            this.y += this.speed;
                            this.rotation += this.rotationSpeed;
                            if (this.y > canvas.height) {
                                this.y = -20;
                                this.x = Math.random() * canvas.width;
                            }
                        }

                        draw() {
                            ctx.save();
                            ctx.translate(this.x, this.y);
                            ctx.rotate(this.rotation * Math.PI / 180);
                            ctx.fillStyle = this.color;
                            ctx.fillRect(-this.size / 2, -this.size / 2, this.size, this.size);
                            ctx.restore();
                        }
                    }

                    function startConfetti() {
                        updateCanvasSize();
                        canvas.style.display = 'block';
                        pieces = [];
                        for (let i = 0; i < numberOfPieces; i++) {
                            pieces.push(new ConfettiPiece());
                        }
                        active = true;
                        animateConfetti();

                        // Arrêter automatiquement après 5 secondes
                        setTimeout(() => {
                            active = false;
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            canvas.style.display = 'none';
                        }, 5000);
                    }

                    function animateConfetti() {
                        if (!active) return;
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        pieces.forEach(p => {
                            p.update();
                            p.draw();
                        });
                        requestAnimationFrame(animateConfetti);
                    }
                });
            </script>
        </div>
    @endsection
