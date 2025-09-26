@extends('finance.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Ajout des bibliothèques pour l'export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card custom-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h5 class="mb-0">Historique des Timbres</h5>
                        <p class="mb-0 text-sm">Historique complet des recharges et ventes de timbres</p>
                    </div>
                    <div class="icon-container">
                        <i class="fas fa-history"></i>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Filtres -->
                    <div class="filters-container mb-4">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label>Type d'opération</label>
                                <select id="filterType">
                                    <option value="all">Tous</option>
                                    <option value="recharge">Recharges</option>
                                    <option value="vente">Ventes</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Date début</label>
                                <input type="date" id="filterDateStart">
                            </div>
                            <div class="filter-group">
                                <label>Date fin</label>
                                <input type="date" id="filterDateEnd">
                            </div>
                            <div class="filter-group">
                                <label>&nbsp;</label>
                                <button class="btn-primary" onclick="appliquerFiltres()">
                                    <i class="fas fa-search me-2"></i> Appliquer
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="stats-grid mb-4">
                        <div class="stat-card" style="background-color: #ff8800">
                            <div class="stat-content">
                                <h6>Total Recharges</h6>
                                <h3>{{ number_format($total_recharges, 0, ',', ' ') }} timbres</h3>
                                <p>{{ number_format($total_recharges * 500, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                        </div>
                        <div class="stat-card" style="background-color: rgb(71, 71, 198)">
                            <div class="stat-content">
                                <h6>Total Ventes</h6>
                                <h3>{{ number_format($total_ventes, 0, ',', ' ') }} timbres</h3>
                                <p>{{ number_format($total_ventes * 500, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-minus-circle"></i>
                            </div>
                        </div>
                        <div class="stat-card " style="background-color: #1977cc">
                            <div class="stat-content">
                                <h6>Stock Actuel</h6>
                                <h3>{{ number_format($solde_actuel, 0, ',', ' ') }} timbres</h3>
                                <p>{{ number_format($solde_actuel * 500, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                        <div class="stat-card bg-info">
                            <div class="stat-content">
                                <h6>Chiffre d'Affaires</h6>
                                <h3>{{ number_format($total_ventes * 500, 0, ',', ' ') }} FCFA</h3>
                                <p>{{ $total_ventes }} ventes</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau d'historique -->
                    <div class="table-container">
                        <table class="data-table" id="tableHistorique">
                            <thead>
                                <tr>
                                    <th>Date & Heure</th>
                                    <th>Type</th>
                                    <th>Quantité</th>
                                    <th>Montant</th>
                                    <th>Solde après opération</th>
                                    <th>Vendeur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $solde_cumulatif = 0;
                                @endphp
                                @forelse($historique as $operation)
                                    @php
                                        $solde_cumulatif += $operation->nombre_timbre;
                                        $type = $operation->nombre_timbre > 0 ? 'recharge' : 'vente';
                                        $quantite = abs($operation->nombre_timbre);
                                        $montant = $quantite * 500;
                                    @endphp
                                    <tr class="operation-row" data-type="{{ $type }}" data-date="{{ $operation->created_at->format('Y-m-d') }}">
                                        <td>
                                            <div class="date-cell">
                                                <div class="date">{{ $operation->created_at->format('d/m/Y') }}</div>
                                                <div class="time">{{ $operation->created_at->format('H:i:s') }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $type }}">
                                                <i class="fas fa-{{ $type == 'recharge' ? 'plus' : 'minus' }} me-1"></i>
                                                {{ $type == 'recharge' ? 'Recharge' : 'Vente' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="quantity quantity-{{ $type }}">
                                                {{ $type == 'recharge' ? '+' : '-' }}{{ number_format($quantite, 0, ',', ' ') }} timbres
                                            </span>
                                        </td>
                                        <td>
                                            <span class="amount">
                                                {{ number_format($montant, 0, ',', ' ') }} FCFA
                                            </span>
                                        </td>
                                        <td>
                                            <span class="balance">
                                                {{ number_format($solde_cumulatif, 0, ',', ' ') }} timbres
                                            </span>
                                        </td>
                                        <td>
                                            <span class="seller">
                                                {{ $operation->comptable->name ?? 'Rechargement'}} {{ $operation->comptable->prenom ?? '' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox"></i>
                                                <p>Aucune opération enregistrée</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($historique->hasPages())
                    <div class="pagination-container">
                        <div class="pagination-info">
                            Affichage de {{ $historique->firstItem() }} à {{ $historique->lastItem() }} sur {{ $historique->total() }} résultats
                        </div>
                        <div class="pagination-links">
                            {{ $historique->appends(request()->query())->links('pagination.custom') }}
                        </div>
                    </div>
                    @endif

                    <!-- Boutons d'export -->
                    <div class="export-container">
                        <button class="btn-export excel" onclick="exporterExcel()">
                            <i class="fas fa-file-excel me-2"></i> Exporter en Excel
                        </button>
                        <button class="btn-export pdf" onclick="exporterPDF()">
                            <i class="fas fa-file-pdf me-2"></i> Exporter en PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #ff8800;
        --primary-light: #4895ef;
        --secondary: #1977cc;
        --success: #ff8800;
        --danger: #1977cc;
        --warning: #ffd166;
        --info: black;
        --dark: #2b2d42;
        --light: #f8f9fa;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-400: #ced4da;
        --gray-500: #adb5bd;
        --gray-600: #6c757d;
        --gray-700: #495057;
        --gray-800: #343a40;
        --gray-900: #212529;
        --border-radius: 12px;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.08);
        --shadow-lg: 0 10px 25px rgba(0,0,0,0.1), 0 5px 10px rgba(0,0,0,0.05);
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: #f5f7fb;
        color: var(--gray-800);
        line-height: 1.6;
    }

    .custom-card {
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        overflow: hidden;
        background: white;
    }

    .custom-card:hover {
        box-shadow: var(--shadow-md);
    }

    .card-header {
        background: white;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-header h5 {
        font-weight: 700;
        color: var(--dark);
        font-size: 1.25rem;
    }

    .card-header p {
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    .icon-container {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
    }

    /* Filtres */
    .filters-container {
        background: var(--gray-100);
        border-radius: var(--border-radius);
        padding: 1.25rem;
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-group label {
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: var(--gray-700);
    }

    .filter-group select, 
    .filter-group input {
        padding: 0.75rem;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        font-size: 0.875rem;
        transition: var(--transition);
    }

    .filter-group select:focus, 
    .filter-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    }

    .btn-primary {
        padding: 0.75rem 1rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary:hover {
        background: var(--primary-light);
        transform: translateY(-2px);
    }

    /* Statistiques */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
    }

    .stat-card {
        border-radius: var(--border-radius);
        padding: 1.5rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    .stat-card.bg-primary { background: linear-gradient(135deg, var(--primary), var(--primary-light)); }
    .stat-card.bg-danger { background: linear-gradient(135deg, var(--danger), #ff8800); }
    .stat-card.bg-success { background: linear-gradient(135deg, var(--success), #06d6a0); }
    .stat-card.bg-info { background: linear-gradient(135deg, var(--info), #2a9dce); }

    .stat-content h6 {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }

    .stat-content h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-content p {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    /* Tableau */
    .table-container {
        overflow-x: auto;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        background: white;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        background: #1977cc;
        padding: 1rem;
        text-align: center;
        font-weight: 600;
        color: white;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--gray-300);
    }

    .data-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--gray-200);
        text-align: center
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .data-table tr:hover {
        background: var(--gray-50);
    }

    .date-cell .date {
        font-weight: 500;
        color: var(--gray-800);
    }

    .date-cell .time {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .badge-recharge {
        background: rgba(6, 214, 160, 0.15);
        color: black;
    }

    .badge-vente {
        background: rgba(239, 71, 111, 0.15);
        color: black;
    }

    .quantity {
        font-weight: 600;
    }

    .quantity-recharge {
        color: var(--success);
    }

    .quantity-vente {
        color: var(--danger);
    }

    .amount {
        font-weight: 600;
        color: var(--gray-800);
    }

    .balance {
        font-weight: 600;
        color: var(--info);
    }

    .seller {
        font-weight: 500;
        color: var(--gray-700);
    }

    /* État vide */
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: var(--gray-500);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Pagination */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .pagination-info {
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    .pagination-links {
        display: flex;
        gap: 0.5rem;
    }

    /* Bouton export */
    .export-container {
        display: flex;
        justify-content: flex-end;
        margin-top: 1.5rem;
    }

    .btn-export {
        padding: 0.75rem 1.5rem;
        background: var(--success);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
    }

    .btn-export:hover {
        background: #05c28f;
        transform: translateY(-2px);
    }

     .export-container {
        display: flex;
        justify-content: flex-end;
        margin-top: 1.5rem;
        gap: 1rem;
    }

    .btn-export {
        padding: 0.75rem 1.5rem;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
    }

    .btn-export.excel {
        background: var(--success);
    }

    .btn-export.excel:hover {
        background:#d57c16 ;
        transform: translateY(-2px);
    }

    .btn-export.pdf {
        background: var(--danger);
    }

    .btn-export.pdf:hover {
        background: #05c505;
        transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .filter-row {
            grid-template-columns: 1fr;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .pagination-container {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .data-table {
            font-size: 0.875rem;
        }
        
        .data-table th, 
        .data-table td {
            padding: 0.75rem 0.5rem;
        }
    }
</style>

<script>
    // Initialisation des bibliothèques
    window.jsPDF = window.jspdf.jsPDF;

    document.addEventListener('DOMContentLoaded', function() {
        // Afficher les messages de session avec Toast
        @if(session('success'))
            showToast('Succès', '{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showToast('Erreur', '{{ session('error') }}', 'error');
        @endif
    });

    function showToast(title, message, type) {
        // Ici vous pouvez utiliser votre bibliothèque de toast préférée
        // Pour l'exemple, nous utilisons une alerte simple
        alert(`${title}: ${message}`);
    }

    function appliquerFiltres() {
        const type = document.getElementById('filterType').value;
        const dateStart = document.getElementById('filterDateStart').value;
        const dateEnd = document.getElementById('filterDateEnd').value;
        
        const rows = document.querySelectorAll('.operation-row');
        
        rows.forEach(row => {
            let show = true;
            const rowType = row.getAttribute('data-type');
            const rowDate = row.getAttribute('data-date');
            
            // Filtre par type
            if (type !== 'all' && rowType !== type) {
                show = false;
            }
            
            // Filtre par date
            if (dateStart && rowDate < dateStart) {
                show = false;
            }
            
            if (dateEnd && rowDate > dateEnd) {
                show = false;
            }
            
            row.style.display = show ? '' : 'none';
        });
    }

    // Fonction pour exporter en Excel
    function exporterExcel() {
        try {
            // Préparer les données
            let data = [];
            const headers = ['Date', 'Heure', 'Type', 'Quantité', 'Montant', 'Solde', 'Vendeur'];
            data.push(headers);
            
            // Récupérer les lignes du tableau
            const rows = document.querySelectorAll('#tableHistorique tbody tr.operation-row');
            
            rows.forEach(row => {
                if (row.style.display !== 'none') { // Ne prendre que les lignes visibles
                    const cells = row.querySelectorAll('td');
                    const dateTime = cells[0].querySelector('.date-cell');
                    const date = dateTime.querySelector('.date').textContent;
                    const time = dateTime.querySelector('.time').textContent;
                    const type = cells[1].textContent.trim();
                    const quantite = cells[2].textContent.trim();
                    const montant = cells[3].textContent.trim();
                    const solde = cells[4].textContent.trim();
                    const vendeur = cells[5].textContent.trim();
                    
                    data.push([date, time, type, quantite, montant, solde, vendeur]);
                }
            });
            
            // Créer un classeur Excel
            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Historique Timbres");
            
            // Générer le fichier Excel
            const dateExport = new Date().toISOString().slice(0, 10);
            XLSX.writeFile(wb, `historique_timbres_${dateExport}.xlsx`);
            
            showToast('Succès', 'Export Excel terminé avec succès', 'success');
        } catch (error) {
            console.error('Erreur lors de l\'export Excel:', error);
            showToast('Erreur', 'Une erreur est survenue lors de l\'export Excel', 'error');
        }
    }

    // Fonction pour exporter en PDF
    function exporterPDF() {
        try {
            // Créer un nouveau document PDF
            const doc = new jsPDF();
            
            // Titre du document
            const dateExport = new Date().toLocaleDateString('fr-FR');
            doc.setFontSize(16);
            doc.text('Historique des Timbres', 14, 15);
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text(`Export du ${dateExport}`, 14, 22);
            
            // Préparer les données du tableau
            const headers = [['Date', 'Heure', 'Type', 'Quantité', 'Montant', 'Solde', 'Vendeur']];
            const rows = [];
            
            // Récupérer les lignes du tableau
            const tableRows = document.querySelectorAll('#tableHistorique tbody tr.operation-row');
            
            tableRows.forEach(row => {
                if (row.style.display !== 'none') { // Ne prendre que les lignes visibles
                    const cells = row.querySelectorAll('td');
                    const dateTime = cells[0].querySelector('.date-cell');
                    const date = dateTime.querySelector('.date').textContent;
                    const time = dateTime.querySelector('.time').textContent;
                    const type = cells[1].textContent.trim();
                    const quantite = cells[2].textContent.trim();
                    const montant = cells[3].textContent.trim();
                    const solde = cells[4].textContent.trim();
                    const vendeur = cells[5].textContent.trim();
                    
                    rows.push([date, time, type, quantite, montant, solde, vendeur]);
                }
            });
            
            // Générer le tableau dans le PDF
            doc.autoTable({
                head: headers,
                body: rows,
                startY: 30,
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2
                },
                headStyles: {
                    fillColor: [0, 51, 196],
                    textColor: 255
                },
                alternateRowStyles: {
                    fillColor: [240, 240, 240]
                }
            });
            
            // Ajouter les statistiques en pied de page
            const lastPage = doc.internal.getNumberOfPages();
            doc.setPage(lastPage);
            
            const totalRecharges = "{{ number_format($total_recharges, 0, ',', ' ') }}";
            const totalVentes = "{{ number_format($total_ventes, 0, ',', ' ') }}";
            const soldeActuel = "{{ number_format($solde_actuel, 0, ',', ' ') }}";
            const chiffreAffaires = "{{ number_format($total_ventes * 500, 0, ',', ' ') }}";
            
            const finalY = doc.lastAutoTable.finalY + 10;
            
            doc.setFontSize(10);
            doc.setTextColor(0);
            doc.setFont(undefined, 'bold');
            doc.text('Récapitulatif:', 14, finalY);
            doc.setFont(undefined, 'normal');
            doc.text(`Total Recharges: ${totalRecharges} timbres`, 14, finalY + 7);
            doc.text(`Total Ventes: ${totalVentes} timbres`, 14, finalY + 14);
            doc.text(`Solde Actuel: ${soldeActuel} timbres`, 14, finalY + 21);
            doc.text(`Chiffre d'Affaires: ${chiffreAffaires} FCFA`, 14, finalY + 28);
            
            // Sauvegarder le PDF
            doc.save(`historique_timbres_${new Date().toISOString().slice(0, 10)}.pdf`);
            
            showToast('Succès', 'Export PDF terminé avec succès', 'success');
        } catch (error) {
            console.error('Erreur lors de l\'export PDF:', error);
            showToast('Erreur', 'Une erreur est survenue lors de l\'export PDF', 'error');
        }
    }
</script>
@endsection