<aside class="custom-sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <a href="{{ route('delivery.dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('assets/assets/img/logo plateau.png') }}" alt="Logo Plateau" />
        </a>
        <div class="sidebar-user-info">
            <p class="user-name">{{ Auth::guard('livreur')->user()->name }}</p>
            <p class="user-email">Livreur</p>
        </div>
    </div>

    <!-- Sidebar Content -->
    <nav class="sidebar-nav">
        <!-- Dashboard -->
        <a href="{{ route('delivery.dashboard') }}"
            class="sidebar-link {{ request()->routeIs('delivery.dashboard') ? 'active' : '' }}" title="Tableau de bord">
            <i class="material-icons">home</i>
            <span>Tableau de bord</span>
        </a>

        <div class="sidebar-divider"></div>

        <!-- Valider Livraison -->
        <a href="{{ route('livreur.validated') }}"
            class="sidebar-link {{ request()->routeIs('livreur.validated') ? 'active' : '' }}"
            title="Valider la livraison">
            <i class="material-icons">check_circle</i>
            <span>Valider la livraison</span>
        </a>

        <!-- Colis Group -->
        <div class="sidebar-menu-item">
            <a href="#"
                class="sidebar-link has-submenu {{ request()->routeIs('livreur.livraison') || request()->routeIs('livreur.livree') ? 'active-parent' : '' }}"
                data-submenu="submenu-colis" title="Gestion Colis">
                <i class="material-icons">inventory_2</i>
                <span>Gestion Colis</span>
                <i class="material-icons chevron">chevron_right</i>
            </a>
            <div class="sidebar-submenu {{ request()->routeIs('livreur.livraison') || request()->routeIs('livreur.livree') ? 'active' : '' }}"
                id="submenu-colis">
                <a href="{{ route('livreur.livraison') }}"
                    class="sidebar-sublink {{ request()->routeIs('livreur.livraison') ? 'active' : '' }}">
                    <span>Colis à livrer</span>
                </a>
                <a href="{{ route('livreur.livree') }}"
                    class="sidebar-sublink {{ request()->routeIs('livreur.livree') ? 'active' : '' }}">
                    <span>Colis livrés</span>
                </a>
            </div>
        </div>
    </nav>
</aside>

<style>
    /* ========== SYNCED MODERN SIDEBAR STYLES (Livreur) ========== */
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
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
    }

    /* Règles pour l'état réduit (sidebar-mini) */
    body.sidebar-mini .sidebar-header {
        padding: 20px 5px;
    }
    body.sidebar-mini .sidebar-logo img {
        width: 40px;
    }
    body.sidebar-mini .sidebar-user-info, 
    body.sidebar-mini .sidebar-link span, 
    body.sidebar-mini .sidebar-link .chevron,
    body.sidebar-mini .sidebar-divider,
    body.sidebar-mini .sidebar-submenu {
        display: none !important;
    }
    body.sidebar-mini .sidebar-link {
        justify-content: center;
        margin: 4px 10px;
        padding: 12px 0;
        width: 60px;
    }
    body.sidebar-mini .sidebar-link i {
        margin-right: 0;
    }

    .custom-sidebar::-webkit-scrollbar {
        width: 5px;
    }

    .custom-sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }

    .custom-sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
    }

    /* Sidebar Header */
    .sidebar-header {
        padding: 35px 20px;
        background: rgba(0, 0, 0, 0.12);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        text-align: center;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .sidebar-logo {
        display: inline-block;
        margin-bottom: 15px;
        transition: transform 0.3s ease;
    }

    .sidebar-logo img {
        width: 75px;
        height: auto;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    }

    .sidebar-user-info .user-name {
        font-size: 15px;
        font-weight: 700;
        color: #ffffff;
        margin: 0 0 4px 0;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .sidebar-user-info .user-email {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.6);
        margin: 0;
        font-weight: 400;
        white-space: nowrap;
    }

    /* Navigation */
    .sidebar-nav {
        padding: 20px 0;
    }

    .sidebar-divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.08);
        margin: 15px 20px;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: rgba(255, 255, 255, 0.75);
        text-decoration: none !important;
        transition: all 0.2s ease;
        margin: 4px 15px;
        border-radius: 12px;
        position: relative;
        white-space: nowrap;
        overflow: hidden;
    }

    .sidebar-link:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }

    .sidebar-link.active,
    .sidebar-link.active-parent {
        background: #ffffff;
        color: #1f4083 !important;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .sidebar-link i.material-icons {
        font-size: 22px;
        margin-right: 14px;
        transition: transform 0.3s ease;
        min-width: 24px;
    }

    .chevron {
        margin-left: auto;
        font-size: 18px !important;
    }

    /* Submenu */
    .sidebar-submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-out;
        background: rgba(0, 0, 0, 0.1);
        margin: 0 15px;
        border-radius: 0 0 12px 12px;
    }

    .sidebar-submenu.active {
        max-height: 500px;
        padding: 8px 0;
    }

    .sidebar-sublink {
        display: block;
        padding: 10px 20px 10px 52px;
        color: rgba(255, 255, 255, 0.65);
        text-decoration: none !important;
        font-size: 13px;
        transition: all 0.2s;
        border-radius: 8px;
        white-space: nowrap;
    }

    @media (max-width: 991px) {
        .custom-sidebar {
            transform: translateX(-100%);
        }
        body.sidebar-mini .custom-sidebar {
            transform: translateX(0);
        }
    }
</style>
