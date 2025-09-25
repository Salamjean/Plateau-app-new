@extends('comptable.layouts.template')
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
                        <h5 class="mb-0">Toutes vos ventes</h5>
                        <p class="mb-0 text-sm">Liste des ventes que vous avez effectuées</p>
                    </div>
                    <div class="icon-container">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Cartes statistiques -->
                    <div class="stats-grid mb-4">
                        <div class="stat-card" style="background-color: #ff8800">
                            <div class="stat-content">
                                <h6>Aujourd'hui</h6>
                                <h3>{{ number_format($stats['today'], 0, ',', ' ') }} timbres</h3>
                                <p>{{ number_format($stats['today'] * 500, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                        </div>
                        <div class="stat-card" style="background-color: rgb(71, 71, 198)">
                            <div class="stat-content">
                                <h6>Cette semaine</h6>
                                <h3>{{ number_format($stats['this_week'], 0, ',', ' ') }} timbres</h3>
                                <p>{{ number_format($stats['this_week'] * 500, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                        </div>
                        <div class="stat-card" style="background-color: #1977cc">
                            <div class="stat-content">
                                <h6>Ce mois</h6>
                                <h3>{{ number_format($stats['this_month'], 0, ',', ' ') }} timbres</h3>
                                <p>{{ number_format($stats['this_month'] * 500, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <div class="filters-container mb-4">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label>Date début</label>
                                <input type="date" name="date_debut" class="form-control" 
                                       value="{{ request('date_debut') }}" id="filterDateStart">
                            </div>
                            <div class="filter-group">
                                <label>Date fin</label>
                                <input type="date" name="date_fin" class="form-control" 
                                       value="{{ request('date_fin') }}" id="filterDateEnd">
                            </div>
                            <div class="filter-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn-primary" onclick="appliquerFiltres()">
                                    <i class="fas fa-filter me-2"></i> Appliquer
                                </button>
                            </div>
                            <div class="filter-group">
                                <label>&nbsp;</label>
                                <a href="{{ route('comptable.timbre.history') }}" class="btn-primary" style="text-decoration: none; text-align: center;">
                                    <i class="fas fa-times me-2"></i> Réinitialiser
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des ventes -->
                    <div class="table-container">
                        <table class="data-table" id="tableVentes">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Heure</th>
                                    <th>Financier</th>
                                    <th>Quantité vendue</th>
                                    <th>Type opération</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventes as $vente)
                                <tr class="operation-row" data-date="{{ $vente->created_at->format('Y-m-d') }}">
                                    <td>
                                        <div class="date-cell">
                                            <div class="date">{{ $vente->created_at->format('d/m/Y') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-cell">
                                            <div class="time">{{ $vente->created_at->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center" style="display: flex; justify-content:center">
                                            <div class="user-avatar">
                                                {{ substr(($vente->comptable->name ?? 'N'), 0, 1) }}{{ substr(($vente->comptable->prenom ?? 'A'), 0, 1) }}
                                            </div>
                                            <div class="user-info ms-2">
                                                {{ $vente->comptable->name ?? 'N/A' }} {{ $vente->comptable->prenom ?? '' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="quantity quantity-vente">
                                            {{ number_format(abs($vente->nombre_timbre), 0, ',', ' ') }} timbres
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-vente">
                                            <i class="fas fa-shopping-cart me-1"></i> Vente
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <p>Aucune vente enregistrée pour les financiers</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($ventes->hasPages())
                    <div class="pagination-container">
                        <div class="pagination-info">
                            Affichage de {{ $ventes->firstItem() }} à {{ $ventes->lastItem() }} sur {{ $ventes->total() }} résultats
                        </div>
                        <div class="pagination-links">
                            {{ $ventes->appends(request()->query())->links('pagination.custom') }}
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
        --primary: #1977cc;
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
        text-align: center;
        vertical-align: middle;
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

    .badge-vente {
        background: rgba(239, 71, 111, 0.15);
        color: black;
    }

    .quantity {
        font-weight: 600;
    }

    .quantity-vente {
        color: var(--danger);
    }

    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background-color: #1977cc;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 0.75rem;
        flex-shrink: 0;
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
        background: #3f7aad;
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
        
        .user-avatar {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
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
        const dateStart = document.getElementById('filterDateStart').value;
        const dateEnd = document.getElementById('filterDateEnd').value;
        
        const rows = document.querySelectorAll('.operation-row');
        
        rows.forEach(row => {
            let show = true;
            const rowDate = row.getAttribute('data-date');
            
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
            const headers = ['Date', 'Heure', 'Financier', 'Quantité vendue', 'Type opération'];
            data.push(headers);
            
            // Récupérer les lignes du tableau
            const rows = document.querySelectorAll('#tableVentes tbody tr.operation-row');
            
            rows.forEach(row => {
                if (row.style.display !== 'none') { // Ne prendre que les lignes visibles
                    const cells = row.querySelectorAll('td');
                    const date = cells[0].querySelector('.date').textContent;
                    const time = cells[1].querySelector('.time').textContent;
                    const financier = cells[2].textContent.trim();
                    const quantite = cells[3].textContent.trim();
                    const type = cells[4].textContent.trim();
                    
                    data.push([date, time, financier, quantite, type]);
                }
            });
            
            // Créer un classeur Excel
            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Ventes Financiers");
            
            // Générer le fichier Excel
            const dateExport = new Date().toISOString().slice(0, 10);
            XLSX.writeFile(wb, `ventes_financiers_${dateExport}.xlsx`);
            
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
            doc.text('Ventes des Financiers', 14, 15);
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text(`Export du ${dateExport}`, 14, 22);
            
            // Préparer les données du tableau
            const headers = [['Date', 'Heure', 'Financier', 'Quantité vendue', 'Type opération']];
            const rows = [];
            
            // Récupérer les lignes du tableau
            const tableRows = document.querySelectorAll('#tableVentes tbody tr.operation-row');
            
            tableRows.forEach(row => {
                if (row.style.display !== 'none') { // Ne prendre que les lignes visibles
                    const cells = row.querySelectorAll('td');
                    const date = cells[0].querySelector('.date').textContent;
                    const time = cells[1].querySelector('.time').textContent;
                    const financier = cells[2].textContent.trim();
                    const quantite = cells[3].textContent.trim();
                    const type = cells[4].textContent.trim();
                    
                    rows.push([date, time, financier, quantite, type]);
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
            
            const today = "{{ number_format($stats['today'], 0, ',', ' ') }}";
            const week = "{{ number_format($stats['this_week'], 0, ',', ' ') }}";
            const month = "{{ number_format($stats['this_month'], 0, ',', ' ') }}";
            
            const finalY = doc.lastAutoTable.finalY + 10;
            
            doc.setFontSize(10);
            doc.setTextColor(0);
            doc.setFont(undefined, 'bold');
            doc.text('Récapitulatif:', 14, finalY);
            doc.setFont(undefined, 'normal');
            doc.text(`Aujourd'hui: ${today} timbres`, 14, finalY + 7);
            doc.text(`Cette semaine: ${week} timbres`, 14, finalY + 14);
            doc.text(`Ce mois: ${month} timbres`, 14, finalY + 21);
            
            // Sauvegarder le PDF
            doc.save(`ventes_financiers_${new Date().toISOString().slice(0, 10)}.pdf`);
            
            showToast('Succès', 'Export PDF terminé avec succès', 'success');
        } catch (error) {
            console.error('Erreur lors de l\'export PDF:', error);
            showToast('Erreur', 'Une erreur est survenue lors de l\'export PDF', 'error');
        }
    }
</script>
@endsection