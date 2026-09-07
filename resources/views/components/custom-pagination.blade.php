@if ($paginator->hasPages())
    @php $paginator->appends(request()->query()); @endphp
    <div class="d-flex justify-content-between align-items-center mt-4 p-4 border-top">
        <div class="text-muted">
            Affichage de <strong>{{ $paginator->firstItem() }}</strong> à
            <strong>{{ $paginator->lastItem() }}</strong>
            sur <strong>{{ $paginator->total() }}</strong> résultats
        </div>

        <nav aria-label="Page navigation">
            <ul class="pagination mb-0">
                <!-- Première page -->
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-angle-double-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url(1) }}" aria-label="Première page">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>
                @endif

                <!-- Page précédente -->
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Précédent">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                <!-- Pages numérotées -->
                @php
                    $current = $paginator->currentPage();
                    $last = $paginator->lastPage();
                    $start = max($current - 2, 1);
                    $end = min($start + 4, $last);
                    $start = max($end - 4, 1);
                @endphp

                @if ($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                    </li>
                    @if ($start > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    <li class="page-item {{ $i == $current ? 'active' : '' }}">
                        @if ($i == $current)
                            <span class="page-link">{{ $i }}</span>
                        @else
                            <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                        @endif
                    </li>
                @endfor

                @if ($end < $last)
                    @if ($end < $last - 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                    </li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($last) }}">{{ $last }}</a>
                    </li>
                @endif

                <!-- Page suivante -->
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Suivant">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif

                <!-- Dernière page -->
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($last) }}" aria-label="Dernière page">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-angle-double-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif
