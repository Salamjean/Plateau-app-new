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
            min-width: 1200px;
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
                <i class="fas fa-cross me-2"></i>Gestion des demandes d'acte de décès
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
                    <h3>{{ $deces->total() }}</h3>
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
                                <th style="text-align: center">Quantité</th>
                                <th style="text-align: center">Type</th>
                                <th style="text-align: center">Demandeur</th>
                                <th style="text-align: center">Nom complet du défunt</th>
                                <th style="text-align: center">Parents</th>
                                <th style="text-align: center">N° Registre</th>
                                <th style="text-align: center">Date Registre</th>
                                <th style="text-align: center">Date demande</th>
                                <th style="text-align: center">Document</th>
                                <th style="text-align: center">Statut</th>
                                <th style="text-align: center">Actions</th>
                                <th style="text-align: center">Mode Retrait</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($deces as $dece)
                                <tr>
                                    <td style="text-align: center">
                                        @if ($dece->choix_option == 'livraison' && $dece->etat == 'terminé')
                                            <button class="download-btn"
                                                onclick="showDeliveryInfo({{ json_encode($dece) }})">
                                                <i class="fas fa-download me-1"></i>Télécharger
                                            </button>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-center fw-bold">{{ $dece->quantite }} copie(s)</div>
                                        @if ($dece->type == 'simpleIntegrale' || $dece->type == 'groupee')
                                            <div class="text-center mt-1" style="font-size: 0.75rem;">
                                                <span class="text-info">{{ $dece->qty_simple }} Simple</span><br>
                                                <span class="text-primary">{{ $dece->qty_integral }} Intégrale</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td style="text-align: center">
                                        @if ($dece->type == 'simple')
                                            <span class="badge bg-info text-white">Copie Simple</span>
                                        @elseif($dece->type == 'groupee' || $dece->type == 'simpleIntegrale')
                                            <span class="badge bg-success text-white">Simple + Intégral</span>
                                        @elseif($dece->type == 'integrale' || $dece->type == 'extrait_integral')
                                            <span class="badge bg-primary text-white">Copie Intégrale</span>
                                        @else
                                            <span
                                                class="badge bg-secondary text-white">{{ ucfirst($dece->type ?: 'Simple') }}</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center" data-label="Demandeur">
                                        <div class="user-info"
                                            style="text-align: center; display:flex; justify-content:center">
                                            <div class="user-avatar" style="text-align: center">
                                                {{ substr($dece->user->name, 0, 1) . '' . substr($dece->user->prenom, 0, 1) }}
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name">{{ $dece->user->name . ' ' . $dece->user->prenom }}
                                                </div>
                                                <div class="user-email">{{ $dece->user->contact }}</div>
                                                <div class="user-email">{{ $dece->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center" data-label="Demandeur">
                                        <div class="user-info"
                                            style="text-align: center; display:flex; justify-content:center">
                                            <div class="user-avatar1" style="text-align: center">
                                                {{ substr($dece->name, 0, 1) . '' . substr($dece->prenom, 0, 1) }}
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name">{{ $dece->name . ' ' . $dece->prenom }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center">
                                        <small>
                                            <strong>P:</strong> {{ $dece->nom_prenoms_pere ?? '--' }}<br>
                                            <strong>M:</strong> {{ $dece->nom_prenoms_mere ?? '--' }}
                                        </small>
                                    </td>
                                    <td style="text-align: center">{{ $dece->numberR }}</td>
                                    <td style="text-align: center">{{ $dece->dateR }}</td>
                                    <td style="text-align: center" data-label="Date demande">
                                        {{ $dece->created_at->format('d/m/Y H:i') }}</td>
                                    <td style="text-align: center">
                                        @if ($dece->CNIdfnt)
                                            @php
                                                $CNIPath = asset('storage/' . $dece->CNIdfnt);
                                                $isCNIPdf =
                                                    strtolower(pathinfo($CNIPath, PATHINFO_EXTENSION)) === 'pdf';
                                            @endphp
                                            @if ($isCNIPdf)
                                                <a href="{{ $CNIPath }}" target="_blank" class="document-preview">
                                                    <img src="{{ asset('assets/assets/img/pdf.jpg') }}" style="width: 40px"
                                                        alt="PDF" class="document-preview">
                                                </a>
                                            @else
                                                <img src="{{ $CNIPath }}" alt="CNI"
                                                    style="width: 40px; height:40px" class="document-preview"
                                                    data-bs-toggle="modal" data-bs-target="#imageModal"
                                                    onclick="showImage(this)"
                                                    onerror="this.onerror=null; this.src='{{ asset('assets/images/profiles/bébé.jpg') }}'">
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                        @if ($dece->CNIdcl)
                                            @php
                                                $CNIdclPath = asset('storage/' . $dece->CNIdcl);
                                                $isCNIdclPdf =
                                                    strtolower(pathinfo($CNIdclPath, PATHINFO_EXTENSION)) === 'pdf';
                                            @endphp
                                            @if ($isCNIdclPdf)
                                                <a href="{{ $CNIdclPath }}" target="_blank" class="document-preview">
                                                    <img src="{{ asset('assets/assets/img/pdf.jpg') }}" style="width: 40px"
                                                        alt="PDF" class="document-preview">
                                                </a>
                                            @else
                                                <img src="{{ $CNIdclPath }}" alt="CNIdcl"
                                                    style="width: 40px; height:40px" class="document-preview"
                                                    data-bs-toggle="modal" data-bs-target="#imageModal"
                                                    onclick="showImage(this)"
                                                    onerror="this.onerror=null; this.src='{{ asset('assets/images/profiles/bébé.jpg') }}'">
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                        @if (!empty($dece->documentMariage))
                                            @php
                                                $documentMariagePath = asset('storage/' . $dece->documentMariage);
                                                $isdocumentMariagePdf =
                                                    strtolower(pathinfo($documentMariagePath, PATHINFO_EXTENSION)) ===
                                                    'pdf';
                                            @endphp

                                            @if ($isdocumentMariagePdf)
                                                <a href="{{ $documentMariagePath }}" target="_blank"
                                                    class="document-preview">
                                                    <img src="{{ asset('assets/assets/img/pdf.jpg') }}"
                                                        style="width: 40px" alt="PDF">
                                                </a>
                                            @else
                                                <img src="{{ $documentMariagePath }}" alt="documentMariage"
                                                    style="width: 40px; height:40px" class="document-preview"
                                                    data-bs-toggle="modal" data-bs-target="#imageModal"
                                                    onclick="showImage(this)"
                                                    onerror="this.onerror=null; this.src='{{ asset('assets/images/profiles/bébé.jpg') }}'">
                                            @endif
                                        @endif
                                        @if (!empty($dece->RequisPolice))
                                            @php
                                                $RequisPolicePath = asset('storage/' . $dece->RequisPolice);
                                                $isRequisPolicePdf =
                                                    strtolower(pathinfo($RequisPolicePath, PATHINFO_EXTENSION)) ===
                                                    'pdf';
                                            @endphp

                                            @if ($isRequisPolicePdf)
                                                <a href="{{ $RequisPolicePath }}" target="_blank"
                                                    class="document-preview">
                                                    <img src="{{ asset('assets/assets/img/pdf.jpg') }}"
                                                        style="width: 40px" alt="PDF">
                                                </a>
                                            @else
                                                <img src="{{ $RequisPolicePath }}" alt="RequisPolice"
                                                    style="width: 40px; height:40px" class="document-preview"
                                                    data-bs-toggle="modal" data-bs-target="#imageModal"
                                                    onclick="showImage(this)"
                                                    onerror="this.onerror=null; this.src='{{ asset('assets/images/profiles/bébé.jpg') }}'">
                                            @endif
                                        @endif
                                    </td>
                                    <td style="text-align: center" data-label="Statut">
                                        @if ($dece->etat == 'en attente')
                                            <span class="badge-status badge-pending">En attente</span>
                                        @elseif($dece->etat == 'réçu')
                                            <span class="badge-status badge-progress">En cours</span>
                                        @else
                                            <span class="badge-status badge-completed">Terminé</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center" data-label="Actions">
                                        <!-- Bouton pour voir les détails -->
                                        <button class="btn-action btn-icon"
                                            style="background-color: #17a2b8; cursor: pointer;"
                                            data-task="{{ json_encode($dece) }}" onclick="showRequestDetails(this)"
                                            title="Voir les détails de la demande">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if ($dece->etat === 'terminé')
                                            <a href="#" class="btn-action btn-secondary btn-icon disabled"
                                                title="Demande terminée"
                                                style="opacity: 0.5; pointer-events: none; background-color: #6c757d;">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('agent.demandes.deces.edit', $dece->id) }}"
                                                class="btn-action btn-secondary btn-icon"
                                                title="Modifier l'état de la demande">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td style="text-align: center">
                                        <div class="d-flex justify-content-center gap-2">
                                            @if ($dece->choix_option == 'livraison')
                                                <a href="#" class="delivery-badge badge"
                                                    style="background-color: #2c7873;" data-bs-toggle="modal"
                                                    data-bs-target="#livraisonModal"
                                                    onclick="showLivraisonModal({{ json_encode($dece) }})">
                                                    <i class="fas fa-truck"></i> Livraison
                                                </a>
                                                @if ($dece->date_livraison)
                                                    <div style="font-size: 0.7rem; color: #666; margin-top: 5px;">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        {{ \Carbon\Carbon::parse($dece->date_livraison)->format('d/m/Y') }}
                                                        @if ($dece->heure_livraison)
                                                            <br><i class="fas fa-clock"></i>
                                                            {{ \Carbon\Carbon::parse($dece->heure_livraison)->format('H:i') }}
                                                        @endif
                                                    </div>
                                                @endif
                                            @else
                                                @if ($dece->etat !== 'terminé')
                                                    <span class="badge"><i class="fas fa-home"></i> Retrait sur
                                                        place</span>
                                                @endif
                                                @if ($dece->etat == 'terminé')
                                                    <button class="btn-action"
                                                        onclick="markAsDelivered({{ $dece->id }})"
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
                                    <td style="text-align: center" colspan="13" class="empty-state">
                                        <i class="fas fa-cross"></i>
                                        <h5>Aucune demande d'extrait de deces en cours</h5>
                                        <p>Toutes les demandes sont traitées ou vous n'avez pas encore récuperer demande.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($deces->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Affichage de <strong>{{ $deces->firstItem() }}</strong> à
                            <strong>{{ $deces->lastItem() }}</strong> sur
                            <strong>{{ $deces->total() }}</strong> résultats
                        </div>

                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0">
                                <!-- Première page -->
                                @if ($deces->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-angle-double-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $deces->url(1) }}" aria-label="Première page">
                                            <i class="fas fa-angle-double-left"></i>
                                        </a>
                                    </li>
                                @endif

                                <!-- Page précédente -->
                                @if ($deces->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $deces->previousPageUrl() }}"
                                            aria-label="Précédent">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                <!-- Pages numérotées -->
                                @php
                                    $current = $deces->currentPage();
                                    $last = $deces->lastPage();
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
                                        <a class="page-link" href="{{ $deces->url(1) }}">1</a>
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
                                            <a class="page-link" href="{{ $deces->url($i) }}">{{ $i }}</a>
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
                                        <a class="page-link" href="{{ $deces->url($last) }}">{{ $last }}</a>
                                    </li>
                                @endif

                                <!-- Page suivante -->
                                @if ($deces->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $deces->nextPageUrl() }}" aria-label="Suivant">
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
                                @if ($deces->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $deces->url($last) }}" aria-label="Dernière page">
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
        const markAsDeliveredUrl = "{{ route('livraison.mark.deces', ':id') }}";
        const downloadDeliveryInfoUrl = "{{ route('agent.download.deces.delivery.info', ':id') }}";

        // Fonction pour afficher tous les détails de la demande
        function showRequestDetails(deceOrElement) {
            let dece = deceOrElement;
            if (deceOrElement && (deceOrElement.dataset || (deceOrElement.getAttribute && deceOrElement.getAttribute(
                    'data-task')))) {
                const raw = deceOrElement.dataset.task || deceOrElement.getAttribute('data-task');
                if (raw) {
                    try {
                        dece = typeof raw === 'string' ? JSON.parse(raw) : raw;
                    } catch (e) {
                        console.error("Error parsing dece JSON:", e);
                    }
                }
            } else if (typeof deceOrElement === 'string') {
                try {
                    dece = JSON.parse(deceOrElement);
                } catch (e) {}
            }
            const user = (dece && dece.user) || {};
            const documentType = dece.type === 'simple' ? 'Copie Simple' : (dece.type === 'simpleIntegrale' || dece.type ===
                'groupee' ? 'Simple + Intégral' : 'Copie Intégrale');
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
            const status = statusMap[dece.etat] || {
                color: '#6b7280',
                bg: '#f9fafb',
                border: '#e5e7eb',
                icon: 'fa-question-circle',
                label: dece.etat
            };

            const formatDocuments = (dece) => {
                const docs = [];
                if (dece.CNIdfnt) {
                    const p = '{{ asset('storage/') }}/' + dece.CNIdfnt;
                    docs.push({
                        label: 'CNI Défunt',
                        path: p,
                        isPdf: p.toLowerCase().endsWith('.pdf')
                    });
                }
                if (dece.CNIdcl) {
                    const p = '{{ asset('storage/') }}/' + dece.CNIdcl;
                    docs.push({
                        label: 'CNI Déclarant',
                        path: p,
                        isPdf: p.toLowerCase().endsWith('.pdf')
                    });
                }
                if (dece.documentMariage) {
                    const p = '{{ asset('storage/') }}/' + dece.documentMariage;
                    docs.push({
                        label: 'Document Mariage',
                        path: p,
                        isPdf: p.toLowerCase().endsWith('.pdf')
                    });
                }
                if (dece.RequisPolice) {
                    const p = '{{ asset('storage/') }}/' + dece.RequisPolice;
                    docs.push({
                        label: 'Réquis Police',
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
                <a href="${d.path}" download style="color:#475569;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:5px;border:1px solid #e2e8f0;background:white;"><i class="fas fa-download"></i> Télécharger</a>
              </div>
            </div>
          </div>
        `).join('');
            };

            const htmlContent = `
        <div class="dp-wrap">
          <div class="dp-hero">
            <div class="dp-status-pill"><i class="fas ${status.icon}"></i> ${status.label}</div>
            <div class="dp-hero-icon"><i class="fas fa-cross"></i></div>
            <div class="dp-hero-title">Demande d'Acte de Décès</div>
            <div class="dp-hero-meta">
              <span><i class="fas fa-hashtag"></i> #${dece.id}</span>
              <span style="opacity:.4">|</span>
              <span><i class="fas fa-calendar-alt"></i> ${new Date(dece.created_at).toLocaleDateString('fr-FR',{day:'2-digit',month:'short',year:'numeric'})}</span>
              <span style="opacity:.4">|</span>
              <span><i class="fas fa-file-alt"></i> ${documentType}</span>
            </div>
          </div>
          <div class="dp-tabs" id="dpTabsD">
            <div class="dp-tab dp-active" data-panel="dpPD-infos"><i class="fas fa-info-circle"></i> Informations</div>
            <div class="dp-tab" data-panel="dpPD-livraison"><i class="fas fa-${dece.choix_option === 'livraison' ? 'truck' : 'store'}"></i> ${dece.choix_option === 'livraison' ? 'Livraison' : 'Retrait'}</div>
            <div class="dp-tab" data-panel="dpPD-docs"><i class="fas fa-paperclip"></i> Documents</div>
          </div>
          <div class="dp-panel dp-active" id="dpPD-infos">
            ${dece.motif_de_rejet ? `<div class="dp-alert"><div class="dp-alert-icon"><i class="fas fa-exclamation-triangle"></i></div><div><div class="dp-alert-title">Demande rejetée</div><div class="dp-alert-text">${dece.motif_de_rejet}</div></div></div>` : ''}
            <div class="dp-grid">
              <div class="dp-section">
                <div class="dp-section-head"><div class="dp-section-icon"><i class="fas fa-cross"></i></div><div class="dp-section-title">Informations du Défunt</div></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Nom</span><span class="dp-value">${dece.name||'--'}</span></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Prénom</span><span class="dp-value">${dece.prenom||'--'}</span></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-hashtag"></i> N° Registre</span><span class="dp-value">${dece.numberR||'--'}</span></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-calendar"></i> Date Reg.</span><span class="dp-value">${dece.dateR||'--'}</span></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-map-pin"></i> Commune</span><span class="dp-value">${dece.commune||'--'}</span></div>
              </div>
              <div class="dp-section">
                <div class="dp-section-head"><div class="dp-section-icon"><i class="fas fa-user-circle"></i></div><div class="dp-section-title">Demandeur</div></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Nom</span><span class="dp-value">${(user.name||'')+' '+(user.prenom||'')}</span></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-envelope"></i> Email</span><span class="dp-value">${user.email||'--'}</span></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-phone"></i> Contact</span><span class="dp-value">${user.contact||'--'}</span></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-clock"></i> Date</span><span class="dp-value">${new Date(dece.created_at).toLocaleString('fr-FR')}</span></div>
              </div>
            </div>
            <div class="dp-section">
              <div class="dp-section-head"><div class="dp-section-icon"><i class="fas fa-file-invoice"></i></div><div class="dp-section-title">Détails de la Commande</div></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-file-alt"></i> Type</span><span class="dp-value" style="color:#1f4083;font-weight:700;">${documentType}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-copy"></i> Quantité</span><span class="dp-value">${dece.quantite} copie(s)${(dece.type==='simpleIntegrale'||dece.type==='groupee')?` <small style="color:#64748b;font-weight:400;">(${dece.qty_simple||0}s + ${dece.qty_integral||0}i)</small>`:''}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-circle"></i> Statut</span><span class="dp-value"><span class="dp-badge" style="background:${status.bg};color:${status.color};border:1px solid ${status.border};"><i class="fas ${status.icon}"></i> ${status.label}</span></span></div>
            </div>
          </div>
          <div class="dp-panel" id="dpPD-livraison">
            ${dece.choix_option === 'livraison' ? `
                    <div class="dp-section">
                      <div class="dp-section-head"><div class="dp-section-icon"><i class="fas fa-truck"></i></div><div class="dp-section-title">Informations de Livraison</div></div>
                      <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Destinataire</span><span class="dp-value">${dece.nom_destinataire||'--'}</span></div>
                      <div class="dp-row"><span class="dp-label"><i class="fas fa-phone"></i> Contact</span><span class="dp-value">${dece.contact_destinataire||'--'}</span></div>
                      <div class="dp-row"><span class="dp-label"><i class="fas fa-envelope"></i> Email</span><span class="dp-value">${dece.email_destinataire||'--'}</span></div>
                      <div class="dp-row"><span class="dp-label"><i class="fas fa-map-marker-alt"></i> Adresse</span><span class="dp-value">${dece.adresse_livraison||'--'}</span></div>
                      <div class="dp-row"><span class="dp-label"><i class="fas fa-city"></i> Ville</span><span class="dp-value">${dece.ville||'--'}</span></div>
                      <div class="dp-row"><span class="dp-label"><i class="fas fa-map"></i> Commune</span><span class="dp-value">${dece.commune_livraison||'--'}</span></div>
                      <div class="dp-row"><span class="dp-label"><i class="fas fa-home"></i> Quartier</span><span class="dp-value">${dece.quartier||'--'}</span></div>
                      <div class="dp-row"><span class="dp-label"><i class="fas fa-mail-bulk"></i> Code postal</span><span class="dp-value">${dece.code_postal||'--'}</span></div>
                    </div>
                    ` : `<div style="text-align:center;padding:36px 20px;"><div class="dp-pickup"><i class="fas fa-store"></i> Retrait sur place</div><p style="margin-top:12px;color:#64748b;font-size:0.82rem;">Le demandeur récupérera son document directement à la mairie.</p></div>`}
          </div>
          <div class="dp-panel" id="dpPD-docs">
            <div class="dp-section">
              <div class="dp-section-head"><div class="dp-section-icon"><i class="fas fa-paperclip"></i></div><div class="dp-section-title">Documents Joints</div></div>
              <div style="padding:12px;">${formatDocuments(dece)}</div>
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
                    var tabs = document.querySelectorAll('#dpTabsD .dp-tab');
                    tabs.forEach(function(t) {
                        t.addEventListener('click', function() {
                            tabs.forEach(function(x) {
                                x.classList.remove('dp-active');
                            });
                            document.querySelectorAll('#dpTabsD ~ .dp-panel').forEach(function(
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
        // Fonction pour ouvrir une image en grand dans une modal
        function openImageModal(imageSrc) {
            const htmlContent = `
        <div style="text-align:center;">
          <div style="border-radius:12px;overflow:hidden;background:#000;display:inline-block;max-width:100%;box-shadow:0 8px 32px rgba(0,0,0,0.25);">
            <img src="${imageSrc}" style="max-width:100%;max-height:65vh;display:block;" alt="Document">
          </div>
          <div style="margin-top:16px;display:flex;justify-content:center;gap:10px;flex-wrap:wrap;">
            <a href="${imageSrc}" download style="color:#1f4083;text-decoration:none;display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border:1px solid #bfdbfe;border-radius:8px;background:#eff6ff;font-size:0.85rem;font-weight:600;">
              <i class="fas fa-download"></i> Télécharger
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
                            reference: result.value // Ajoutez la référence ici
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

        // Fonction pour afficher les informations de livraison
        function showDeliveryInfo(dece) {
            // Récupérer les informations de livraison
            const deliveryInfo = dece || {};

            // Formater le contenu HTML pour SweetAlert
            const htmlContent = `
          <div style="text-align: center;">
            <h3 style="color: #1f4083; margin-bottom: 20px;">Informations de Livraison</h3>

            <div style="margin-bottom: 15px;">
              <strong>Nom du destinataire:</strong> ${deliveryInfo.nom_destinataire + ' ' + deliveryInfo.prenom_destinataire || dece.user.name + ' ' + dece.user.prenom}
            </div>

            <div style="margin-bottom: 15px;">
              <strong>Téléphone:</strong> ${deliveryInfo.telephone || dece.user.contact}
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

            // Afficher les informations dans une popup SweetAlert
            Swal.fire({
                title: 'Détails de Livraison',
                html: htmlContent,
                showCancelButton: true,
                confirmButtonText: 'Télécharger en PDF',
                cancelButtonText: 'Fermer',
                confirmButtonColor: '#1f4083',
                width: '600px',
                customClass: {
                    popup: 'delivery-info-popup'
                },
                didOpen: () => {
                    // Ajouter un style pour la popup
                    const popup = Swal.getPopup();
                    popup.style.borderRadius = '12px';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Rediriger vers la route de téléchargement
                    const url = downloadDeliveryInfoUrl.replace(':id', dece.id);
                    window.open(url, '_blank');
                }
            });
        }
    </script>
    <style>
        /* Styles pour le popup de détails des demandes */
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

        /* Styles pour la modal d'image */
        .image-modal-popup {
            border-radius: 12px;
        }

        .image-modal-popup .swal2-close {
            color: #666;
            font-size: 24px;
        }

        /* Styles pour les documents dans la popup */
        .document-item {
            margin-bottom: 15px;
            padding: 10px;
            background: white;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }

        .document-preview {
            max-width: 150px;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: transform 0.2s ease;
        }

        .document-preview:hover {
            transform: scale(1.05);
        }

        .document-actions {
            margin-top: 5px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .document-action-link {
            color: #1f4083;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }

        .document-action-link:hover {
            text-decoration: underline;
        }
    </style>
@endsection
