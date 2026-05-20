<div class="modern-table-wrapper" data-total="{{ $allRequests->total() }}">
    <table class="modern-table">
        <thead>
            <tr>
                <th>Type</th>
                <th>Demandeur</th>
                <th>Date</th>
                <th>Heure</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($allRequests as $request)
                @php
                    $badgeClass = '';
                    $iconClass = '';
                    $typeName = '';
                    $actionRoute = '';
                    $details = '';

                    if ($request->request_type == 'naissance') {
                        $badgeClass = 'badge-naiss';
                        $iconClass = 'fa-baby';
                        $typeName = 'Naissance';
                        $actionRoute = route('naissance.traiter', $request->id);
                        $details = $request->user->name . ' ' . $request->user->prenom;
                    } elseif ($request->request_type == 'deces') {
                        $badgeClass = 'badge-deces';
                        $iconClass = 'fa-cross';
                        $typeName = 'Décès';
                        $actionRoute = route('deces.traiter', $request->id);
                        $details = $request->user->name . ' ' . $request->user->prenom;
                    } elseif ($request->request_type == 'mariage') {
                        $badgeClass = 'badge-mariage';
                        $iconClass = 'fa-heart';
                        $typeName = 'Mariage';
                        $actionRoute = route('mariage.traiter', $request->id);
                        if (isset($request->nomEpoux)) {
                            $details = $request->user->name . ' ' . $request->user->prenom;
                        } elseif (isset($request->user)) {
                            $details = $request->user->name . ' ' . $request->user->prenom;
                        } else {
                            $details = 'Demande de mariage';
                        }
                    }
                @endphp
                <tr>
                    <td>
                        <span class="type-badge {{ $badgeClass }}">
                            <i class="fas {{ $iconClass }}"></i>
                            {{ $typeName }}
                        </span>
                    </td>
                    <td>
                        <div class="demandeur-info">
                            <i class="fas fa-user-circle"></i>
                            <span>{{ $details }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="date-info">
                            <i class="fas fa-calendar-day"></i>
                            {{ $request->created_at->format('d/m/Y') }}
                        </div>
                    </td>
                    <td>
                        <div class="time-info">
                            <i class="fas fa-clock"></i>
                            {{ $request->created_at->format('H:i') }}
                        </div>
                    </td>
                    <td class="text-center">
                        <form action="{{ $actionRoute }}" method="POST" style="display:inline;">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn-modern-action">
                                <i class="fas fa-hand-holding"></i>
                                Récupérer
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-table">
                        <div class="empty-content">
                            <i class="fas fa-inbox"></i>
                            <h5>Aucune demande en attente</h5>
                            <p>Il n'y a actuellement aucune demande à traiter</p>
                        </div>
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
        <ul class="pagination mb-0">
            {{-- Bouton Previous --}}
            <li class="{{ $allRequests->onFirstPage() ? 'disabled' : '' }}">
                <a href="{{ $allRequests->previousPageUrl() ?? '#' }}">
                    &laquo; Précédent
                </a>
            </li>

            {{-- Numéros de pages --}}
            @for ($i = 1; $i <= $allRequests->lastPage(); $i++)
                <li class="{{ $allRequests->currentPage() == $i ? 'active' : '' }}">
                    <a href="{{ $allRequests->url($i) }}">
                        {{ $i }}
                    </a>
                </li>
            @endfor

            {{-- Bouton Next --}}
            <li class="{{ !$allRequests->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $allRequests->nextPageUrl() ?? '#' }}">
                    Suivant &raquo;
                </a>
            </li>
        </ul>
    </div>
@endif
