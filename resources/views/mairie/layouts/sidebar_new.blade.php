<aside class="custom-sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <a href="{{ route('mairie.dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('assets/assets/img/logo plateau.png') }}" alt="Logo Plateau" />
        </a>
        <div class="sidebar-user-info">
            <p class="user-name">Mairie : Plateau</p>
            <p class="user-email">{{ Auth::guard('mairie')->user()->email }}</p>
        </div>
    </div>

    <!-- Sidebar Content -->
    <nav class="sidebar-nav">
        <!-- Dashboard -->
        <a href="{{ route('mairie.dashboard') }}"
            class="sidebar-link {{ request()->routeIs('mairie.dashboard') ? 'active' : '' }}" title="Tableau de bord">
            <i class="material-icons">home</i>
            <span>Tableau de bord</span>
        </a>

        <div class="sidebar-divider"></div>

        <!-- Etat civil -->
        <div class="sidebar-menu-item">
            <a href="#"
                class="sidebar-link has-submenu {{ request()->routeIs('mairie.state.*') ? 'active-parent' : '' }}"
                data-submenu="etat-civil" title="Etat civil">
                <i class="material-icons">home</i>
                <span>Etat civil</span>
                <i class="material-icons chevron">chevron_right</i>
            </a>
            <div class="sidebar-submenu {{ request()->routeIs('mairie.state.*') ? 'active' : '' }}" id="etat-civil">
                @php
                    $hasEtatCivil = \App\Models\EtatCivil::where(
                        'mairie_id',
                        Auth::guard('mairie')->user()->id,
                    )->exists();
                @endphp
                @if ($hasEtatCivil)
                    <a href="javascript:void(0)" class="sidebar-sublink disabled">
                        Ajout du service
                    </a>
                @else
                    <a href="{{ route('mairie.state.create') }}"
                        class="sidebar-sublink {{ request()->routeIs('mairie.state.create') ? 'active' : '' }}">
                        Ajout du service
                    </a>
                @endif
                <a href="{{ route('mairie.state.index') }}"
                    class="sidebar-sublink {{ request()->routeIs('mairie.state.index') ? 'active' : '' }}">
                    Informations
                </a>
            </div>
        </div>

        <!-- Régie des taxes -->
        <div class="sidebar-menu-item">
            <a href="#"
                class="sidebar-link has-submenu {{ request()->routeIs('mairie.finance.*') ? 'active-parent' : '' }}"
                data-submenu="finance" title="Régie des taxes">
                <i class="material-icons">monetization_on</i>
                <span>Régie des taxes</span>
                <i class="material-icons chevron">chevron_right</i>
            </a>
            <div class="sidebar-submenu {{ request()->routeIs('mairie.finance.*') ? 'active' : '' }}" id="finance">
                @php
                    $hasFinance = \App\Models\Finance::where('mairie_id', Auth::guard('mairie')->user()->id)->exists();
                @endphp
                @if ($hasFinance)
                    <a href="javascript:void(0)" class="sidebar-sublink disabled">
                        Ajout du service
                    </a>
                @else
                    <a href="{{ route('mairie.finance.create') }}"
                        class="sidebar-sublink {{ request()->routeIs('mairie.finance.create') ? 'active' : '' }}">
                        Ajout du service
                    </a>
                @endif
                <a href="{{ route('mairie.finance.index') }}"
                    class="sidebar-sublink {{ request()->routeIs('mairie.finance.index') ? 'active' : '' }}">
                    Informations
                </a>
            </div>
        </div>

        <!-- Livraison -->
        <div class="sidebar-menu-item">
            <a href="#"
                class="sidebar-link has-submenu {{ request()->routeIs('post.*') ? 'active-parent' : '' }}"
                data-submenu="livraison" title="Livraison">
                <i class="material-icons">local_shipping</i>
                <span>Livraison</span>
                <i class="material-icons chevron">chevron_right</i>
            </a>
            <div class="sidebar-submenu {{ request()->routeIs('post.*') ? 'active' : '' }}" id="livraison">
                @php
                    $hasPoste = \App\Models\Poste::exists();
                @endphp
                @if ($hasPoste)
                    <a href="javascript:void(0)" class="sidebar-sublink disabled">
                        Ajout du service
                    </a>
                @else
                    <a href="{{ route('post.create') }}"
                        class="sidebar-sublink {{ request()->routeIs('post.create') ? 'active' : '' }}">
                        Ajout du service
                    </a>
                @endif
                <a href="{{ route('post.index') }}"
                    class="sidebar-sublink {{ request()->routeIs('post.index') ? 'active' : '' }}">
                    Informations
                </a>
            </div>
        </div>

        <div class="sidebar-divider"></div>

        <!-- Actes -->
        <a href="{{ route('mairie.request.birth') }}"
            class="sidebar-link {{ request()->routeIs('mairie.request.birth') ? 'active' : '' }}"
            title="Acte Naissance">
            <i class="material-icons">child_care</i>
            <span>Acte Naissance</span>
        </a>

        <a href="{{ route('mairie.request.death') }}"
            class="sidebar-link {{ request()->routeIs('mairie.request.death') ? 'active' : '' }}" title="Acte Décès">
            <i class="material-icons">spa</i>
            <span>Acte Décès</span>
        </a>

        <a href="{{ route('mairie.request.wedding') }}"
            class="sidebar-link {{ request()->routeIs('mairie.request.wedding') ? 'active' : '' }}"
            title="Acte Mariage">
            <i class="material-icons">favorite</i>
            <span>Acte Mariage</span>
        </a>

        <div class="sidebar-divider"></div>

        <!-- Rendez-vous & Timbres -->
        <a href="{{ route('mairie.rendezvous.index') }}"
            class="sidebar-link {{ request()->routeIs('mairie.rendezvous.index') ? 'active' : '' }}"
            title="Rendez-vous Mariage">
            <i class="material-icons">event</i>
            <span>Rendez-vous Mariage</span>
        </a>

        <a href="{{ route('mairie.timbres.index') }}"
            class="sidebar-link {{ request()->routeIs('mairie.timbres.index') ? 'active' : '' }}"
            title="Ventes de timbres">
            <i class="material-icons">local_activity</i>
            <span>Ventes de timbres</span>
        </a>

        <a href="{{ route('mairie.portefeuille.index') }}"
            class="sidebar-link {{ request()->routeIs('mairie.portefeuille.index') ? 'active' : '' }}"
            title="Portefeuille en ligne">
            <i class="material-icons">account_balance_wallet</i>
            <span>Portefeuille en ligne</span>
        </a>
    </nav>
</aside>

<style>
    /* Custom Sidebar Styles - Completely Independent */
    .custom-sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 260px;
        height: 100vh;
        background-color: #1f4083;
        color: #ffffff;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 1000;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .custom-sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }

    .custom-sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
    }

    .custom-sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    /* Sidebar Header */
    .sidebar-header {
        padding: 25px 20px;
        background: rgba(0, 0, 0, 0.15);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-logo {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
    }

    .sidebar-logo img {
        width: 80px;
        height: auto;
    }

    .sidebar-user-info {
        text-align: center;
    }

    .custom-sidebar .sidebar-user-info .user-name {
        font-size: 15px;
        font-weight: 600;
        color: #ffffff !important;
        margin: 0 0 5px 0;
        padding: 0;
        line-height: 1.4;
        white-space: normal;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .custom-sidebar .sidebar-user-info .user-email {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.7) !important;
        margin: 0;
        padding: 0;
        line-height: 1.4;
        word-break: break-all;
        white-space: normal;
        overflow-wrap: break-word;
    }

    /* Sidebar Navigation */
    .sidebar-nav {
        padding: 15px 0;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        border-radius: 8px;
        margin: 4px 12px;
    }

    .sidebar-link:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
    }

    .sidebar-link.active {
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.15));
        color: #ffffff;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .sidebar-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 70%;
        width: 6px;
        background: #ffffff;
        border-radius: 0 4px 4px 0;
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.8);
    }

    .sidebar-link.active::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 8px;
        pointer-events: none;
    }

    .sidebar-link i.material-icons {
        font-size: 22px;
        margin-right: 12px;
        color: inherit;
    }

    .sidebar-link.active i.material-icons {
        color: #ffffff;
        filter: drop-shadow(0 0 4px rgba(255, 255, 255, 0.5));
    }

    .sidebar-link span {
        flex: 1;
        font-size: 14px;
        letter-spacing: 0.3px;
    }

    .sidebar-link.active span {
        text-shadow: 0 0 8px rgba(255, 255, 255, 0.3);
    }

    .sidebar-link .chevron {
        font-size: 20px;
        margin-left: auto;
        margin-right: 0;
        transition: transform 0.3s ease;
    }

    /* Parent menu active state when child is active */
    .sidebar-link.active-parent {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        font-weight: 600;
        border-radius: 8px;
    }

    .sidebar-link.active-parent::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 50%;
        width: 4px;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 0 4px 4px 0;
    }

    /* Submenu */
    .sidebar-menu-item {
        position: relative;
    }

    .sidebar-submenu {
        max-height: 0;
        overflow: hidden;
        background: rgba(0, 0, 0, 0.2);
        transition: max-height 0.3s ease;
    }

    .sidebar-submenu.active {
        max-height: 500px;
    }

    .sidebar-sublink {
        display: block;
        padding: 10px 20px 10px 54px;
        color: rgba(255, 255, 255, 0.75);
        text-decoration: none;
        font-size: 13px;
        transition: all 0.3s ease;
        position: relative;
        border-radius: 6px;
        margin: 2px 12px;
    }

    .sidebar-sublink:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        padding-left: 56px;
    }

    .sidebar-sublink.active {
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.12));
        color: #ffffff;
        font-weight: 700;
        padding-left: 56px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .sidebar-sublink.active::before {
        content: '';
        position: absolute;
        left: 40px;
        top: 50%;
        transform: translateY(-50%);
        width: 8px;
        height: 8px;
        background: #ffffff;
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.8);
    }

    .sidebar-sublink.active::after {
        content: '';
        position: absolute;
        left: 12px;
        right: 12px;
        top: 0;
        bottom: 0;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 6px;
        pointer-events: none;
    }

    .sidebar-sublink.disabled {
        color: rgba(255, 255, 255, 0.4);
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Divider */
    .sidebar-divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.15);
        margin: 10px 20px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .custom-sidebar {
            width: 0;
            transform: translateX(-100%);
        }

        .custom-sidebar.mobile-open {
            width: 260px;
            transform: translateX(0);
        }
    }

    /* Collapsed Sidebar State */
    .custom-sidebar.collapsed {
        width: 70px;
    }

    .custom-sidebar.collapsed .sidebar-header {
        padding: 20px 10px;
    }

    .custom-sidebar.collapsed .sidebar-logo img {
        width: 50px;
    }

    .custom-sidebar.collapsed .sidebar-user-info {
        display: none;
    }

    .custom-sidebar.collapsed .sidebar-link span,
    .custom-sidebar.collapsed .sidebar-sublink,
    .custom-sidebar.collapsed .chevron {
        display: none;
    }

    .custom-sidebar.collapsed .sidebar-link {
        justify-content: center;
        padding: 12px;
        margin: 4px 8px;
    }

    .custom-sidebar.collapsed .sidebar-link i.material-icons {
        margin-right: 0;
    }

    .custom-sidebar.collapsed .sidebar-menu-item .sidebar-submenu {
        display: none;
    }

    .custom-sidebar.collapsed .sidebar-divider {
        margin: 10px 10px;
    }

    /* Tooltip for collapsed sidebar */
    .custom-sidebar.collapsed .sidebar-link {
        position: relative;
    }

    .custom-sidebar.collapsed .sidebar-link:hover::after {
        content: attr(title);
        position: absolute;
        left: 70px;
        top: 50%;
        transform: translateY(-50%);
        background: #2d5aa8;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        white-space: nowrap;
        font-size: 13px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        z-index: 10000;
        pointer-events: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Apply chevron rotation for already active submenus from server (no localStorage)
        document.querySelectorAll('.sidebar-submenu.active').forEach(submenu => {
            const parentLink = document.querySelector(`[data-submenu="${submenu.id}"]`);
            if (parentLink) {
                const chevron = parentLink.querySelector('.chevron');
                if (chevron) chevron.style.transform = 'rotate(90deg)';
            }
        });

        // Toggle submenu on click (manual toggle only, no persistence)
        const submenuLinks = document.querySelectorAll('.sidebar-link.has-submenu');

        submenuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const submenuId = this.getAttribute('data-submenu');
                const submenu = document.getElementById(submenuId);
                const chevron = this.querySelector('.chevron');

                // Toggle active state (no localStorage)
                const isActive = submenu.classList.contains('active');

                if (isActive) {
                    // Close this submenu
                    submenu.classList.remove('active');
                    chevron.style.transform = 'rotate(0deg)';
                } else {
                    // Open this submenu
                    submenu.classList.add('active');
                    chevron.style.transform = 'rotate(90deg)';
                }
            });
        });

        // Clear any old localStorage data from previous version
        localStorage.removeItem('sidebarSubmenuStates');
    });
</script>
