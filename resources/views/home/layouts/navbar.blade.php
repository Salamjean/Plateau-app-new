<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top" id="mainNav">
    <div class="container-fluid px-10percent">
        <a class="navbar-brand d-flex align-items-center py-1" href="{{ route('home') }}">
            <img src="{{ asset('assets/assets/img/plateau-mart.png') }}" alt="Mairie du Plateau"
                style="height: 60px; width: 150px;">
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <i class="bi bi-list text-dark fs-1"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mx-auto text-center py-4 py-lg-0">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                        href="{{ route('home') }}">Accueil</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center justify-content-center {{ request()->routeIs('home.birth', 'home.wedding', 'home.death', 'recherche.demande') ? 'active' : '' }}"
                        href="#" id="actesDropdown" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Actes civils <i class="bi bi-chevron-down ms-2" style="font-size: 0.8rem;"></i>
                    </a>
                    <ul class="dropdown-menu border-0 shadow-lg p-3" aria-labelledby="actesDropdown"
                        style="border-radius: 15px;">
                        <li><a class="dropdown-item fw-600 py-2 {{ request()->routeIs('home.birth') ? 'active' : '' }}"
                                href="{{ route('home.birth') }}"><i
                                    class="bi bi-person-plus me-2 text-primary"></i>Naissance</a></li>
                        <li><a class="dropdown-item fw-600 py-2 {{ request()->routeIs('home.wedding') ? 'active' : '' }}"
                                href="{{ route('home.wedding') }}"><i
                                    class="bi bi-heart me-2 text-danger"></i>Mariage</a></li>
                        <li><a class="dropdown-item fw-600 py-2 {{ request()->routeIs('home.death') ? 'active' : '' }}"
                                href="{{ route('home.death') }}"><i
                                    class="bi bi-person-dash me-2 text-muted"></i>Décès</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item fw-600 py-2 {{ request()->routeIs('recherche.demande') ? 'active' : '' }}"
                                href="{{ route('recherche.demande') }}"><i
                                    class="bi bi-search me-2 text-secondary"></i>Suivre ma demande</a></li>
                    </ul>
                </li>
                {{-- <li class="nav-item"><a class="nav-link {{ request()->routeIs('service.demande') ? 'active' : '' }}"
                        href="{{ route('service.demande') }}">Services</a></li> --}}
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home.contact') ? 'active' : '' }}"
                        href="{{ route('home.contact') }}">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center justify-content-center gap-4 mt-3 mt-lg-0">
                <a href="{{ route('login') }}" class="btn-portal w-100 w-lg-auto text-center">Mon espace citoyen</a>
            </div>
        </div>
    </div>
</nav>
