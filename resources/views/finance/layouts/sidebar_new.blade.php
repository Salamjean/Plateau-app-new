<aside class="custom-sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <a href="{{ route('finance.dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('assets/assets/img/logo plateau.png') }}" alt="Logo Plateau" />
        </a>
        <div class="sidebar-user-info">
            <p class="user-name">Régie des taxes : Plateau</p>
            <p class="user-email">{{ Auth::guard('finance')->user()->email }}</p>
        </div>
    </div>

    <!-- Sidebar Content -->
    <nav class="sidebar-nav">
        <!-- Dashboard -->
        <a href="{{ route('finance.dashboard') }}"
            class="sidebar-link {{ request()->routeIs('finance.dashboard') ? 'active' : '' }}" title="Tableau de bord">
            <i class="material-icons">home</i>
            <span>Tableau de bord</span>
        </a>

        <div class="sidebar-divider"></div>

        <!-- Ajout Timbre (Recharger) -->
        <a href="{{ route('finance.timbre.recharge') }}"
            class="sidebar-link {{ request()->routeIs('finance.timbre.recharge') ? 'active' : '' }}"
            title="Ajout Timbre">
            <i class="material-icons">add_circle</i>
            <span>Ajouter des Timbres</span>
        </a>

        <!-- Timbre Vendu (Vente) -->
        <a href="{{ route('finance.timbre.vente') }}"
            class="sidebar-link {{ request()->routeIs('finance.timbre.vente') ? 'active' : '' }}" title="Timbre Vendu">
            <i class="material-icons">shopping_cart</i>
            <span>Timbre Vendu</span>
        </a>

        <!-- Historique Générale -->
        <a href="{{ route('finance.timbre.history') }}"
            class="sidebar-link {{ request()->routeIs('finance.timbre.history') ? 'active' : '' }}"
            title="Historique Générale">
            <i class="material-icons">history</i>
            <span>Historique Générale</span>
        </a>

        <div class="sidebar-divider"></div>

        <!-- Ajout Régie -->
        <a href="{{ route('comptable.create') }}"
            class="sidebar-link {{ request()->routeIs('comptable.create') ? 'active' : '' }}" title="Ajout Régie">
            <i class="material-icons">person_add</i>
            <span>Ajout Régie</span>
        </a>

        <!-- Liste des Régies -->
        <a href="{{ route('comptable.index') }}"
            class="sidebar-link {{ request()->routeIs('comptable.index') || request()->routeIs('comptable.edit') ? 'active' : '' }}"
            title="Régies">
            <i class="material-icons">people</i>
            <span>Régies</span>
        </a>

        <!-- Portefeuille en ligne -->
        <div class="sidebar-divider"></div>
        <a href="{{ route('finance.portefeuille.index') }}"
            class="sidebar-link {{ request()->routeIs('finance.portefeuille.index') || request()->routeIs('finance.portefeuille.historique') ? 'active' : '' }}"
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

    /* Main Content Adjustment */
    .main-wrapper {
        margin-left: 260px;
        transition: margin-left 0.3s ease;
    }

    .custom-sidebar.collapsed~.main-wrapper {
        margin-left: 70px;
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
