<div class="table-responsive">
    <table class="table">
        <thead>
            <tr class="text-center">
                <th class="text-center">Type</th>
                <th class="text-center">Demandeur / Sujet</th>
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
                        $typeName = 'Naissance';
                        $actionRoute = route('naissance.traiter', $request->id);
                        $details = $request->name . ' ' . $request->prenom;
                    } elseif ($request->request_type == 'deces') {
                        $badgeClass = 'badge-deces';
                        $rowClass = 'row-deces';
                        $typeName = 'Décès';
                        $actionRoute = route('deces.traiter', $request->id);
                        $details = $request->name;
                    } elseif ($request->request_type == 'mariage') {
                        $badgeClass = 'badge-mariage';
                        $rowClass = 'row-mariage';
                        $typeName = 'Mariage';
                        $actionRoute = route('mariage.traiter', $request->id);
                        // Tentative de récupérer un nom pour le mariage
                        if (isset($request->nomEpoux)) {
                            $details = $request->nomEpoux . ' & ' . $request->nomEpouse;
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
                        <h5>Aucune demande en attente pour cette période</h5>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>