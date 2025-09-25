@extends('agent.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<style>
  :root {
    --primary-color: #1977cc;
    --primary-light: #1977cc;
    --primary-dark: #1977cc;
    --secondary-color: #1977cc;
    --secondary-light: #1977cc;
    --secondary-dark: #1977cc;
    --light-color: #ffffff;
    --dark-color: #212529;
    --gray-color: #6c757d;
    --light-gray: #f8f9fa;
    --border-radius: 12px;
    --box-shadow: 0 8px 20px rgba(0, 126, 0, 0.1);
    --transition: all 0.3s ease;
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
    overflow: hidden;
  }

  .table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
  }

  .table thead th {
    background-color: rgba(0, 126, 0, 0.05);
    color: var(--secondary-color);
    font-weight: 600;
    border: none;
    padding: 15px;
    vertical-align: middle;
    border-bottom: 2px solid rgba(0, 126, 0, 0.1);
  }

  .table tbody tr {
    transition: var(--transition);
  }

  .table tbody tr:hover {
    background-color: rgba(0, 126, 0, 0.03);
  }

  .table tbody td {
    padding: 15px;
    vertical-align: middle;
    border-top: 1px solid rgba(0, 126, 0, 0.05);
  }

  .badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
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
    border-radius: 20px;
    padding: 8px 15px;
    font-size: 0.9rem;
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
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 3px;
  }
  
  .badge{
    padding: 5px;
    color: white;
    border-radius: 50px;
    background-color: #1977cc;
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
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 10px;
  }
  .user-avatar1 {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--secondary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 10px;
  }

  .user-details {
    line-height: 1.2;
  }

  .user-name {
    font-weight: 600;
    color: var(--dark-color);
  }

  .user-email {
    font-size: 0.85rem;
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
        confirmButtonColor: '#1977cc',
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
        confirmButtonColor: '#1977cc',
        background: 'white'
      });
    </script>
  @endif

  <div class="page-title">
    <h2>
      <i class="fas fa-cross me-2"></i>Gestion des demandes d'extrait de décès
    </h2>
    <div class="page-actions">
      <a href="{{ route('agent.dashboard') }}" class="btn-action">
        <i class="fas fa-arrow-left me-1"></i>Retour
      </a>
    </div>
  </div>

  <!-- Statistiques -->
  <div class="stats-card">
    <div class="stats-icon">
      <i class="fas fa-tasks"></i>
    </div>
    <div class="stats-content">
      <h3>{{ $deces->total() }}</h3>
      <p>Demandes d'extraits de deces en cours</p>
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
              <th style="text-align: center">Demandeur</th>
              <th style="text-align: center">Nom complet du défunt</th>
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
                  @if($dece->choix_option == 'livraison' && $dece->etat == 'terminé')
                    <button class="download-btn" onclick="showDeliveryInfo({{ json_encode($dece) }})">
                      <i class="fas fa-download me-1"></i>Télécharger
                    </button>
                  @else
                    <span class="badge bg-secondary">N/A</span>
                  @endif
                </td>
                <td style="text-align: center" data-label="Demandeur">
                  <div class="user-info" style="text-align: center; display:flex; justify-content:center">
                    <div class="user-avatar" style="text-align: center">
                      {{ substr($dece->user->name, 0, 1).''.substr($dece->user->prenom, 0, 1) }}
                    </div>
                    <div class="user-details">
                      <div class="user-name">{{ $dece->user->name.' '.$dece->user->prenom }}</div>
                      <div class="user-email">{{ $dece->user->contact }}</div>
                      <div class="user-email">{{ $dece->user->email }}</div>
                    </div>
                  </div>
                </td>
                <td style="text-align: center" data-label="Demandeur">
                  <div class="user-info" style="text-align: center; display:flex; justify-content:center">
                    <div class="user-avatar1" style="text-align: center">
                      {{ substr($dece->name, 0, 1).''.substr($dece->prenom, 0, 1) }}
                    </div>
                    <div class="user-details">
                      <div class="user-name">{{ $dece->name.' '.$dece->prenom }}</div>
                    </div>
                  </div>
                </td>
                <td  style="text-align: center">{{ $dece->numberR }}</td>
                <td  style="text-align: center">{{ $dece->dateR }}</td>
                <td style="text-align: center" data-label="Date demande">{{ $dece->created_at->format('d/m/Y H:i') }}</td>
                {{-- <td style="text-align: center" data-label="Type">
                  @if($dece->type == 'simple')
                    <span class="badge-status badge-pending">Copie Simple</span>
                  @else
                    <span class="badge-status badge-completed">Copie Integrale</span>
                  @endif
                </td> --}}
                <td style="text-align: center">
                  @if($dece->CNIdfnt)
                    @php
                      $CNIPath = asset('storage/' . $dece->CNIdfnt);
                      $isCNIPdf = strtolower(pathinfo($CNIPath, PATHINFO_EXTENSION)) === 'pdf';
                    @endphp
                    @if ($isCNIPdf)
                      <a href="{{ $CNIPath }}" target="_blank" class="document-preview">
                        <img src="{{ asset('assets/assets/img/pdf.jpg') }}" style="width: 40px" alt="PDF" class="document-preview">
                      </a>
                    @else
                      <img src="{{ $CNIPath }}"
                        alt="CNI" 
                        class="document-preview"
                        data-bs-toggle="modal" 
                        data-bs-target="#imageModal" 
                        onclick="showImage(this)" 
                        onerror="this.onerror=null; this.src='{{ asset('assets/images/profiles/bébé.jpg') }}'">
                    @endif
                  @else
                    <span class="badge bg-secondary">N/A</span>
                  @endif
                  @if($dece->CNIdcl)
                    @php
                      $CNIdclPath = asset('storage/' . $dece->CNIdcl);
                      $isCNIdclPdf = strtolower(pathinfo($CNIdclPath, PATHINFO_EXTENSION)) === 'pdf';
                    @endphp
                    @if ($isCNIdclPdf)
                      <a href="{{ $CNIdclPath }}" target="_blank" class="document-preview">
                        <img src="{{ asset('assets/assets/img/pdf.jpg') }}" style="width: 40px" alt="PDF" class="document-preview">
                      </a>
                    @else
                      <img src="{{ $CNIdclPath }}"
                        alt="CNIdcl" 
                        class="document-preview"
                        data-bs-toggle="modal" 
                        data-bs-target="#imageModal" 
                        onclick="showImage(this)" 
                        onerror="this.onerror=null; this.src='{{ asset('assets/images/profiles/bébé.jpg') }}'">
                    @endif
                  @else
                    <span class="badge bg-secondary">N/A</span>
                  @endif
                  @if (!empty($dece->documentMariage))
                    @php
                        $documentMariagePath = asset('storage/' . $dece->documentMariage);
                        $isdocumentMariagePdf = strtolower(pathinfo($documentMariagePath, PATHINFO_EXTENSION)) === 'pdf';
                    @endphp

                    @if ($isdocumentMariagePdf)
                        <a href="{{ $documentMariagePath }}" target="_blank" class="document-preview">
                            <img src="{{ asset('assets/assets/img/pdf.jpg') }}" style="width: 40px" alt="PDF">
                        </a>
                    @else
                        <img src="{{ $documentMariagePath }}"
                            alt="documentMariage" 
                            class="document-preview"
                            data-bs-toggle="modal" 
                            data-bs-target="#imageModal" 
                            onclick="showImage(this)" 
                            onerror="this.onerror=null; this.src='{{ asset('assets/images/profiles/bébé.jpg') }}'">
                    @endif
                  @endif
                  @if (!empty($dece->RequisPolice))
                    @php
                        $RequisPolicePath = asset('storage/' . $dece->RequisPolice);
                        $isRequisPolicePdf = strtolower(pathinfo($RequisPolicePath, PATHINFO_EXTENSION)) === 'pdf';
                    @endphp

                    @if ($isRequisPolicePdf)
                        <a href="{{ $RequisPolicePath }}" target="_blank" class="document-preview">
                            <img src="{{ asset('assets/assets/img/pdf.jpg') }}" style="width: 40px" alt="PDF">
                        </a>
                    @else
                        <img src="{{ $RequisPolicePath }}"
                            alt="RequisPolice" 
                            class="document-preview"
                            data-bs-toggle="modal" 
                            data-bs-target="#imageModal" 
                            onclick="showImage(this)" 
                            onerror="this.onerror=null; this.src='{{ asset('assets/images/profiles/bébé.jpg') }}'">
                    @endif
                  @endif
                </td>
                <td style="text-align: center" data-label="Statut">
                  @if($dece->etat == 'en attente')
                    <span class="badge-status badge-pending">En attente</span>
                  @elseif($dece->etat == 'réçu')
                    <span class="badge-status badge-progress">En cours</span>
                  @else
                    <span class="badge-status badge-completed">Terminé</span>
                  @endif
                </td>
                <td style="text-align: center" data-label="Actions">
                  <a href="{{ route('agent.demandes.deces.edit', $dece->id) }}" class="btn-action btn-secondary btn-icon" title="Modifier l'état de la demande">
                    <i class="fas fa-edit"></i>
                  </a>
                </td>
                <td style="text-align: center">
                    <div class="d-flex justify-content-center gap-2">
                        @if($dece->choix_option == 'livraison')
                            <a href="#" class="delivery-badge badge" data-bs-toggle="modal" data-bs-target="#livraisonModal" onclick="showLivraisonModal({{ json_encode($dece) }})">
                                <i class="fas fa-truck"></i> Livraison
                            </a>
                        @else
                            @if($dece->etat !== 'terminé')
                              <span class="badge"><i class="fas fa-home"></i> Retrait sur place</span>
                            @endif
                            @if($dece->etat == 'terminé')
                                <button class="btn-action" onclick="markAsDelivered({{ $dece->id }})" title="Livré l'extrait">
                                    <i class="fas fa-file"></i>Retrait
                                </button>
                            @endif
                        @endif
                    </div>
                </td>
              </tr>
            @empty
              <tr>
                <td style="text-align: center" colspan="9" class="empty-state">
                  <i class="fas fa-cross"></i>
                  <h5>Aucune demande d'extrait de deces en cours</h5>
                  <p>Toutes les demandes sont traitées ou vous n'avez pas encore récuperer demande.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      @if($deces->hasPages())
        <div class="pagination-container">
          {{ $deces->links() }}
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
      if (window.innerWidth <= 768px) {
        // Ajout des data-labels pour l'affichage mobile
        $('table thead th').each(function() {
          const headerText = $(this).text();
          const columnIndex = $(this).index();
          $('table tbody tr td:nth-child(' + (columnIndex + 1) + ')').attr('data-label', headerText);
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
                        const errorMessage = xhr.responseJSON.error || 'Une erreur est survenue lors de la mise à jour.';
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
                <h3 style="color: #1977cc; margin-bottom: 20px;">Informations de Livraison</h3>
                
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
            confirmButtonColor: '#1977cc',
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
@endsection