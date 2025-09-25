<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <h5 style="color: var(--primary-color);">Informations de base</h5>
            <div class="detail-row">
                <span class="detail-label">Référence:</span>
                <span class="detail-value">{{ $demande->reference }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Type:</span>
                <span class="detail-value">{{ $demande->type_demande }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date création:</span>
                <span class="detail-value">{{ $demande->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">État:</span>
                <span class="badge badge-etat">{{ $demande->etat }}</span>
            </div>
        </div>
        
        <div class="col-md-6">
            <h5 style="color: var(--primary-color);">Informations financières</h5>
            <div class="detail-row">
                <span class="detail-label">Montant timbre:</span>
                <span class="detail-value">{{ $demande->montant_timbre }} FCFA</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Frais livraison:</span>
                <span class="detail-value">{{ $demande->montant_livraison }} FCFA</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total:</span>
                <span class="detail-value font-weight-bold">{{ $demande->montant_timbre + $demande->montant_livraison }} FCFA</span>
            </div>
        </div>
    </div>
    
    <hr>
    
    <div class="row">
        <div class="col-md-12">
            <h5 style="color: var(--primary-color);">Détails de livraison</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-row">
                        <span class="detail-label">Option:</span>
                        <span class="detail-value">{{ $demande->choix_option === 'livraison' ? 'Livraison' : 'Retrait' }}</span>
                    </div>
                    
                    @if($demande->choix_option === 'livraison')
                        <div class="detail-row">
                            <span class="detail-label">Destinataire:</span>
                            <span class="detail-value">{{ $demande->nom_destinataire }} {{ $demande->prenom_destinataire }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Contact:</span>
                            <span class="detail-value">{{ $demande->contact_destinataire }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value">{{ $demande->email_destinataire ?? 'N/A' }}</span>
                        </div>
                    @else
                        <div class="detail-row">
                            <span class="detail-label">Lieu de retrait:</span>
                            <span class="detail-value">{{ $demande->commune ?? 'Non précisé' }}</span>
                        </div>
                    @endif
                </div>
                
                <div class="col-md-6">
                    @if($demande->choix_option === 'livraison')
                        <div class="detail-row">
                            <span class="detail-label">Adresse:</span>
                            <span class="detail-value">{{ $demande->adresse_livraison }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Ville:</span>
                            <span class="detail-value">{{ $demande->ville }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Commune:</span>
                            <span class="detail-value">{{ $demande->commune_livraison }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Quartier:</span>
                            <span class="detail-value">{{ $demande->quartier }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .detail-row {
        display: flex;
        margin-bottom: 0.8rem;
    }
    
    .detail-label {
        font-weight: 600;
        color: var(--primary-color);
        min-width: 150px;
    }
    
    .detail-value {
        flex: 1;
    }
    
    .badge-etat {
        background-color: #f9cf03;
        color: #06634e;
        padding: 0.3rem 0.6rem;
        border-radius: 4px;
        font-weight: 600;
    }
    
    hr {
        border-top: 1px solid rgba(6, 99, 78, 0.1);
        margin: 1.5rem 0;
    }
</style>