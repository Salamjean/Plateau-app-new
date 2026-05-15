@extends('agent.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .detail-container { padding: 2rem; max-width: 1400px; margin: 0 auto; }
    .header-card { background: linear-gradient(135deg, #FF3B67 0%, #ff7ba0 100%); color: #fff; padding: 2rem; border-radius: 20px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(255,59,103,0.2); }
    .header-card h1 { font-size: 1.6rem; font-weight: 800; margin: 0 0 8px 0; }
    .header-meta { display: flex; gap: 24px; flex-wrap: wrap; opacity: 0.95; font-size: 0.9rem; }
    .header-meta span { display: flex; align-items: center; gap: 6px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
    @media (max-width: 768px) { .info-grid { grid-template-columns: 1fr; } }
    .info-card { background: #fff; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
    .info-card h3 { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; color: #FF3B67; font-weight: 800; margin: 0 0 1rem 0; border-bottom: 2px solid #f4f7fe; padding-bottom: 8px; }
    .info-row { padding: 6px 0; font-size: 0.9rem; }
    .info-row strong { color: #2b3674; min-width: 140px; display: inline-block; }
    .lignes-section h2 { font-size: 1.4rem; font-weight: 800; color: #2b3674; margin-bottom: 1rem; }
    .ligne-card { background: #fff; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.04); border-left: 5px solid #FF3B67; }
    .ligne-card.simple { border-left-color: #01B574; }
    .ligne-card.integral { border-left-color: #FF3B67; }
    .ligne-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem; }
    .ligne-title { font-weight: 800; color: #2b3674; font-size: 1.05rem; display: flex; align-items: center; gap: 12px; }
    .ligne-badge { padding: 5px 12px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
    .ligne-badge.simple { background: rgba(1,181,116,0.1); color: #01B574; }
    .ligne-badge.integral { background: rgba(255,59,103,0.1); color: #FF3B67; }
    .ligne-body { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; }
    @media (max-width: 768px) { .ligne-body { grid-template-columns: 1fr; } }
    .ligne-info-row { padding: 4px 0; font-size: 0.9rem; }
    .ligne-info-row strong { color: #2b3674; min-width: 120px; display: inline-block; }
    .cni-preview { background: #f4f7fe; border-radius: 12px; padding: 1rem; text-align: center; }
    .cni-preview img { max-width: 100%; border-radius: 8px; max-height: 150px; }
    .ligne-decision { background: #f8fafc; padding: 1rem; border-radius: 12px; margin-top: 1rem; }
    .decision-options { display: flex; gap: 1rem; flex-wrap: wrap; }
    .decision-option { flex: 1; min-width: 150px; padding: 14px 18px; border-radius: 12px; border: 2px solid #e2e8f0; background: #fff; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 700; font-size: 0.9rem; }
    .decision-option input { display: none; }
    .decision-option.valider { color: #01B574; }
    .decision-option.rejeter { color: #EE5D50; }
    .decision-option:has(input:checked) { border-color: currentColor; background: rgba(0,0,0,0.02); }
    .motif-input { margin-top: 1rem; display: none; }
    .motif-input textarea { width: 100%; padding: 12px; border-radius: 10px; border: 2px solid #e2e8f0; min-height: 80px; }
    .global-actions { background: linear-gradient(135deg, #2b3674 0%, #FF3B67 100%); color: #fff; padding: 1.5rem 2rem; border-radius: 16px; margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
    .btn-submit-all { background: #fff; color: #2b3674; padding: 14px 28px; border-radius: 12px; font-weight: 800; border: none; cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; gap: 10px; }
    .btn-submit-all:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
    .alert-warning { background: #fff3cd; border-left: 5px solid #ffa500; color: #856404; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-size: 0.95rem; }
    .badge-etat { padding: 6px 14px; border-radius: 50px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; }
    .badge-etat.attente { background: #fff3cd; color: #856404; }
    .badge-etat.termine { background: #d4edda; color: #155724; }
    .badge-etat.rejetee { background: #f8d7da; color: #721c24; }
    .back-link { display: inline-flex; align-items: center; gap: 8px; color: #FF3B67; text-decoration: none; font-weight: 700; margin-bottom: 1rem; }
</style>

<div class="detail-container">
    <a href="{{ route('agent.demandes.mariage.groupes.index') }}" class="back-link"><i class="fas fa-arrow-left"></i> Retour à la liste</a>

    <div class="header-card">
        <h1><i class="fas fa-shopping-basket"></i> Demande groupée Mariage — {{ $groupe->reference }}</h1>
        <div class="header-meta">
            <span><i class="fas fa-clock"></i> {{ $groupe->created_at->format('d/m/Y à H:i') }}</span>
            <span><i class="fas fa-user"></i> {{ $groupe->user->name ?? '' }} {{ $groupe->user->prenom ?? '' }}</span>
            <span><i class="fas fa-{{ $groupe->choix_option == 'livraison' ? 'motorcycle' : 'university' }}"></i> {{ $groupe->choix_option == 'livraison' ? 'Livraison' : 'Retrait sur place' }}</span>
            <span><i class="fas fa-coins"></i> {{ number_format($groupe->montant_total, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    @if($groupe->etat == 'terminé' || $groupe->etat == 'rejetée')
        <div class="alert-warning"><i class="fas fa-info-circle"></i><div>Cette demande a déjà été traitée — état : <strong>{{ $groupe->etat }}</strong>.</div></div>
    @endif

    <div class="info-grid">
        <div class="info-card">
            <h3><i class="fas fa-user"></i> Demandeur</h3>
            <div class="info-row"><strong>Nom :</strong> {{ $groupe->user->name }} {{ $groupe->user->prenom }}</div>
            <div class="info-row"><strong>Téléphone :</strong> {{ $groupe->user->contact ?? '—' }}</div>
            <div class="info-row"><strong>Email :</strong> {{ $groupe->user->email ?? '—' }}</div>
        </div>

        @if($groupe->choix_option == 'livraison')
            <div class="info-card">
                <h3><i class="fas fa-motorcycle"></i> Livraison</h3>
                <div class="info-row"><strong>Destinataire :</strong> {{ $groupe->nom_destinataire }} {{ $groupe->prenom_destinataire }}</div>
                <div class="info-row"><strong>Téléphone :</strong> {{ $groupe->contact_destinataire }}</div>
                <div class="info-row"><strong>Adresse :</strong> {{ $groupe->adresse_livraison }}</div>
                @if($groupe->livraison_code)<div class="info-row" style="margin-top: 10px; padding: 8px; background: #d4edda; border-radius: 8px;"><strong>Code de livraison :</strong> <code>{{ $groupe->livraison_code }}</code></div>@endif
            </div>
        @else
            <div class="info-card"><h3><i class="fas fa-university"></i> Retrait sur place</h3><div class="info-row">L'utilisateur viendra récupérer ses actes en mairie.</div></div>
        @endif
    </div>

    <div class="lignes-section">
        <h2><i class="fas fa-list"></i> Actes demandés ({{ $groupe->lignes->count() }})</h2>

        @if($groupe->etat != 'terminé' && $groupe->etat != 'rejetée')
            <div class="alert-warning"><i class="fas fa-info-circle"></i><div><strong>Tout ou rien :</strong> Si au moins un acte est rejeté, toute la demande sera rejetée.</div></div>
        @endif

        <form id="processForm" action="{{ route('agent.demandes.mariage.groupe.process', $groupe->id) }}" method="POST">
            @csrf
            @foreach($groupe->lignes as $ligne)
                @php $isIntegral = $ligne->type_document == 'extrait_integral'; @endphp
                <div class="ligne-card {{ $isIntegral ? 'integral' : 'simple' }}">
                    <div class="ligne-header">
                        <div class="ligne-title"><i class="fas fa-{{ $isIntegral ? 'file-contract' : 'file-alt' }}"></i> Mariage n°{{ $ligne->position_in_groupe }}<span class="ligne-badge {{ $isIntegral ? 'integral' : 'simple' }}">{{ $isIntegral ? 'Intégral' : 'Simple' }}</span></div>
                        @if($ligne->etat == 'terminé')<span class="badge-etat termine">Validé</span>@elseif($ligne->etat == 'rejetée')<span class="badge-etat rejetee">Rejeté</span>@else<span class="badge-etat attente">En attente</span>@endif
                    </div>
                    <div class="ligne-body">
                        <div>
                            <div class="ligne-info-row"><strong>Époux :</strong> {{ $ligne->nomEpoux }} {{ $ligne->prenomEpoux }}</div>
                            <div class="ligne-info-row"><strong>Date naissance :</strong> {{ $ligne->dateNaissanceEpoux ?? '—' }}</div>
                            <div class="ligne-info-row"><strong>Lieu naissance :</strong> {{ $ligne->lieuNaissanceEpoux ?? '—' }}</div>
                            <div class="ligne-info-row"><strong>Commune mariage :</strong> {{ $ligne->commune_mariage ?? '—' }}</div>
                            @if($ligne->etat == 'rejetée' && $ligne->motif_de_rejet)<div style="margin-top: 1rem; padding: 12px; background: #f8d7da; border-radius: 8px; color: #721c24;"><strong>Motif :</strong><br>{{ $ligne->motif_de_rejet }}</div>@endif
                        </div>
                        <div class="cni-preview">
                            <strong style="display:block; margin-bottom: 8px;">Pièce d'identité :</strong>
                            @if($ligne->pieceIdentite)
                                @php $url = asset('storage/' . $ligne->pieceIdentite); $isPdf = strtolower(pathinfo($url, PATHINFO_EXTENSION)) === 'pdf'; @endphp
                                @if($isPdf)<a href="{{ $url }}" target="_blank"><img src="{{ asset('assets/assets/img/pdf.jpg') }}" style="width: 80px;"><br>Ouvrir PDF</a>@else<a href="{{ $url }}" target="_blank"><img src="{{ $url }}"><br>Voir</a>@endif
                            @else<em>Aucune pièce</em>@endif

                            @if($ligne->extraitMariage)
                                <hr style="margin: 12px 0;">
                                <strong style="display:block; margin-bottom: 8px;">Extrait mariage :</strong>
                                @php $url2 = asset('storage/' . $ligne->extraitMariage); $isPdf2 = strtolower(pathinfo($url2, PATHINFO_EXTENSION)) === 'pdf'; @endphp
                                @if($isPdf2)<a href="{{ $url2 }}" target="_blank"><img src="{{ asset('assets/assets/img/pdf.jpg') }}" style="width: 60px;"><br>Ouvrir</a>@else<a href="{{ $url2 }}" target="_blank"><img src="{{ $url2 }}" style="max-height:100px;"><br>Voir</a>@endif
                            @endif
                        </div>
                    </div>

                    @if($groupe->etat != 'terminé' && $groupe->etat != 'rejetée')
                        <div class="ligne-decision">
                            <strong style="color: #2b3674; display: block; margin-bottom: 10px;">Votre décision :</strong>
                            <div class="decision-options">
                                <label class="decision-option valider"><input type="radio" name="decisions[{{ $ligne->id }}]" value="valider" onchange="onDecisionChange(this, {{ $ligne->id }})"><i class="fas fa-check-circle"></i> Valider</label>
                                <label class="decision-option rejeter"><input type="radio" name="decisions[{{ $ligne->id }}]" value="rejeter" onchange="onDecisionChange(this, {{ $ligne->id }})"><i class="fas fa-times-circle"></i> Rejeter</label>
                            </div>
                            <div class="motif-input" id="motif-{{ $ligne->id }}">
                                <label style="display:block; margin-top: 1rem; margin-bottom: 6px; font-weight: 700; color: #EE5D50;">Motif du rejet :</label>
                                <textarea name="motifs[{{ $ligne->id }}]" placeholder="Ex: Document illisible, infos incorrectes..."></textarea>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            @if($groupe->etat != 'terminé' && $groupe->etat != 'rejetée')
                <div class="global-actions">
                    <div><strong style="font-size: 1.1rem;">Soumettre votre décision finale</strong></div>
                    <button type="submit" class="btn-submit-all"><i class="fas fa-paper-plane"></i> Soumettre</button>
                </div>
                <div class="info-card" style="margin-top: 1rem;">
                    <h3><i class="fas fa-comment"></i> Commentaire général (optionnel)</h3>
                    <textarea name="motif_global" placeholder="Commentaire global ajouté à tous les motifs." style="width: 100%; padding: 10px; border-radius: 8px; border: 2px solid #f4f7fe; min-height: 60px;"></textarea>
                </div>
            @endif
        </form>
    </div>
</div>

<script>
    function onDecisionChange(input, ligneId) {
        document.getElementById('motif-' + ligneId).style.display = input.value === 'rejeter' ? 'block' : 'none';
    }
    document.getElementById('processForm')?.addEventListener('submit', function(e) {
        const decisions = document.querySelectorAll('input[name^="decisions"]:checked');
        const totalLignes = {{ $groupe->lignes->count() }};
        if (decisions.length < totalLignes) {
            e.preventDefault();
            alert(`Veuillez décider pour TOUS les actes (${decisions.length}/${totalLignes}).`);
            return;
        }
        const aRejeter = Array.from(decisions).some(d => d.value === 'rejeter');
        const message = aRejeter ? `Au moins un acte est rejeté. TOUTE la demande sera REJETÉE.\n\nConfirmer ?` : `Tous les actes sont validés. La demande sera TERMINÉE.\n\nConfirmer ?`;
        if (!confirm(message)) e.preventDefault();
    });
</script>
@endsection
