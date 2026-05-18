<div class="table-responsive" data-total="{{ $allRequests->total() }}">
    <table class="table">
        <thead>
            <tr class="text-center">
                <th class="text-center">Type</th>
                <th class="text-center">Demandeur</th>
                <th class="text-center">Date</th>
                <th class="text-center">Heure</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($allRequests as $request)
                @php
                    $badgeClass = '';
                    $rowClass = '';
                    $typeName = '';
                    $actionRoute = '';
                    $details = '';

                    if ($request->request_type == 'naissance') {
                        $badgeClass = 'badge-naiss';
                        $rowClass = 'row-naissance';
                        $typeName = 'Acte de naissance';
                        $actionRoute = route('naissance.traiter', $request->id);
                        $details =  $request->user->name . ' ' . $request->user->prenom;
                    } elseif ($request->request_type == 'deces') {
                        $badgeClass = 'badge-deces';
                        $rowClass = 'row-deces';
                        $typeName = 'Acte de décès';
                        $actionRoute = route('deces.traiter', $request->id);
                        $details =  $request->user->name . ' ' . $request->user->prenom;
                    } elseif ($request->request_type == 'mariage') {
                        $badgeClass = 'badge-mariage';
                        $rowClass = 'row-mariage';
                        $typeName = 'Acte de mariage';
                        $actionRoute = route('mariage.traiter', $request->id);
                        // Tentative de récupérer un nom pour le mariage
                        if (isset($request->nomEpoux)) {
                            $details =  $request->user->name . ' ' . $request->user->prenom;
                        } elseif (isset($request->user)) {
                            $details = $request->user->name . ' ' . $request->user->prenom;
                        } else {
                            $details = "Demande de mariage";
                        }
                    }
                @endphp
                <tr class="text-center {{ $rowClass }}">
                    <td class="text-center">
                        <span class="badge-type {{ $badgeClass }}">
                            {{ $typeName }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="fw-bold">{{ $details }}</span>
                    </td>
                    <td class="text-center">{{ $request->created_at->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $request->created_at->format('H:i') }}</td>
                    <td class="text-center">
                        <form action="{{ $actionRoute }}" method="POST" style="display:inline;">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn-action">
                                <i class="fas fa-download me-1"></i>Récupérer
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-state">
                        <i class="fas fa-file-invoice" style="align-items: center"></i>
                        <h5>Aucune demande en attente trouvée</h5>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($allRequests->hasPages())
    <div class="mt-4 d-flex justify-content-between align-items-center px-2">
        <small class="text-muted">
            Affichage {{ $allRequests->firstItem() }} à {{ $allRequests->lastItem() }} 
            sur {{ $allRequests->total() }} résultats
        </small>
        <ul class="pagination mb-0" style="gap: 4px; display: flex; list-style: none; padding: 0;">
            {{-- Bouton Previous --}}
            <li class="{{ $allRequests->onFirstPage() ? 'disabled' : '' }}">
                <a href="{{ $allRequests->previousPageUrl() ?? '#' }}"
                   style="display:inline-block; padding: 6px 14px; border-radius: 6px; 
                          background: {{ $allRequests->onFirstPage() ? '#e9ecef' : '#1976d2' }}; 
                          color: {{ $allRequests->onFirstPage() ? '#aaa' : 'white' }};
                          text-decoration: none; font-size: 14px; pointer-events: {{ $allRequests->onFirstPage() ? 'none' : 'auto' }};">
                    &laquo; Précédent
                </a>
            </li>

            {{-- Numéros de pages --}}
            @for ($i = 1; $i <= $allRequests->lastPage(); $i++)
                <li>
                    <a href="{{ $allRequests->url($i) }}"
                       style="display:inline-block; padding: 6px 12px; border-radius: 6px;
                              background: {{ $allRequests->currentPage() == $i ? '#1976d2' : '#f0f0f0' }};
                              color: {{ $allRequests->currentPage() == $i ? 'white' : '#333' }};
                              text-decoration: none; font-size: 14px;">
                        {{ $i }}
                    </a>
                </li>
            @endfor

            {{-- Bouton Next --}}
            <li class="{{ !$allRequests->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $allRequests->nextPageUrl() ?? '#' }}"
                   style="display:inline-block; padding: 6px 14px; border-radius: 6px;
                          background: {{ !$allRequests->hasMorePages() ? '#e9ecef' : '#1976d2' }};
                          color: {{ !$allRequests->hasMorePages() ? '#aaa' : 'white' }};
                          text-decoration: none; font-size: 14px; pointer-events: {{ !$allRequests->hasMorePages() ? 'none' : 'auto' }};">
                    Suivant &raquo;
                </a>
            </li>
        </ul>
    </div>
@endif