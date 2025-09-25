@extends('poste.layouts.template')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --primary-color: #06634e;
        --primary-light: #0a8a6d;
        --secondary-color: #f9cf03;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --border-radius: 10px;
        --box-shadow: 0 5px 15px rgba(6, 99, 78, 0.1);
    }
    
    .versement-container {
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .back-btn {
        background-color: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: var(--border-radius);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 1.5rem;
        box-shadow: var(--box-shadow);
        text-align: center;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }
    
    .solde-disponible {
        color: var(--primary-color);
    }
    
    .total-livraisons {
        color: var(--secondary-color);
    }
    
    .total-versements {
        color: var(--danger-color);
    }
    
    .versement-form {
        background: white;
        border-radius: var(--border-radius);
        padding: 2rem;
        box-shadow: var(--box-shadow);
        margin-bottom: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--dark-color);
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: var(--border-radius);
        transition: border-color 0.3s;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        outline: none;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.3s;
    }
    
    .btn-primary:hover {
        background-color: var(--primary-light);
    }
    
    .btn-primary:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }
    
    .history-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 2rem;
        box-shadow: var(--box-shadow);
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table th,
    .table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .badge-success {
        background-color: var(--success-color);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 50px;
        font-size: 0.75rem;
    }
    
    .alert {
        padding: 1rem;
        border-radius: var(--border-radius);
        margin-bottom: 1rem;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<div class="versement-container">
    <!-- En-tête -->
    <div class="page-header">
        <h1>Versement pour {{ $livreur->name }} {{ $livreur->prenom }}</h1>
        <a href="{{ route('delivery.index') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
    
    <!-- Cartes de statistiques -->
    <div class="stats-cards">
        <div class="stat-card">
            <h3>Total des Livraisons</h3>
            <div class="stat-value total-livraisons">{{ number_format($soldeDisponible, 0, ',', ' ') }} FCFA</div>
            <p>Montant total des livraisons effectuées</p>
        </div>
        
        <div class="stat-card">
            <h3>Total des Versements</h3>
            <div class="stat-value total-versements">{{ number_format($totalVersements, 0, ',', ' ') }} FCFA</div>
            <p>Montant total déjà versé</p>
        </div>
        
        <div class="stat-card">
            <h3>Solde Disponible</h3>
            <div class="stat-value solde-disponible">{{ number_format($soldeReel, 0, ',', ' ') }} FCFA</div>
            <p>Montant disponible pour versement</p>
        </div>
    </div>
    
    <!-- Formulaire de versement -->
    <div class="versement-form">
        <h2>Effectuer un versement</h2>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <form action="{{ route('poste.livreur.versement.process', $livreur->id) }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="montant">Montant à verser (FCFA)</label>
                <input type="number" id="montant" name="montant" class="form-control" 
                       min="1" max="{{ $soldeReel }}" required 
                       placeholder="Entrez le montant à verser"
                       @if($soldeReel <= 0) disabled @endif>
                <small>Solde maximum: {{ number_format($soldeReel, 0, ',', ' ') }} FCFA</small>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="methode_paiement">Méthode de paiement</label>
                <select id="methode_paiement" name="methode_paiement" class="form-control" required
                        @if($soldeReel <= 0) disabled @endif>
                    <option value="">Sélectionnez une méthode</option>
                    <option value="Espèce">Espèce</option>
                    <option value="Wave">Wave</option>
                    <option value="Orange Money">Orange Money</option>
                    <option value="MTN Money">MTN Money</option>
                    <option value="Virement bancaire">Virement bancaire</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="reference">Référence (optionnel)</label>
                <input type="text" id="reference" name="reference" class="form-control" 
                        value="{{ 'VERS'.Auth::guard('livreur')->user()->id.'-'.Str::random(6).'-'.time() }}"
                       @readonly(true)
                       @if($soldeReel <= 0) disabled @endif>
            </div>
            
            <button type="submit" class="btn-primary" @if($soldeReel <= 0) disabled @endif>
                <i class="fas fa-money-bill-wave"></i> Effectuer le versement
            </button>
            
            @if($soldeReel <= 0)
            <div class="alert alert-danger mt-3">
                Aucun solde disponible pour effectuer un versement.
            </div>
            @endif
        </form>
    </div>
    
    <!-- Historique des versements -->
    <div class="history-card">
        <h2>Historique des versements</h2>
        
        @if($versements->isEmpty())
            <p>Aucun versement effectué pour ce livreur.</p>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Méthode</th>
                            <th>Référence</th>
                            <th>Statut</th>
                            <th>Effectué par</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($versements as $versement)
                        <tr>
                            <td>{{ $versement->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ number_format($versement->montant, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $versement->methode_paiement }}</td>
                            <td>{{ $versement->reference ?? 'N/A' }}</td>
                            <td><span class="badge-success">{{ $versement->statut }}</span></td>
                            <td>{{ $versement->poste->name ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
    // Validation du montant
    document.getElementById('montant')?.addEventListener('input', function() {
        const maxAmount = {{ $soldeReel }};
        const enteredAmount = parseFloat(this.value);
        
        if (enteredAmount > maxAmount) {
            this.setCustomValidity('Le montant ne peut pas dépasser ' + maxAmount.toLocaleString() + ' FCFA');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
@endsection