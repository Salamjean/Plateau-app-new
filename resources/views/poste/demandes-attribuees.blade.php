@extends('poste.layouts.template')

@section('content')
    <style>
        :root {
            --primary: #1f4083;
            --secondary: #ea8c51;
            --success: #10b981;
            --warning: #f59e0b;
            --text-main: #2d3748;
            --text-muted: #718096;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
        }

        .page-title i {
            background: rgba(31, 64, 131, 0.1);
            padding: 8px;
            border-radius: 10px;
            color: var(--primary);
        }

        .action-bar {
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #edf2f7;
        }

        .assign-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .select-livreur {
            padding: 8px 12px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            min-width: 250px;
            outline: none;
            transition: border-color 0.2s;
        }

        .select-livreur:focus {
            border-color: var(--primary);
        }

        .demandes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .demande-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .demande-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .demande-card.selected {
            border-color: var(--secondary);
            background: rgba(234, 140, 81, 0.02);
        }

        .selection-badge i {
            display: none;
        }

        .demande-card.selected .selection-badge {
            background: var(--secondary);
            border-color: var(--secondary);
            color: white;
        }

        .demande-card.selected .selection-badge i {
            display: block;
        }

        .card-accent {
            height: 4px;
            background: var(--primary);
        }

        .card-header {
            padding: 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ref-badge {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--primary);
            background: rgba(31, 64, 131, 0.05);
            padding: 4px 10px;
            border-radius: 6px;
        }

        .type-badge {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 20px;
            background: var(--secondary);
            color: white;
        }

        .card-body {
            padding: 1.25rem;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 10px;
        }

        .info-row i {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .info-content {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .info-value {
            font-size: 0.9rem;
            color: var(--text-main);
            font-weight: 600;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-top: 5px;
        }

        .status-pending {
            background: #fff7ed;
            color: #c2410c;
        }

        .status-assigned {
            background: #eff6ff;
            color: #1e40af;
        }

        .btn-submit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-submit:hover:not(:disabled) {
            background: #163266;
            transform: translateY(-1px);
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem;
            background: white;
            border-radius: 16px;
            border: 2px dashed #cbd5e1;
        }
    </style>

    <div class="page-header">
        <h2 class="page-title">
            <i class="material-icons">inventory_2</i>
            Colis en stock (Courrier)
        </h2>
        <div id="selection-counter" class="stats-badge"
            style="background: var(--secondary); color: white; padding: 5px 15px; border-radius: 20px; font-weight: 600; display: none;">
            0 colis sélectionné(s)
        </div>
    </div>

    @if ($demandes->isNotEmpty())
        <div class="action-bar">
            <div class="selection-tools">
                <button type="button" class="btn-submit" style="background: #f1f5f9; color: var(--text-main);"
                    onclick="toggleSelectAll()">
                    <i class="material-icons">select_all</i>
                    Tout sélectionner
                </button>
            </div>

            <form id="assign-form" action="{{ route('poste.assigner-livreur') }}" method="POST" class="assign-section">
                @csrf
                <select name="livreur_id" class="select-livreur" required>
                    <option value="">Choisir un livreur disponible...</option>
                    @foreach ($livreurs as $livreur)
                        <option value="{{ $livreur->id }}">
                            {{ $livreur->name }} {{ $livreur->prenom }} ({{ $livreur->contact }})
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn-submit" id="submit-btn" disabled>
                    <i class="material-icons">send</i>
                    Assigner aux livreurs
                </button>
                <div id="hidden-inputs"></div>
            </form>
        </div>

        <div class="demandes-grid">
            @foreach ($demandes as $demande)
                <div class="demande-card" data-ref="{{ $demande->reference }}" data-type="{{ $demande->type_demande }}"
                    data-assigned="{{ $demande->livreur_id ? '1' : '0' }}"
                    onclick="if(this.dataset.assigned == '0') toggleSelection(this, '{{ $demande->reference }}', '{{ $demande->type_demande }}')"
                    ondblclick="if(this.dataset.assigned == '1') toggleSelection(this, '{{ $demande->reference }}', '{{ $demande->type_demande }}')">
                    <div class="selection-badge">
                        <i class="material-icons" style="font-size: 16px;">check</i>
                    </div>
                    <div class="card-accent"></div>
                    <div class="card-header">
                        <span class="ref-badge" title="Code de Livraison">{{ $demande->livraison_code ?? 'N/A' }}</span>
                        <span class="type-badge">{{ $demande->type_demande }}</span>
                    </div>

                    <div class="card-body">
                        <div class="info-row">
                            <i class="material-icons">person</i>
                            <div class="info-content">
                                <span class="info-label">Destinataire</span>
                                <span class="info-value">{{ $demande->nom_destinataire }}
                                    {{ $demande->prenom_destinataire }}</span>
                            </div>
                        </div>

                        <div class="info-row">
                            <i class="material-icons">location_on</i>
                            <div class="info-content">
                                <span class="info-label">Destination</span>
                                <span class="info-value">{{ $demande->commune_livraison }},
                                    {{ $demande->quartier }}</span>
                            </div>
                        </div>

                        <div class="info-row">
                            <i class="material-icons">event</i>
                            <div class="info-content">
                                <span class="info-label">Reçu au Courrier</span>
                                <span class="info-value">{{ $demande->updated_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <div class="status-pill {{ $demande->livreur_id ? 'status-assigned' : 'status-pending' }}">
                            <i class="material-icons"
                                style="font-size: 14px;">{{ $demande->livreur_id ? 'person_pin' : 'hourglass_empty' }}</i>
                            <span>{{ $demande->livreur_id ? 'Assigné à ' . $demande->livreur->name : "En attente d'assignation" }}</span>
                            @if ($demande->livreur_id)
                                <span
                                    style="font-size: 0.65rem; opacity: 0.8; margin-left: 5px; border-left: 1px solid rgba(0,0,0,0.1); padding-left: 5px;">(Double-clic
                                    pour réassigner)</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="material-icons" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem;">inbox</i>
            <h3>Stock vide</h3>
            <p class="text-muted">Aucun colis n'est actuellement en attente d'assignation dans votre section Courrier.</p>
        </div>
    @endif

    <script>
        let selectedDemandes = [];

        function toggleSelection(card, ref, type) {
            const index = selectedDemandes.findIndex(d => d.ref === ref);

            if (index > -1) {
                selectedDemandes.splice(index, 1);
                card.classList.remove('selected');
            } else {
                selectedDemandes.push({
                    ref,
                    type
                });
                card.classList.add('selected');
            }

            console.log("Sélection actuelle:", selectedDemandes);
            updateUI();
        }

        function toggleSelectAll() {
            // On ne sélectionne par défaut que les colis non assignés pour la sélection groupée
            const cards = document.querySelectorAll('.demande-card[data-assigned="0"]');
            const allCards = document.querySelectorAll('.demande-card');

            if (selectedDemandes.length >= cards.length && selectedDemandes.length > 0) {
                selectedDemandes = [];
                allCards.forEach(c => c.classList.remove('selected'));
            } else {
                selectedDemandes = [];
                cards.forEach(card => {
                    const ref = card.getAttribute('data-ref');
                    const type = card.getAttribute('data-type');
                    selectedDemandes.push({
                        ref,
                        type
                    });
                    card.classList.add('selected');
                });
            }
            updateUI();
        }

        function updateUI() {
            const counter = document.getElementById('selection-counter');
            const submitBtn = document.getElementById('submit-btn');
            const hiddenDiv = document.getElementById('hidden-inputs');

            if (counter) {
                counter.textContent = `${selectedDemandes.length} colis sélectionné(s)`;
                counter.style.display = selectedDemandes.length > 0 ? 'block' : 'none';
            }

            if (submitBtn) {
                submitBtn.disabled = selectedDemandes.length === 0;
            }

            if (hiddenDiv) {
                hiddenDiv.innerHTML = '';
                selectedDemandes.forEach((d, i) => {
                    const refInput = document.createElement('input');
                    refInput.type = 'hidden';
                    refInput.name = `demandes[${i}][reference]`;
                    refInput.value = d.ref;

                    const typeInput = document.createElement('input');
                    typeInput.type = 'hidden';
                    typeInput.name = `demandes[${i}][type]`;
                    typeInput.value = d.type;

                    hiddenDiv.appendChild(refInput);
                    hiddenDiv.appendChild(typeInput);
                });
            }
        }
    </script>
@endsection
