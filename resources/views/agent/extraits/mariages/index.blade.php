@extends('agent.layouts.template')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <style>
        :root {
            --primary-color: #1f4083;
            --primary-light: #1f4083;
            --primary-dark: #1f4083;
            --secondary-color: #1f4083;
            --secondary-light: #1f4083;
            --secondary-dark: #1f4083;
            --light-color: #ffffff;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --light-gray: #f8f9fa;
            --border-radius: 12px;
            --box-shadow: 0 8px 20px rgba(0, 126, 0, 0.1);
            --transition: all 0.3s ease;
        }

        /* Fix pagination - alignement horizontal */
        .pagination {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap;
            align-items: center;
            list-style: none !important;
            padding-left: 0 !important;
            margin: 0 !important;
            gap: 4px;
        }

        .pagination .page-item {
            list-style: none !important;
            display: inline-block;
        }

        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border-radius: 50% !important;
            border: 1px solid #dee2e6;
            color: var(--secondary-color);
            background-color: white;
            font-size: 0.85rem;
            transition: var(--transition);
            text-decoration: none;
            cursor: pointer;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            pointer-events: none;
            background-color: #f8f9fa;
        }

        .pagination .page-link:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
        }

        /* Ajoute ceci dans le bloc <style> de chaque page */
        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 12px;
            border-radius: 20px !important;
            margin: 0 3px;
            border: 1px solid #dee2e6;
            color: var(--secondary-color);
            background-color: white;
            font-size: 0.85rem;
            transition: var(--transition);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            pointer-events: none;
            background-color: #f8f9fa;
        }

        .pagination .page-link:hover:not(.disabled) {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
        }

        body {
            background-color: var(--light-gray);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .dashboard-container {
            padding: 30px;
            max-width: 100%;
            margin: 0 auto;
        }

        .page-title {
            color: var(--secondary-color);
            font-weight: 700;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .page-actions {
            display: flex;
            gap: 10px;
        }

        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            border-left: 5px solid var(--primary-color);
        }

        .stats-icon {
            font-size: 2rem;
            margin-right: 15px;
            color: var(--primary-color);
            background: rgba(255, 136, 0, 0.1);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stats-content h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            color: var(--dark-color);
        }

        .stats-content p {
            margin: 0;
            color: var(--gray-color);
            font-weight: 500;
        }

        .dashboard-card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            margin-bottom: 30px;
            overflow: hidden;
            background-color: var(--light-color);
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(0, 126, 0, 0.15);
        }

        .card-header {
            background-color: var(--secondary-color);
            color: white;
            padding: 18px 25px;
            font-weight: 600;
            border-bottom: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .card-header h5 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .card-header i {
            font-size: 1.3rem;
            margin-right: 12px;
        }

        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box input {
            padding-left: 40px;
            border-radius: 20px;
            border: 1px solid #e0e8ff;
            box-shadow: none;
            height: 40px;
            transition: var(--transition);
        }

        .search-box input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 136, 0, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-color);
        }

        .table-responsive {
            border-radius: var(--border-radius);
            overflow-x: auto;
        }

        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.85rem;
            min-width: 1100px;
        }

        .table thead th {
            background-color: rgba(0, 126, 0, 0.05);
            color: var(--secondary-color);
            font-weight: 600;
            border: none;
            padding: 10px 8px;
            vertical-align: middle;
            border-bottom: 2px solid rgba(0, 126, 0, 0.1);
            white-space: nowrap;
            font-size: 0.8rem;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background-color: rgba(0, 126, 0, 0.03);
        }

        .table tbody td {
            padding: 10px 8px;
            vertical-align: middle;
            border-top: 1px solid rgba(0, 126, 0, 0.05);
            font-size: 0.8rem;
        }

        .badge-status {
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .badge-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-progress {
            background-color: #cce5ff;
            color: #004085;
        }

        .badge-completed {
            background-color: rgba(0, 126, 0, 0.1);
            color: var(--secondary-color);
        }

        .btn-action {
            background-color: var(--primary-color);
            border: none;
            border-radius: 15px;
            padding: 5px 10px;
            font-size: 0.75rem;
            font-weight: 500;
            transition: var(--transition);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-action:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 136, 0, 0.2);
            color: white;
        }

        .btn-action:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
        }

        .btn-secondary:hover {
            background-color: var(--secondary-dark);
            box-shadow: 0 4px 8px rgba(0, 126, 0, 0.2);
        }

        .btn-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 2px;
            padding: 0;
        }

        .badge {
            padding: 4px 8px;
            color: white;
            border-radius: 50px;
            background-color: #1f4083;
            font-size: 0.7rem;
            white-space: nowrap;
        }

        .empty-state {
            text-align: center;
            padding: 40px 0;
            color: var(--gray-color);
        }

        .empty-state i {
            font-size: 50px;
            margin-bottom: 15px;
            color: #dee2e6;
        }

        .empty-state h5 {
            font-weight: 500;
            color: var(--gray-color);
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.7rem;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .user-avatar1 {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.7rem;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .user-details {
            line-height: 1.1;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.75rem;
            white-space: nowrap;
        }

        .user-email {
            font-size: 0.7rem;
            color: var(--gray-color);
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination .page-item .page-link {
            color: var(--secondary-color);
            border: 1px solid #dee2e6;
            padding: 8px 16px;
            border-radius: 20px;
            margin: 0 3px;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
        }

        /* Styles pour le bouton de téléchargement */
        .download-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .download-btn:hover {
            background-color: var(--secondary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 126, 0, 0.2);
            color: white;
        }

        /* Styles pour les aperçus de documents */
        .document-preview {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid #eee;
            margin: 2px;
        }

        .document-preview:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .document-container {
            display: flex;
            justify-content: center;
            gap: 5px;
            flex-wrap: wrap;
        }

        /* === Modern Popup Styles === */
        @keyframes popupIn {
            from {
                opacity: 0;
                transform: scale(0.96) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .request-details-popup {
            border-radius: 20px !important;
            overflow: hidden !important;
            padding: 0 !important;
        }

        .request-details-popup .swal2-html-container {
            max-height: 72vh;
            overflow-y: auto;
            padding: 0 !important;
            margin: 0 !important;
            scrollbar-width: thin;
            scrollbar-color: #1f408344 transparent;
        }

        .request-details-popup .swal2-html-container::-webkit-scrollbar {
            width: 5px;
        }

        .request-details-popup .swal2-html-container::-webkit-scrollbar-thumb {
            background: #1f408355;
            border-radius: 4px;
        }

        .request-details-popup .swal2-title {
            display: none !important;
        }

        .request-details-popup .swal2-actions {
            padding: 12px 20px 20px;
            margin: 0;
        }

        .request-details-popup .swal2-close {
            color: rgba(255, 255, 255, 0.85) !important;
            font-size: 18px !important;
            top: 12px !important;
            right: 12px !important;
        }

        .request-details-popup .swal2-close:hover {
            color: white !important;
        }

        .image-modal-popup {
            border-radius: 16px !important;
        }

        .image-modal-popup .swal2-close {
            color: #555 !important;
            font-size: 22px !important;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 15px;
            }

            .page-title {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-header {
                padding: 15px;
                flex-direction: column;
                align-items: flex-start;
            }

            .card-header h5 {
                font-size: 1.1rem;
            }

            .table thead {
                display: none;
            }

            .table tbody tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid rgba(0, 126, 0, 0.1);
                border-radius: var(--border-radius);
                padding: 10px;
            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 15px;
                border: none;
                border-bottom: 1px solid rgba(0, 126, 0, 0.05);
            }

            .table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--secondary-color);
                margin-right: 15px;
            }

            .table tbody td:last-child {
                border-bottom: none;
                justify-content: center;
            }

            .user-info {
                justify-content: space-between;
                width: 100%;
            }
        }
    </style>

    <div class="dashboard-container">
        <!-- Notifications -->
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

        <div class="page-title">
            <h2>
                <i class="fas fa-ring me-2"></i>Gestion des demandes d'acte de mariage
            </h2>
            <div class="page-actions">
                <a href="{{ route('agent.dashboard') }}" class="btn-action">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
            <div class="stats-card" style="flex: 1; min-width: 250px;">
                <div class="stats-icon"><i class="fas fa-tasks"></i></div>
                <div class="stats-content">
                    <h3>{{ $mariages->total() }}</h3>
                    <p>Demandes en cours</p>
                </div>
            </div>
        </div>

        <!-- Tableau des demandes -->
        <div class="dashboard-card">
            <div class="card-header">
                <h5><i class="fas fa-list me-2"></i>Liste des demandes</h5>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="text-align: center">Infos Livraison</th>
                                <th class="text-center">Quantité</th>
                                <th class="text-center">Demandeur</th>
                                <th class="text-center">Conjoint(e)</th>
                                <th class="text-center">Document</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Date demande</th>
                                <th class="text-center">État</th>
                                <th class="text-center">Actions</th>
                                <th class="text-center">Retrait</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mariages as $mariage)
                                <tr>
                                    <td style="text-align: center">
                                        @if ($mariage->choix_option == 'livraison' && $mariage->etat == 'terminé')
                                            <button class="download-btn" onclick="imprimerEtiquette({{ $mariage->id }})">
                                                <i class="fas fa-print me-1"></i>Imprimer
                                            </button>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-center fw-bold">{{ $mariage->quantite }} copie(s)</div>
                                        @if ($mariage->qty_simple > 0 && $mariage->qty_integral > 0)
                                            <div class="text-center mt-1" style="font-size: 0.75rem;">
                                                <span class="text-info">{{ $mariage->qty_simple }} Simple</span><br>
                                                <span class="text-primary">{{ $mariage->qty_integral }} Intégrale</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td style="text-align: center" data-label="Demandeur">
                                        <div class="user-info"
                                            style="text-align: center; display:flex; justify-content:center">
                                            <div class="user-avatar" style="text-align: center">
                                                {{ substr($mariage->user->name, 0, 1) . '' . substr($mariage->user->prenom, 0, 1) }}
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name">
                                                    {{ $mariage->user->name . ' ' . $mariage->user->prenom ?? '--' }} </div>
                                                <div class="user-email">{{ $mariage->user->contact ?? '--' }}</div>
                                                <div class="user-email">{{ $mariage->user->email ?? '--' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center" data-label="Conjoint(e)">
                                        <div class="user-info"
                                            style="text-align: center; display:flex; justify-content:center">
                                            <div class="user-details">
                                                <div class="user-name">
                                                    {{ $mariage->nomEpoux == null ? 'Copie Simple' : $mariage->nomEpoux }}
                                                </div>
                                                <div class="user-name">
                                                    {{ $mariage->prenomEpoux == null ? 'Copie Simple' : $mariage->prenomEpoux }}
                                                </div>
                                                <div class="user-email">
                                                    {{ $mariage->dateNaissanceEpoux == null ? 'Copie Simple' : $mariage->dateNaissanceEpoux }}
                                                </div>
                                                <div class="user-email">
                                                    {{ $mariage->lieuNaissanceEpoux == null ? 'Copie Simple' : $mariage->lieuNaissanceEpoux }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            @if ($mariage->pieceIdentite)
                                                @php
                                                    $piecePath = asset('storage/' . $mariage->pieceIdentite);
                                                    $isPiecePdf =
                                                        strtolower(pathinfo($piecePath, PATHINFO_EXTENSION)) === 'pdf';
                                                @endphp
                                                @if ($isPiecePdf)
                                                    <a href="{{ $piecePath }}" target="_blank"
                                                        title="Pièce d'identité (PDF)">
                                                        <img src="{{ asset('assets/assets/img/pdf.jpg') }}" alt="PDF"
                                                            class="document-preview">
                                                    </a>
                                                @else
                                                    <img src="{{ $piecePath }}" alt="Pièce d'identité"
                                                        class="document-preview"
                                                        onclick="openImageModal('{{ $piecePath }}')"
                                                        title="Pièce d'identité">
                                                @endif
                                            @endif

                                            @if ($mariage->extraitMariage)
                                                @php
                                                    $extraitPath = asset('storage/' . $mariage->extraitMariage);
                                                    $isExtraitPdf =
                                                        strtolower(pathinfo($extraitPath, PATHINFO_EXTENSION)) ===
                                                        'pdf';
                                                @endphp
                                                @if ($isExtraitPdf)
                                                    <a href="{{ $extraitPath }}" target="_blank" title="Extrait (PDF)">
                                                        <img src="{{ asset('assets/assets/img/pdf.jpg') }}" alt="PDF"
                                                            class="document-preview">
                                                    </a>
                                                @else
                                                    <img src="{{ $extraitPath }}" alt="Extrait de mariage"
                                                        class="document-preview"
                                                        onclick="openImageModal('{{ $extraitPath }}')"
                                                        title="Extrait de mariage">
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td style="text-align: center">
                                        @if ($mariage->type == 'simple')
                                            <span class="badge bg-info text-white">Copie Simple</span>
                                        @elseif($mariage->type == 'groupee')
                                            <span class="badge bg-success text-white">Simple + Intégral</span>
                                        @elseif($mariage->type == 'integrale')
                                            <span class="badge bg-primary text-white">Copie Intégrale</span>
                                        @else
                                            <span
                                                class="badge bg-secondary text-white">{{ ucfirst($mariage->type ?: 'Simple') }}</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center" data-label="Date demande">
                                        {{ $mariage->created_at->format('d/m/Y H:i') }}</td>
                                    <td style="text-align: center" data-label="Statut">
                                        @if ($mariage->etat == 'en attente')
                                            <span class="badge-status badge-pending">En attente</span>
                                        @elseif($mariage->etat == 'réçu')
                                            <span class="badge-status badge-progress">En cours</span>
                                        @else
                                            <span class="badge-status badge-completed">Terminé</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center" data-label="Actions">
                                        <!-- Bouton pour voir les détails -->
                                        <button class="btn-action btn-icon"
                                            style="background-color: #17a2b8; cursor: pointer;"
                                            data-task="{{ json_encode($mariage) }}" onclick="showRequestDetails(this)"
                                            title="Voir les détails de la demande">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if ($mariage->etat === 'terminé')
                                            <a href="#" class="btn-action btn-secondary btn-icon disabled"
                                                title="Demande terminée"
                                                style="opacity: 0.5; pointer-events: none; background-color: #6c757d;">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('agent.demandes.wedding.edit', $mariage->id) }}"
                                                class="btn-action btn-secondary btn-icon"
                                                title="Modifier l'état de la demande">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td style="text-align: center">
                                        <div class="d-flex justify-content-center gap-2">
                                            @if ($mariage->choix_option == 'livraison')
                                                <a href="#" class="delivery-badge badge" data-bs-toggle="modal"
                                                    data-bs-target="#livraisonModal"
                                                    onclick="showLivraisonModal({{ json_encode($mariage) }})">
                                                    <i class="fas fa-truck"></i> Livraison
                                                </a>
                                            @else
                                                @if ($mariage->etat !== 'terminé')
                                                    <span class="badge"><i class="fas fa-home"></i> Retrait sur
                                                        place</span>
                                                @endif
                                                @if ($mariage->etat == 'terminé')
                                                    <button class="btn-action"
                                                        onclick="markAsDelivered({{ $mariage->id }})"
                                                        title="Livré l'extrait">
                                                        <i class="fas fa-file"></i>Retrait
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td style="text-align: center" colspan="10" class="empty-state">
                                        <i class="fas fa-cross"></i>
                                        <h5>Aucune demande d'extrait de mariage en cours</h5>
                                        <p>Toutes les demandes sont traitées ou vous n'avez pas encore récuperer demande.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($mariages->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Affichage de <strong>{{ $mariages->firstItem() }}</strong> à
                            <strong>{{ $mariages->lastItem() }}</strong> sur <strong>{{ $mariages->total() }}</strong>
                            résultats
                        </div>

                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0">
                                <!-- Première page -->
                                @if ($mariages->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-angle-double-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $mariages->url(1) }}" aria-label="Première page">
                                            <i class="fas fa-angle-double-left"></i>
                                        </a>
                                    </li>
                                @endif

                                <!-- Page précédente -->
                                @if ($mariages->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $mariages->previousPageUrl() }}"
                                            aria-label="Précédent">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                <!-- Pages numérotées -->
                                @php
                                    $current = $mariages->currentPage();
                                    $last = $mariages->lastPage();
                                    $start = max($current - 2, 1);
                                    $end = min($current + 2, $last);

                                    if ($start > 1) {
                                        $start = max($current - 1, 1);
                                        $end = min($current + 1, $last);
                                    }

                                    if ($end - $start < 2) {
                                        $start = max($current - 2, 1);
                                        $end = min($current + 2, $last);
                                    }
                                @endphp

                                @if ($start > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $mariages->url(1) }}">1</a>
                                    </li>
                                    @if ($start > 2)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                @endif

                                @for ($i = $start; $i <= $end; $i++)
                                    <li class="page-item {{ $i == $current ? 'active' : '' }}">
                                        @if ($i == $current)
                                            <span class="page-link">{{ $i }}</span>
                                        @else
                                            <a class="page-link" href="{{ $mariages->url($i) }}">{{ $i }}</a>
                                        @endif
                                    </li>
                                @endfor

                                @if ($end < $last)
                                    @if ($end < $last - 1)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $mariages->url($last) }}">{{ $last }}</a>
                                    </li>
                                @endif

                                <!-- Page suivante -->
                                @if ($mariages->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $mariages->nextPageUrl() }}" aria-label="Suivant">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif

                                <!-- Dernière page -->
                                @if ($mariages->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $mariages->url($last) }}"
                                            aria-label="Dernière page">
                                            <i class="fas fa-angle-double-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-angle-double-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Recherche dans le tableau
            $('#searchInput').on('keyup', function() {
                const value = $(this).val().toLowerCase();
                $('table tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Adaptation pour mobile
            function adaptForMobile() {
                if (window.innerWidth <= 768) {
                    // Ajout des data-labels pour l'affichage mobile
                    $('table thead th').each(function() {
                        const headerText = $(this).text();
                        const columnIndex = $(this).index();
                        $('table tbody tr td:nth-child(' + (columnIndex + 1) + ')').attr('data-label',
                            headerText);
                    });
                }
            }

            // Exécuter au chargement et lors du redimensionnement
            adaptForMobile();
            $(window).resize(adaptForMobile);
        });
    </script>

    <script>
        const markAsDeliveredUrl = "{{ route('livraison.mark.mariage', ':id') }}";
        const downloadDeliveryInfoUrl = "{{ route('agent.download.mariage.delivery.info', ':id') }}";

        // Fonction pour afficher tous les détails de la demande
        function showRequestDetails(mariageOrElement) {
            let mariage = mariageOrElement;
            if (mariageOrElement && (mariageOrElement.dataset || (mariageOrElement.getAttribute && mariageOrElement
                    .getAttribute('data-task')))) {
                const raw = mariageOrElement.dataset.task || mariageOrElement.getAttribute('data-task');
                if (raw) {
                    try {
                        mariage = typeof raw === 'string' ? JSON.parse(raw) : raw;
                    } catch (e) {
                        console.error("Error parsing mariage JSON:", e);
                    }
                }
            } else if (typeof mariageOrElement === 'string') {
                try {
                    mariage = JSON.parse(mariageOrElement);
                } catch (e) {}
            }
            const user = (mariage && mariage.user) || {};
            const isCopieSimple = mariage.nomEpoux === null;
            let documentType = '';
            const qtySimple = parseInt(mariage.qty_simple) || 0;
            const qtyIntegral = parseInt(mariage.qty_integral) || 0;

            if (qtySimple > 0 && qtyIntegral > 0) {
                documentType =
                    `${qtySimple} copie${qtySimple > 1 ? 's' : ''} simple${qtySimple > 1 ? 's' : ''} et ${qtyIntegral} copie${qtyIntegral > 1 ? 's' : ''} intégrale${qtyIntegral > 1 ? 's' : ''}`;
            } else if (qtySimple > 0) {
                if (qtySimple === 1) {
                    documentType = "copie simple";
                } else {
                    documentType = `${qtySimple} copies simples`;
                }
            } else if (qtyIntegral > 0) {
                if (qtyIntegral === 1) {
                    documentType = "copie intégrale";
                } else {
                    documentType = `${qtyIntegral} copies intégrales`;
                }
            } else {
                if (isCopieSimple) {
                    documentType = "copie simple";
                } else {
                    documentType = "copie intégrale";
                }
            }
            const statusMap = {
                'en attente': {
                    color: '#f59e0b',
                    bg: '#fffbeb',
                    border: '#fde68a',
                    icon: 'fa-clock',
                    label: 'En attente'
                },
                'réçu': {
                    color: '#3b82f6',
                    bg: '#eff6ff',
                    border: '#bfdbfe',
                    icon: 'fa-spinner',
                    label: 'Reçu'
                },
                'traité': {
                    color: '#10b981',
                    bg: '#ecfdf5',
                    border: '#a7f3d0',
                    icon: 'fa-check-circle',
                    label: 'Traité'
                },
                'rejeté': {
                    color: '#ef4444',
                    bg: '#fef2f2',
                    border: '#fecaca',
                    icon: 'fa-times-circle',
                    label: 'Rejeté'
                },
            };
            const status = statusMap[mariage.etat] || {
                color: '#6b7280',
                bg: '#f9fafb',
                border: '#e5e7eb',
                icon: 'fa-question-circle',
                label: mariage.etat
            };

            const formatDocuments = (mariage) => {
                const docs = [];
                if (mariage.pieceIdentite) {
                    const p = '{{ asset('storage/') }}/' + mariage.pieceIdentite;
                    docs.push({
                        label: "Pièce d'identité",
                        path: p,
                        isPdf: p.toLowerCase().endsWith('.pdf')
                    });
                }
                if (mariage.extraitMariage) {
                    const p = '{{ asset('storage/') }}/' + mariage.extraitMariage;
                    docs.push({
                        label: 'Extrait de mariage',
                        path: p,
                        isPdf: p.toLowerCase().endsWith('.pdf')
                    });
                }
                if (mariage.document_autorisation) {
                    const p = '{{ asset('storage/') }}/' + mariage.document_autorisation;
                    docs.push({
                        label: "Document d'autorisation / Procuration",
                        path: p,
                        isPdf: p.toLowerCase().endsWith('.pdf')
                    });
                }
                if (!docs.length)
                    return `<div style="text-align:center;padding:24px;color:#94a3b8;"><i class="fas fa-folder-open" style="font-size:2rem;margin-bottom:8px;display:block;"></i><p style="margin:0;font-size:0.85rem;">Aucun document joint</p></div>`;
                return docs.map(d => `
              <div style="display:flex;align-items:center;gap:14px;padding:10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:10px;">
                <div style="width:60px;height:60px;border-radius:8px;overflow:hidden;background:white;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  ${d.isPdf ? '<i class="fas fa-file-pdf" style="color:#ef4444;font-size:1.8rem;"></i>' : `<img src="${d.path}" onclick="openImageModal('${d.path}')" style="width:100%;height:100%;object-fit:cover;cursor:pointer;" alt="${d.label}">`}
                </div>
                <div style="flex:1;">
                  <div style="font-weight:600;font-size:0.85rem;color:#0f172a;margin-bottom:6px;">${d.label}</div>
                  <div style="display:flex;gap:8px;">
                    ${!d.isPdf ? `<a href="javascript:void(0)" onclick="openImageModal('${d.path}')" style="color:#1f4083;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:5px;border:1px solid #bfdbfe;background:white;"><i class="fas fa-eye"></i> Aperçu</a>` : `<a href="${d.path}" target="_blank" style="color:#1f4083;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:5px;border:1px solid #bfdbfe;background:white;"><i class="fas fa-external-link-alt"></i> Ouvrir</a>`}
                    <a href="javascript:void(0)" onclick="imprimerDocument('${d.path}')" style="color:#475569;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:5px;border:1px solid #e2e8f0;background:white;"><i class="fas fa-print"></i> Imprimer</a>
                  </div>
                </div>
              </div>
            `).join('');
            };

            const mData = encodeURIComponent(JSON.stringify(mariage)).replace(/'/g, "%27");

            const htmlContent = `
          <div class="dp-wrap">
            <div class="dp-hero">
              <div class="dp-status-pill"><i class="fas ${status.icon}"></i> ${status.label}</div>
              <div class="dp-hero-icon"><i class="fas fa-heart"></i></div>
              <div class="dp-hero-title">Demande d'Extrait de Mariage</div>
              <div class="dp-hero-meta">
                <span><i class="fas fa-hashtag"></i> #${mariage.id}</span>
                <span style="opacity:.4">|</span>
                <span><i class="fas fa-calendar-alt"></i> ${new Date(mariage.created_at).toLocaleDateString('fr-FR',{day:'2-digit',month:'short',year:'numeric'})}</span>
                <span style="opacity:.4">|</span>
                <span><i class="fas fa-file-alt"></i> ${documentType}</span>
              </div>
            </div>
            <div class="dp-tabs" id="dpTabsM">
              <div class="dp-tab dp-active" data-panel="dpPM-infos"><i class="fas fa-info-circle"></i> Informations</div>
              <div class="dp-tab" data-panel="dpPM-livraison"><i class="fas fa-${mariage.choix_option === 'livraison' ? 'truck' : 'store'}"></i> ${mariage.choix_option === 'livraison' ? 'Livraison' : 'Retrait'}</div>
              <div class="dp-tab" data-panel="dpPM-docs"><i class="fas fa-paperclip"></i> Documents</div>
            </div>
            <div class="dp-panel dp-active" id="dpPM-infos">
              ${mariage.motif_de_rejet ? `<div class="dp-alert"><div class="dp-alert-icon"><i class="fas fa-exclamation-triangle"></i></div><div><div class="dp-alert-title">Demande rejetée</div><div class="dp-alert-text">${mariage.motif_de_rejet}</div></div></div>` : ''}
              <div class="dp-grid">
                <div class="dp-section">
                  <div class="dp-section-head" style="justify-content: space-between;">
                      <div style="display:flex; align-items:center; gap:10px;">
                          <div class="dp-section-icon"><i class="fas fa-${isCopieSimple ? 'file-alt' : 'venus-mars'}"></i></div>
                          <div class="dp-section-title">${isCopieSimple ? 'Informations de la demande' : 'Informations de la demande'}</div>
                      </div>
                      ${(mariage.etat === 'terminé' || mariage.etat === 'traité') ? `
                          <button type="button" disabled style="background:#94a3b8;color:white;border-radius:5px;border:none;padding:5px 12px;font-size:0.8rem;display:flex;align-items:center;gap:6px;cursor:not-allowed;opacity:0.6;" title="Cette demande est déjà terminée.">
                              <i class="fas fa-print"></i> Imprimer
                          </button>
                          ` : `
                          <button type="button" onclick="printMariageInfo('${mData}')" style="background:#1f4083;color:white;border-radius:5px;border:none;padding:5px 12px;font-size:0.8rem;display:flex;align-items:center;gap:6px;cursor:pointer;">
                              <i class="fas fa-print"></i> Imprimer
                          </button>
                          `}
                  </div>
                  ${isCopieSimple ? `
                                  <div class="dp-row"><span class="dp-label"><i class="fas fa-tag"></i> Type</span><span class="dp-value" style="color:#1f4083;font-weight:700;">Copie Simple</span></div>
                                  ` : `
                                  <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Nom Époux</span><span class="dp-value">${mariage.nomEpoux||'--'}</span></div>
                                  <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Prénom Époux</span><span class="dp-value">${mariage.prenomEpoux||'--'}</span></div>
                                  <div class="dp-row"><span class="dp-label"><i class="fas fa-calendar"></i> Naiss. Époux</span><span class="dp-value">${mariage.dateNaissanceEpoux||'--'}</span></div>
                                  <div class="dp-row"><span class="dp-label"><i class="fas fa-map-pin"></i> Lieu naiss.</span><span class="dp-value">${mariage.lieuNaissanceEpoux||'--'}</span></div>
                                  `}
                              <div class="dp-row"><span class="dp-label"><i class="fas fa-map-marker-alt"></i> Commune</span><span class="dp-value">${mariage.commune_mariage||'--'}</span></div>
                              <div class="dp-row"><span class="dp-label"><i class="fas fa-hashtag"></i> N° Registre</span><span class="dp-value">${mariage.numero_registre||'--'}</span></div>
                              <div class="dp-row"><span class="dp-label"><i class="fas fa-calendar-alt"></i> Date Registre</span><span class="dp-value">${mariage.date_registre ? new Date(mariage.date_registre).toLocaleDateString('fr-FR') : '--'}</span></div>
                </div>
                <div class="dp-section">
                  <div class="dp-section-head"><div class="dp-section-icon"><i class="fas fa-user-circle"></i></div><div class="dp-section-title">Demandeur</div></div>
                  <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Nom</span><span class="dp-value">${(user.name||'')+' '+(user.prenom||'')}</span></div>
                  <div class="dp-row"><span class="dp-label"><i class="fas fa-envelope"></i> Email</span><span class="dp-value">${user.email||'--'}</span></div>
                  <div class="dp-row"><span class="dp-label"><i class="fas fa-phone"></i> Contact</span><span class="dp-value">${user.contact||'--'}</span></div>
                  <div class="dp-row"><span class="dp-label"><i class="fas fa-clock"></i> Date</span><span class="dp-value">${new Date(mariage.created_at).toLocaleString('fr-FR')}</span></div>
                </div>
              </div>
              <div class="dp-section">
                <div class="dp-section-head"><div class="dp-section-icon"><i class="fas fa-file-invoice"></i></div><div class="dp-section-title">Détails de la Commande</div></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-file-alt"></i> Type</span><span class="dp-value" style="color:#1f4083;font-weight:700;">${documentType}</span></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-copy"></i> Quantité</span><span class="dp-value">${mariage.quantite} copie(s)</span></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-user-friends"></i> Pour</span><span class="dp-value">${mariage.pour === 'proprie' ? 'Lui-même' : (mariage.pour === 'tiers' ? 'Un tiers' : (mariage.pour || '--'))}</span></div>
                ${mariage.relation ? `<div class="dp-row"><span class="dp-label"><i class="fas fa-project-diagram"></i> Relation</span><span class="dp-value">${mariage.relation}</span></div>` : ''}
                <div class="dp-row"><span class="dp-label"><i class="fas fa-circle"></i> Statut</span><span class="dp-value"><span class="dp-badge" style="background:${status.bg};color:${status.color};border:1px solid ${status.border};"><i class="fas ${status.icon}"></i> ${status.label}</span></span></div>
              </div>
            </div>
            <div class="dp-panel" id="dpPM-livraison">
              ${mariage.choix_option === 'livraison' ? `
                              <div class="dp-section">
                                <div class="dp-section-head"><div class="dp-section-icon"><i class="fas fa-truck"></i></div><div class="dp-section-title">Informations de Livraison</div></div>
                                <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Destinataire</span><span class="dp-value">${mariage.nom_destinataire||'--'} ${mariage.prenom_destinataire||''}</span></div>
                                <div class="dp-row"><span class="dp-label"><i class="fas fa-phone"></i> Contact</span><span class="dp-value">${mariage.contact_destinataire||'--'}</span></div>
                                <div class="dp-row"><span class="dp-label"><i class="fas fa-envelope"></i> Email</span><span class="dp-value">${mariage.email_destinataire||'--'}</span></div>
                                <div class="dp-row"><span class="dp-label"><i class="fas fa-map-marker-alt"></i> Adresse</span><span class="dp-value">${mariage.adresse_livraison||'--'}</span></div>
                                <div class="dp-row"><span class="dp-label"><i class="fas fa-city"></i> Ville</span><span class="dp-value">${mariage.ville||'--'}</span></div>
                                <div class="dp-row"><span class="dp-label"><i class="fas fa-map"></i> Commune</span><span class="dp-value">${mariage.commune_livraison||'--'}</span></div>
                                <div class="dp-row"><span class="dp-label"><i class="fas fa-home"></i> Quartier</span><span class="dp-value">${mariage.quartier||'--'}</span></div>
                                <div class="dp-row"><span class="dp-label"><i class="fas fa-mail-bulk"></i> Code postal</span><span class="dp-value">${mariage.code_postal||'--'}</span></div>
                              </div>
                              ` : `<div style="text-align:center;padding:36px 20px;"><div class="dp-pickup"><i class="fas fa-store"></i> Retrait sur place</div><p style="margin-top:12px;color:#64748b;font-size:0.82rem;">Le demandeur récupérera son document directement à la mairie.</p></div>`}
            </div>
            <div class="dp-panel" id="dpPM-docs">
              <div class="dp-section">
                <div class="dp-section-head"><div class="dp-section-icon"><i class="fas fa-paperclip"></i></div><div class="dp-section-title">Documents Joints</div></div>
                <div style="padding:12px;">${formatDocuments(mariage)}</div>
              </div>
            </div>
          </div>
        `;

            Swal.fire({
                html: htmlContent,
                width: '860px',
                confirmButtonText: '<i class="fas fa-times"></i> Fermer',
                confirmButtonColor: '#1f4083',
                showCloseButton: true,
                padding: 0,
                customClass: {
                    popup: 'request-details-popup'
                },
                didOpen: () => {
                    var tabs = document.querySelectorAll('#dpTabsM .dp-tab');
                    tabs.forEach(function(t) {
                        t.addEventListener('click', function() {
                            tabs.forEach(function(x) {
                                x.classList.remove('dp-active');
                            });
                            document.querySelectorAll('#dpTabsM ~ .dp-panel').forEach(function(
                                p) {
                                p.classList.remove('dp-active');
                            });
                            t.classList.add('dp-active');
                            var panel = document.getElementById(t.dataset.panel);
                            if (panel) panel.classList.add('dp-active');
                        });
                    });
                }
            });
        }

        function printMariageInfo(encodedData) {
            const mariage = JSON.parse(decodeURIComponent(encodedData));
            const printWindow = window.open('', '_blank');
            const isCopieSimple = mariage.nomEpoux === null;

            printWindow.document.title = "Impression Informations Demande";

            const style = printWindow.document.createElement('style');
            style.textContent = `
                    body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; padding: 10px; color: #000; margin: 0; }
                    .info-block { border: 1px solid #000; padding: 15px; border-radius: 8px; max-width: 280px; margin: 0 auto; background: #fff; }
                    .row { display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px dotted #94a3b8; padding-bottom: 4px; align-items: center; }
                    .row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
                    .label { font-weight: 700; color: #000; font-size: 0.75rem; }
                    .value { font-weight: 700; color: #000; text-transform: uppercase; font-size: 0.8rem; text-align: right; max-width: 65%; word-wrap: break-word; }
                    .title { text-align: center; font-size: 0.95rem; margin-bottom: 15px; font-weight: 800; color: #000; text-transform: uppercase; border-bottom: 2px solid #000; padding-bottom: 6px; }
                    @media print {
                        body { padding: 0; margin: 0; }
                        .info-block { border: none; padding: 0; width: 100%; max-width: none; }
                    }
                `;
            printWindow.document.head.appendChild(style);

            const container = printWindow.document.createElement('div');
            container.className = 'info-block';

            const title = printWindow.document.createElement('div');
            title.className = 'title';
            title.textContent = 'Demande de Mariage';
            container.appendChild(title);

            let docTypes = '';
            const qtySimple = parseInt(mariage.qty_simple) || 0;
            const qtyIntegral = parseInt(mariage.qty_integral) || 0;

            if (qtySimple > 0 && qtyIntegral > 0) {
                docTypes =
                    `${qtySimple} copie${qtySimple > 1 ? 's' : ''} simple${qtySimple > 1 ? 's' : ''} et ${qtyIntegral} copie${qtyIntegral > 1 ? 's' : ''} intégrale${qtyIntegral > 1 ? 's' : ''}`;
            } else if (qtySimple > 0) {
                if (qtySimple === 1) {
                    docTypes = "copie simple";
                } else {
                    docTypes = `${qtySimple} copies simples`;
                }
            } else if (qtyIntegral > 0) {
                if (qtyIntegral === 1) {
                    docTypes = "copie intégrale";
                } else {
                    docTypes = `${qtyIntegral} copies intégrales`;
                }
            } else {
                if (isCopieSimple) {
                    docTypes = "copie simple";
                } else {
                    docTypes = "copie intégrale";
                }
            }

            const totalQty = mariage.quantite || (qtySimple + qtyIntegral) || 1;

            let fields = [];
            if (isCopieSimple) {
                fields = [{
                        label: 'Type',
                        value: docTypes
                    },
                    {
                        label: 'Quantité',
                        value: `${totalQty} copie(s)`
                    }
                ];
            } else {
                fields = [{
                        label: 'Type',
                        value: docTypes
                    },
                    {
                        label: 'Quantité',
                        value: `${totalQty} copie(s)`
                    },
                    {
                        label: 'Nom Époux',
                        value: mariage.nomEpoux || '--'
                    },
                    {
                        label: 'Prénom Époux',
                        value: mariage.prenomEpoux || '--'
                    },
                    {
                        label: 'Naiss. Époux',
                        value: mariage.dateNaissanceEpoux || '--'
                    },
                    {
                        label: 'Lieu naiss.',
                        value: mariage.lieuNaissanceEpoux || '--'
                    }
                ];
            }

            fields.push({
                label: 'Commune',
                value: mariage.commune_mariage || '--'
            }, {
                label: 'N° Registre',
                value: mariage.numero_registre || '--'
            }, {
                label: 'Date Registre',
                value: mariage.date_registre ? new Date(mariage.date_registre).toLocaleDateString('fr-FR') : '--'
            });

            fields.forEach(field => {
                const row = printWindow.document.createElement('div');
                row.className = 'row';

                const labelSpan = printWindow.document.createElement('span');
                labelSpan.className = 'label';
                labelSpan.textContent = field.label;

                const valueSpan = printWindow.document.createElement('span');
                valueSpan.className = 'value';
                valueSpan.textContent = field.value;

                row.appendChild(labelSpan);
                row.appendChild(valueSpan);
                container.appendChild(row);
            });

            printWindow.document.body.appendChild(container);

            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }

        // Fonction pour ouvrir une image en grand dans une modal
        function openImageModal(imageSrc) {
            const htmlContent = `
            <div style="text-align:center;">
              <div style="border-radius:12px;overflow:hidden;background:#000;display:inline-block;max-width:100%;box-shadow:0 8px 32px rgba(0,0,0,0.25);">
                <img src="${imageSrc}" style="max-width:100%;max-height:65vh;display:block;" alt="Document">
              </div>
              <div style="margin-top:16px;display:flex;justify-content:center;gap:10px;flex-wrap:wrap;">
                <a href="javascript:void(0)" onclick="imprimerDocument('${imageSrc}')" style="color:#1f4083;text-decoration:none;display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border:1px solid #bfdbfe;border-radius:8px;background:#eff6ff;font-size:0.85rem;font-weight:600;">
                  <i class="fas fa-print"></i> Imprimer
                </a>
                <button onclick="Swal.close()" style="color:#475569;display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border:1px solid #e2e8f0;border-radius:8px;background:white;font-size:0.85rem;font-weight:600;cursor:pointer;">
                  <i class="fas fa-times"></i> Fermer
                </button>
              </div>
            </div>
        `;
            Swal.fire({
                title: '<span style="font-size:1rem;color:#0f172a;">Visualisation du document</span>',
                html: htmlContent,
                width: '800px',
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    popup: 'image-modal-popup'
                }
            });
        }

        // Fonction pour imprimer directement un document (image ou PDF)
        function imprimerDocument(url) {
            if (!url) return;
            const isPdf = url.toLowerCase().endsWith('.pdf');
            if (isPdf) {
                const printWindow = window.open(url, '_blank');
                if (printWindow) {
                    printWindow.onload = function() {
                        setTimeout(function() {
                            printWindow.print();
                        }, 500);
                    };
                }
            } else {
                const printWindow = window.open('', '_blank');
                if (!printWindow) return;
                printWindow.document.title = "Impression Document";
                const style = printWindow.document.createElement('style');
                style.textContent = `
                    body { margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #fff; }
                    img { max-width: 100%; height: auto; page-break-inside: avoid; }
                    @media print {
                        body { display: block; }
                        img { max-width: 100%; width: 100%; height: auto; }
                    }
                `;
                printWindow.document.head.appendChild(style);
                const img = printWindow.document.createElement('img');
                img.src = url;
                img.onload = function() {
                    setTimeout(function() {
                        printWindow.print();
                        printWindow.close();
                    }, 300);
                };
                printWindow.document.body.appendChild(img);
            }
        }

        function markAsDelivered(id) {
            Swal.fire({
                title: 'Entrer la référence',
                input: 'text',
                inputLabel: 'Veuillez entrer la référence du colis',
                inputPlaceholder: 'Référence',
                showCancelButton: true,
                confirmButtonText: 'Valider',
                cancelButtonText: 'Annuler',
                preConfirm: (reference) => {
                    if (!reference) {
                        Swal.showValidationMessage('Vous devez entrer une référence');
                    }
                    return reference;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const url = markAsDeliveredUrl.replace(':id', id);

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            statut_livraison: 'livré',
                            reference: result.value
                        },
                        success: function(response) {
                            Swal.fire('Succès!', 'La demande a été marquée comme livrée.', 'success');
                            location.reload();
                        },
                        error: function(xhr) {
                            const errorMessage = xhr.responseJSON.error ||
                                'Une erreur est survenue lors de la mise à jour.';
                            Swal.fire('Erreur!', errorMessage, 'error');
                        }
                    });
                }
            });
        }

        // Impression directe de l'étiquette sans popup
        function imprimerEtiquette(id) {
            const url = downloadDeliveryInfoUrl.replace(':id', id);
            const printWindow = window.open(url, '_blank');
            if (printWindow) {
                printWindow.onload = function() {
                    setTimeout(function() {
                        printWindow.print();
                    }, 500);
                };
            }
        }

        // Fonction pour afficher les informations de livraison
        function showDeliveryInfo(mariage) {
            const deliveryInfo = mariage || {};
            const fallbackUser = mariage.user || {};
            const destinataire = [deliveryInfo.nom_destinataire, deliveryInfo.prenom_destinataire]
                .filter(v => v && String(v).trim() !== '')
                .join(' ')
                .trim() || [fallbackUser.name, fallbackUser.prenom]
                .filter(v => v && String(v).trim() !== '')
                .join(' ')
                .trim() || 'Non spécifié';
            const telephone = deliveryInfo.contact_destinataire || deliveryInfo.telephone || fallbackUser.contact ||
                'Non spécifié';
            const email = deliveryInfo.email_destinataire || fallbackUser.email || 'Non spécifié';

            const htmlContent = `
            <div style="text-align: center;">
                <h3 style="color: #1f4083; margin-bottom: 20px;">Informations de Livraison</h3>
                
                <div style="margin-bottom: 15px;">
                    <strong>Nom du destinataire:</strong> ${destinataire}
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Téléphone:</strong> ${telephone}
                </div>

                <div style="margin-bottom: 15px;">
                    <strong>Email:</strong> ${email}
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Ville:</strong> ${deliveryInfo.ville || 'Non spécifiée'}
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Commune:</strong> ${deliveryInfo.commune_livraison || 'Non spécifiée'}
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Quartier:</strong> ${deliveryInfo.quartier || 'Non spécifiée'}
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Code de livraison:</strong> ${deliveryInfo.livraison_code || 'Non spécifiée'}
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Adresse de livraison:</strong> ${deliveryInfo.adresse_livraison || 'Non spécifiée'}
                </div>
            </div>
        `;

            Swal.fire({
                title: 'Détails de Livraison',
                html: htmlContent,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-print"></i> Imprimer l\'étiquette',
                cancelButtonText: 'Fermer',
                confirmButtonColor: '#1f4083',
                width: '600px',
                customClass: {
                    popup: 'delivery-info-popup'
                },
                didOpen: () => {
                    const popup = Swal.getPopup();
                    popup.style.borderRadius = '12px';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Ouvrir l'étiquette et déclencher l'impression automatique
                    const url = downloadDeliveryInfoUrl.replace(':id', mariage.id);
                    const printWindow = window.open(url, '_blank');
                    if (printWindow) {
                        printWindow.onload = function() {
                            setTimeout(function() {
                                printWindow.print();
                            }, 500);
                        };
                    }
                }
            });
        }
    </script>
    <style>
        .dp-wrap {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            color: #1e293b;
        }

        .dp-hero {
            background: linear-gradient(135deg, #1f4083 0%, #0d47a1 100%);
            padding: 24px 28px 20px;
            position: relative;
            overflow: hidden;
        }

        .dp-hero::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 160px;
            height: 160px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
        }

        .dp-hero::after {
            content: '';
            position: absolute;
            bottom: -40px;
            left: -30px;
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
        }

        .dp-hero-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            font-size: 1.3rem;
            color: white;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(4px);
        }

        .dp-hero-title {
            color: white;
            font-size: 1.25rem;
            font-weight: 800;
            margin: 0 0 6px;
            position: relative;
            z-index: 1;
        }

        .dp-hero-meta {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.78rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .dp-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: absolute;
            top: 18px;
            right: 18px;
            z-index: 2;
            backdrop-filter: blur(4px);
        }

        .dp-tabs {
            display: flex;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 16px;
        }

        .dp-tab {
            padding: 11px 16px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all .2s;
            display: flex;
            align-items: center;
            gap: 6px;
            user-select: none;
            white-space: nowrap;
        }

        .dp-tab:hover {
            color: #1f4083;
        }

        .dp-tab.dp-active {
            color: #1f4083;
            border-bottom-color: #1f4083;
        }

        .dp-panel {
            display: none;
            padding: 16px;
        }

        .dp-panel.dp-active {
            display: block;
        }

        .dp-section {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 14px;
        }

        .dp-section-head {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .dp-section-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: linear-gradient(135deg, #1f4083, #0d47a1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .dp-section-title {
            font-weight: 700;
            font-size: 0.88rem;
            color: #0f172a;
        }

        .dp-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 8px 16px;
            gap: 12px;
            transition: background .15s;
        }

        .dp-row:hover {
            background: #f8fafc;
        }

        .dp-label {
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 500;
            min-width: 120px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .dp-label i {
            width: 13px;
            color: #94a3b8;
            font-size: 0.75rem;
        }

        .dp-value {
            color: #0f172a;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: right;
            word-break: break-word;
        }

        .dp-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .dp-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 14px;
            display: flex;
            gap: 10px;
        }

        .dp-alert-icon {
            color: #ef4444;
            font-size: 1rem;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .dp-alert-title {
            color: #991b1b;
            font-weight: 700;
            font-size: 0.8rem;
            margin-bottom: 2px;
        }

        .dp-alert-text {
            color: #b91c1c;
            font-size: 0.78rem;
        }

        .dp-pickup {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #eff6ff;
            color: #1d4ed8;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid #bfdbfe;
        }

        .dp-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        @media(max-width:600px) {
            .dp-grid {
                grid-template-columns: 1fr
            }

            .dp-label {
                min-width: 90px
            }
        }

        .request-details-popup {
            padding: 0 !important;
            border-radius: 16px !important;
            overflow: hidden !important;
        }
    </style>
@endsection
