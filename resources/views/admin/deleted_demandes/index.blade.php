@extends('admin.layouts.template')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --admin-primary: #6777ef;
        --admin-bg: #f4f6f9;
        --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
    }

    .page-header {
        background: var(--admin-primary);
        border-radius: 12px;
        color: white;
        padding: 25px 30px;
        box-shadow: 0 10px 20px rgba(43, 52, 82, 0.15);
        margin-bottom: 30px;
    }

    .modern-table-container {
        background: white;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .modern-table-header {
        background: #fff;
        padding: 20px 25px;
        border-bottom: 1px solid #f0f0f0;
    }

    .modern-table th {
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #888;
        padding: 15px 20px;
        border-bottom: 2px solid #f4f6f9;
    }

    .modern-table td {
        padding: 15px 20px;
        vertical-align: middle;
        font-weight: 600;
        color: #444;
        border-bottom: 1px solid #f8f9fa;
    }
    
    pre.json-view {
        background: #f4f6f9;
        padding: 15px;
        border-radius: 8px;
        font-size: 14px;
        color: #333;
        overflow-x: auto;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1"><i class="fas fa-trash me-2 text-white-50"></i> Archives des demandes supprimées</h2>
            <p class="mb-0 text-white-50">Historique des demandes (Naissances, Mariages, Décès) supprimées par les utilisateurs.</p>
        </div>
    </div>

    <!-- Table -->
    <div class="modern-table-container">
        <div class="modern-table-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-list me-2 text-primary"></i>Toutes les archives</h5>
            <span class="badge bg-light text-dark border px-3 py-2">{{ $deletedDemandes->total() }} résultats</span>
        </div>
        <div class="table-responsive">
            <table class="table modern-table mb-0 text-center">
                <thead>
                    <tr>
                        <th>Date de suppression</th>
                        <th>Type de demande</th>
                        <th>ID Original</th>
                        <th>ID Utilisateur</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deletedDemandes as $demande)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $demande->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted small">{{ $demande->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                @php
                                    $type = str_replace('App\\Models\\', '', $demande->type_demande);
                                @endphp
                                <span class="badge bg-primary px-3 py-2 rounded-pill">{{ $type }}</span>
                            </td>
                            <td>#{{ $demande->original_id }}</td>
                            <td>
                                @if($demande->user_id)
                                    Utilisateur #{{ $demande->user_id }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modal-{{ $demande->id }}">
                                    <i class="fas fa-eye me-1"></i> Détails
                                </button>
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="modal-{{ $demande->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fw-bold">
                                            <i class="fas fa-file-alt text-primary me-2"></i> Détails de la demande #{{ $demande->original_id }} ({{ $type }})
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-1"></i> Voici les données complètes de la demande au moment de sa suppression.
                                        </div>
                                        <pre class="json-view">{{ json_encode($demande->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted py-4">
                                <i class="fas fa-box-open fa-3x mb-3 text-light"></i>
                                <h5>Aucune demande supprimée trouvée</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($deletedDemandes->hasPages())
            <div class="p-4 border-top bg-light d-flex justify-content-end">
                {{ $deletedDemandes->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Make sure Bootstrap JS is loaded for modals -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
