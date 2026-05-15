@extends('agent.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .detail-container { padding: 2rem; max-width: 1400px; margin: 0 auto; }
    .header-card {
        background: linear-gradient(135deg, #1977cc 0%, #4facfe 100%);
        color: #fff; padding: 2rem; border-radius: 20px; margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(25,119,204,0.2);
    }
    .header-card h1 { font-size: 1.6rem; font-weight: 800; margin: 0 0 8px 0; }
    .header-meta { display: flex; gap: 24px; flex-wrap: wrap; opacity: 0.95; font-size: 0.9rem; }
    .header-meta span { display: flex; align-items: center; gap: 6px; }

    .info-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;
    }
    @media (max-width: 768px) { .info-grid { grid-template-columns: 1fr; } }

    .info-card {
        background: #fff; border-radius: 16px; padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    }
    .info-card h3 {
        font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;
        color: #1977cc; font-weight: 800; margin: 0 0 1rem 0;
        border-bottom: 2px solid #f4f7fe; padding-bottom: 8px;
    }
    .info-row { padding: 6px 0; font-size: 0.9rem; }
    .info-row strong { color: #2b3674; min-width: 140px; display: inline-block; }

    .lignes-section { margin-top: 2rem; }
    .lignes-section h2 { font-size: 1.4rem; font-weight: 800; color: #2b3674; margin-bottom: 1rem; }

    .ligne-card {
        background: #fff; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04); border-left: 5px solid #1977cc;
        position: relative;
    }
    .ligne-card.simple { border-left-color: #01B574; }
    .ligne-card.integral { border-left-color: #FF3B67; }

    .ligne-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem; }
    .ligne-title { font-weight: 800; color: #2b3674; font-size: 1.05rem; display: flex; align-items: center; gap: 12px; }
    .ligne-badge {
        padding: 5px 12px; border-radius: 50px; font-size: 0.7rem;
        font-weight: 700; text-transform: uppercase;
    }
    .ligne-badge.simple { background: rgba(1,181,116,0.1); color: #01B574; }
    .ligne-badge.integral { background: rgba(255,59,103,0.1); color: #FF3B67; }

    .ligne-body { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; }
    @media (max-width: 768px) { .ligne-body { grid-template-columns: 1fr; } }

    .ligne-info { font-size: 0.9rem; }
    .ligne-info-row { padding: 4px 0; }
    .ligne-info-row strong { color: #2b3674; min-width: 120px; display: inline-block; }

    .cni-preview {
        background: #f4f7fe; border-radius: 12px; padding: 1rem; text-align: center;
    }
    .cni-preview img { max-width: 100%; border-radius: 8px; }
    .cni-preview a { color: #1977cc; font-weight: 700; text-decoration: none; }

    .ligne-decision {
        background: #f8fafc; padding: 1rem; border-radius: 12px; margin-top: 1rem;
    }
    .decision-options { display: flex; gap: 1rem; flex-wrap: wrap; }
    .decision-option {
        flex: 1; min-width: 150px; padding: 14px 18px; border-radius: 12px;
        border: 2px solid #e2e8f0; background: #fff; cursor: pointer; transition: 0.2s;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        font-weight: 700; font-size: 0.9rem;
    }
    .decision-option input { display: none; }
    .decision-option.valider { color: #01B574; }
    .decision-option.rejeter { color: #EE5D50; }
    .decision-option:has(input:checked) {
        border-color: currentColor; background: rgba(0,0,0,0.02);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .motif-input {
        margin-top: 1rem; display: none;
    }
    .motif-input textarea {
        width: 100%; padding: 12px; border-radius: 10px;
        border: 2px solid #e2e8f0; font-family: inherit; resize: vertical;
        min-height: 80px;
    }

    .global-actions {
        background: linear-gradient(135deg, #2b3674 0%, #1977cc 100%);
        color: #fff; padding: 1.5rem 2rem; border-radius: 16px;
        margin-top: 2rem; display: flex; justify-content: space-between;
        align-items: center; flex-wrap: wrap; gap: 1rem;
    }
    .btn-submit-all {
        background: #fff; color: #2b3674; padding: 14px 28px;
        border-radius: 12px; font-weight: 800; border: none; cursor: pointer;
        transition: 0.3s; display: inline-flex; align-items: center; gap: 10px;
    }
    .btn-submit-all:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
    .motif-global {
        margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.1);
        border-radius: 12px;
    }
    .motif-global label { display: block; margin-bottom: 8px; font-weight: 700; }
    .motif-global textarea {
        width: 100%; padding: 10px; border-radius: 8px; border: none;
        background: #fff; color: #2b3674; resize: vertical; min-height: 60px;
    }

    .alert-warning {
        background: #fff3cd; border-left: 5px solid #ffa500; color: #856404;
        padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 1.5rem;
        display: flex; align-items: center; gap: 12px; font-size: 0.95rem;
    }

    .badge-etat { padding: 6px 14px; border-radius: 50px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; }
    .badge-etat.attente { background: #fff3cd; color: #856404; }
    .badge-etat.termine { background: #d4edda; color: #155724; }
    .badge-etat.rejetee { background: #f8d7da; color: #721c24; }

    .back-link {
        display: inline-flex; align-items: center; gap: 8px;
        color: #1977cc; text-decoration: none; font-weight: 700;
        margin-bottom: 1rem;
    }
    .back-link:hover { color: #0d4a85; }
</style>

<div class="detail-container">
    <a href="{{ route('agent.demandes.naissance.groupes.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Retour à la liste des groupes
    </a>

    <div class="header-card">
        <h1><i class="fas fa-shopping-basket"></i> Demande groupée — {{ $groupe->reference }}</h1>
        <div class="header-meta">
            <span><i class="fas fa-clock"></i> Créée le {{ $groupe->created_at->format('d/m/Y à H:i') }}</span>
            <span><i class="fas fa-user"></i> {{ $groupe->user->name ?? '' }} {{ $groupe->user->prenom ?? '' }}</span>
            <span><i class="fas fa-{{ $groupe->choix_option == 'livraison' ? 'motorcycle' : 'university' }}"></i>
                {{ $groupe->choix_option == 'livraison' ? 'Livraison' : 'Retrait sur place' }}
            </span>
            <span><i class="fas fa-coins"></i> Total : {{ number_format($groupe->montant_total, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    @if($groupe->etat == 'terminé' || $groupe->etat == 'rejetée')
        <div class="alert-warning">
            <i class="fas fa-info-circle" style="font-size: 1.2rem;"></i>
            <div>
                Cette demande groupée a déjà été traitée — état : <strong>{{ $groupe->etat }}</strong>.
                Vous pouvez consulter les détails ci-dessous.
            </div>
        </div>
    @endif

    <!-- Infos demandeur + livraison -->
    <div class="info-grid">
        <div class="info-card">
            <h3><i class="fas fa-user"></i> Demandeur</h3>
            <div class="info-row"><strong>Nom :</strong> {{ $groupe->user->name }} {{ $groupe->user->prenom }}</div>
            <div class="info-row"><strong>Téléphone :</strong> {{ $groupe->user->contact ?? '—' }}</div>
            <div class="info-row"><strong>Email :</strong> {{ $groupe->user->email ?? '—' }}</div>
            <div class="info-row"><strong>Commune :</strong> {{ $groupe->commune }}</div>
        </div>

        @if($groupe->choix_option == 'livraison')
            <div class="info-card">
                <h3><i class="fas fa-motorcycle"></i> Livraison</h3>
                <div class="info-row"><strong>Destinataire :</strong> {{ $groupe->nom_destinataire }} {{ $groupe->prenom_destinataire }}</div>
                <div class="info-row"><strong>Téléphone :</strong> {{ $groupe->contact_destinataire }}</div>
                <div class="info-row"><strong>Adresse :</strong> {{ $groupe->adresse_livraison }}</div>
                <div class="info-row"><strong>Ville :</strong> {{ $groupe->ville }}, {{ $groupe->commune_livraison }}</div>
                @if($groupe->livraison_code)
                    <div class="info-row" style="margin-top: 10px; padding: 8px; background: #d4edda; border-radius: 8px;">
                        <strong>Code de livraison :</strong> <code>{{ $groupe->livraison_code }}</code>
                    </div>
                @endif
            </div>
        @else
            <div class="info-card">
                <h3><i class="fas fa-university"></i> Retrait sur place</h3>
                <div class="info-row">L'utilisateur viendra récupérer ses actes en mairie.</div>
                <div class="info-row" style="margin-top: 10px; padding: 8px; background: #f4f7fe; border-radius: 8px; font-size: 0.85rem;">
                    <i class="fas fa-info-circle"></i> Les timbres sont à délivrer au guichet une fois la demande validée.
                </div>
            </div>
        @endif
    </div>

    <!-- Lignes du groupe -->
    <div class="lignes-section">
        <h2><i class="fas fa-list"></i> Actes demandés ({{ $groupe->lignes->count() }})</h2>

        @if($groupe->etat != 'terminé' && $groupe->etat != 'rejetée')
            <div class="alert-warning">
                <i class="fas fa-info-circle" style="font-size: 1.2rem;"></i>
                <div>
                    <strong>Logique "tout ou rien" :</strong>
                    Pour chaque acte, choisissez "Valider" ou "Rejeter".
                    Si <strong>au moins un</strong> acte est rejeté, <strong>toute la demande</strong> sera rejetée.
                    Sinon, tous les actes passeront à l'état "terminé".
                </div>
            </div>
        @endif

        <form id="processForm" action="{{ route('agent.demandes.naissance.groupe.process', $groupe->id) }}" method="POST">
            @csrf

            @foreach($groupe->lignes as $ligne)
                @php
                    $isIntegral = $ligne->type_document == 'extrait_integral';
                @endphp
                <div class="ligne-card {{ $isIntegral ? 'integral' : 'simple' }}">
                    <div class="ligne-header">
                        <div class="ligne-title">
                            <i class="fas fa-{{ $isIntegral ? 'file-contract' : 'file-alt' }}"></i>
                            Acte n°{{ $ligne->position_in_groupe }}
                            <span class="ligne-badge {{ $isIntegral ? 'integral' : 'simple' }}">
                                {{ $isIntegral ? 'Intégral' : 'Simple' }}
                            </span>
                        </div>
                        @if($ligne->etat == 'terminé')
                            <span class="badge-etat termine">Validé</span>
                        @elseif($ligne->etat == 'rejetée')
                            <span class="badge-etat rejetee">Rejeté</span>
                        @else
                            <span class="badge-etat attente">En attente</span>
                        @endif
                    </div>

                    <div class="ligne-body">
                        <div class="ligne-info">
                            <div class="ligne-info-row"><strong>Nom :</strong> {{ $ligne->name }} {{ $ligne->prenom }}</div>
                            <div class="ligne-info-row"><strong>Lieu naissance :</strong> {{ $ligne->commune_naissance ?? '—' }}</div>
                            <div class="ligne-info-row"><strong>N° Registre :</strong> {{ $ligne->number ?? '—' }}</div>
                            <div class="ligne-info-row"><strong>Date Registre :</strong> {{ $ligne->DateR ?? '—' }}</div>
                            @if($isIntegral)
                                <div class="ligne-info-row"><strong>Père :</strong> {{ $ligne->nom_prenoms_pere ?? '—' }}</div>
                                <div class="ligne-info-row"><strong>Mère :</strong> {{ $ligne->nom_prenoms_mere ?? '—' }}</div>
                            @endif
                            @if($ligne->etat == 'rejetée' && $ligne->motif_de_rejet)
                                <div style="margin-top: 1rem; padding: 12px; background: #f8d7da; border-radius: 8px; color: #721c24;">
                                    <strong>Motif de rejet :</strong><br>{{ $ligne->motif_de_rejet }}
                                </div>
                            @endif
                        </div>

                        <div class="cni-preview">
                            <strong style="display:block; margin-bottom: 8px; color: #2b3674;">Pièce d'identité :</strong>
                            @if($ligne->CNI)
                                @php
                                    $cniUrl = asset('storage/' . $ligne->CNI);
                                    $isPdf = strtolower(pathinfo($cniUrl, PATHINFO_EXTENSION)) === 'pdf';
                                @endphp
                                @if($isPdf)
                                    <a href="{{ $cniUrl }}" target="_blank">
                                        <img src="{{ asset('assets/assets/img/pdf.jpg') }}" alt="PDF" style="width: 80px;">
                                        <br>Ouvrir PDF
                                    </a>
                                @else
                                    <a href="{{ $cniUrl }}" target="_blank">
                                        <img src="{{ $cniUrl }}" alt="CNI" style="max-height: 150px;">
                                        <br>Voir en grand
                                    </a>
                                @endif
                            @else
                                <em style="color: #a3aed0;">Aucune pièce</em>
                            @endif
                        </div>
                    </div>

                    @if($groupe->etat != 'terminé' && $groupe->etat != 'rejetée')
                        <div class="ligne-decision">
                            <strong style="color: #2b3674; display: block; margin-bottom: 10px;">Votre décision :</strong>
                            <div class="decision-options">
                                <label class="decision-option valider">
                                    <input type="radio" name="decisions[{{ $ligne->id }}]" value="valider" onchange="onDecisionChange(this, {{ $ligne->id }})">
                                    <i class="fas fa-check-circle"></i> Valider cet acte
                                </label>
                                <label class="decision-option rejeter">
                                    <input type="radio" name="decisions[{{ $ligne->id }}]" value="rejeter" onchange="onDecisionChange(this, {{ $ligne->id }})">
                                    <i class="fas fa-times-circle"></i> Rejeter cet acte
                                </label>
                            </div>
                            <div class="motif-input" id="motif-{{ $ligne->id }}">
                                <label style="display:block; margin-top: 1rem; margin-bottom: 6px; font-weight: 700; color: #EE5D50;">
                                    Motif du rejet pour cet acte :
                                </label>
                                <textarea name="motifs[{{ $ligne->id }}]" placeholder="Ex: La pièce d'identité est illisible, le numéro de registre ne correspond pas, etc."></textarea>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            @if($groupe->etat != 'terminé' && $groupe->etat != 'rejetée')
                <div class="global-actions">
                    <div>
                        <strong style="font-size: 1.1rem;">Soumettre votre décision finale</strong><br>
                        <small style="opacity: 0.85;">Toute la demande sera traitée selon vos choix.</small>
                    </div>
                    <button type="submit" class="btn-submit-all" id="btn-submit">
                        <i class="fas fa-paper-plane"></i> Soumettre la décision
                    </button>
                </div>

                <div class="motif-global" style="margin-top: 1rem;">
                    <div class="info-card">
                        <h3><i class="fas fa-comment"></i> Commentaire général (optionnel)</h3>
                        <textarea name="motif_global" placeholder="Commentaire global qui sera ajouté à tous les motifs de rejet."
                                  style="width: 100%; padding: 10px; border-radius: 8px; border: 2px solid #f4f7fe; min-height: 60px;"></textarea>
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>

<script>
    function onDecisionChange(input, ligneId) {
        const motifBlock = document.getElementById('motif-' + ligneId);
        if (input.value === 'rejeter') {
            motifBlock.style.display = 'block';
        } else {
            motifBlock.style.display = 'none';
        }
    }

    // Confirmation avant soumission
    document.getElementById('processForm')?.addEventListener('submit', function(e) {
        const decisions = document.querySelectorAll('input[name^="decisions"]:checked');
        const totalLignes = {{ $groupe->lignes->count() }};

        if (decisions.length < totalLignes) {
            e.preventDefault();
            alert(`Veuillez prendre une décision pour TOUS les actes (${decisions.length}/${totalLignes} actuellement).`);
            return;
        }

        const aRejeter = Array.from(decisions).some(d => d.value === 'rejeter');
        const message = aRejeter
            ? `Au moins un acte est marqué "Rejeter".\n\nToute la demande groupée sera REJETÉE et l'utilisateur sera notifié.\n\nConfirmer ?`
            : `Tous les actes sont marqués "Valider".\n\nLa demande groupée sera marquée comme TERMINÉE.\n\nConfirmer ?`;

        if (!confirm(message)) {
            e.preventDefault();
        }
    });
</script>
@endsection
