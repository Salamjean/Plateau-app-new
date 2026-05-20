<aside class="custom-sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <a href="{{ route('etat_civil.dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('assets/assets/img/logo plateau.png') }}" alt="Logo Plateau" />
        </a>
        <div class="sidebar-user-info">
            <p class="user-name">Espace : État Civil</p>
            <p class="user-email">{{ Auth::guard('etatCivil')->user()->email }}</p>
        </div>
    </div>

    <!-- Sidebar Content -->
    <nav class="sidebar-nav">
        <!-- Dashboard -->
        <a href="{{ route('etat_civil.dashboard') }}"
            class="sidebar-link {{ request()->routeIs('etat_civil.dashboard') ? 'active' : '' }}"
            title="Tableau de bord">
            <i class="material-icons">home</i>
            <span>Tableau de bord</span>
        </a>

        <div class="sidebar-divider"></div>

        <!-- Agent Management -->
        <div class="sidebar-group">
            <div class="sidebar-link has-submenu {{ request()->routeIs('etat_civil.agent.state.*') ? 'active' : '' }}"
                onclick="toggleSubmenu(this)">
                <i class="material-icons">people</i>
                <span>Gestion des Agents</span>
                <i class="material-icons submenu-arrow">expand_more</i>
            </div>
            <div class="sidebar-submenu {{ request()->routeIs('etat_civil.agent.state.*') ? 'show' : '' }}">
                <a href="{{ route('etat_civil.agent.state.index') }}"
                    class="submenu-link {{ request()->routeIs('etat_civil.agent.state.index') ? 'active' : '' }}">
                    <i class="material-icons">list</i>
                    <span>Liste des Agents</span>
                </a>
                <a href="{{ route('etat_civil.agent.state.create') }}"
                    class="submenu-link {{ request()->routeIs('etat_civil.agent.state.create') ? 'active' : '' }}">
                    <i class="material-icons">person_add</i>
                    <span>Ajouter un Agent</span>
                </a>
            </div>
        </div>

        <div class="sidebar-divider"></div>

        <!-- Extraits / Actes -->
        <a href="{{ route('etat_civil.request.birth') }}"
            class="sidebar-link {{ request()->routeIs('etat_civil.request.birth') ? 'active' : '' }}"
            title="Actes de Naissance">
            <i class="material-icons">child_care</i>
            <span>Actes de Naissance</span>
        </a>

        <a href="{{ route('etat_civil.request.death') }}"
            class="sidebar-link {{ request()->routeIs('etat_civil.request.death') ? 'active' : '' }}"
            title="Actes de Décès">
            <i class="material-icons">spa</i>
            <span>Actes de Décès</span>
        </a>

        <a href="{{ route('etat_civil.request.wedding') }}"
            class="sidebar-link {{ request()->routeIs('etat_civil.request.wedding') ? 'active' : '' }}"
            title="Actes de Mariage">
            <i class="material-icons">favorite</i>
            <span>Actes de Mariage</span>
        </a>

        <div class="sidebar-divider"></div>

        <!-- Historiques -->
        <a href="{{ route('etat_civil.history.taskend') }}"
            class="sidebar-link {{ request()->routeIs('etat_civil.history.taskend') ? 'active' : '' }}"
            title="Historique Global">
            <i class="material-icons">history</i>
            <span>Historique Global</span>
        </a>

        <a href="{{ route('etat_civil.livree.taskend') }}"
            class="sidebar-link {{ request()->routeIs('etat_civil.livree.taskend') ? 'active' : '' }}"
            title="Actes Livrés">
            <i class="material-icons">assignment_turned_in</i>
            <span>Actes Livrés</span>
        </a>

        <div class="sidebar-divider"></div>

        <!-- Rapports -->
        <a href="{{ route('etatCivil.rapports') }}"
            class="sidebar-link {{ request()->routeIs('etatCivil.rapports') ? 'active' : '' }}"
            title="Rapports d'Activité">
            <i class="material-icons">assessment</i>
            <span>Rapports d'Activité</span>
        </a>
    </nav>
</aside>

<script>
    function toggleSubmenu(element) {
        const submenu = element.nextElementSibling;
        const arrow = element.querySelector('.submenu-arrow');

        if (submenu.classList.contains('show')) {
            submenu.classList.remove('show');
            element.classList.remove('open');
            submenu.style.display = "none";
        } else {
            submenu.classList.add('show');
            element.classList.add('open');
            submenu.style.display = "block";
        }
    }
</script>

<style>
    /* Custom Sidebar Styles - Copied from Agent for Consistency */
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

    .sidebar-divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.1);
        margin: 10px 20px;
    }

    .sidebar-link i {
        margin-right: 15px;
        font-size: 22px;
    }

    .sidebar-link span {
        font-size: 14px;
        white-space: nowrap;
    }

    /* Submenu Styles */
    .sidebar-submenu {
        display: none;
        background: rgba(0, 0, 0, 0.15);
        margin: 0 12px;
        border-radius: 8px;
        padding: 5px 0;
    }

    .sidebar-submenu.show {
        display: block;
    }

    .submenu-link {
        display: flex;
        align-items: center;
        padding: 10px 15px 10px 35px;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
        border-radius: 6px;
        margin: 2px 5px;
    }

    .submenu-link:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }

    .submenu-link.active {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.12);
        font-weight: 600;
    }

    .submenu-link i {
        font-size: 18px;
        margin-right: 12px;
    }

    .submenu-arrow {
        margin-left: auto;
        margin-right: 0 !important;
        transition: transform 0.3s;
    }

    .has-submenu.open .submenu-arrow {
        transform: rotate(180deg);
    }

    @media (max-width: 991px) {
        .custom-sidebar {
            left: -260px;
        }

        .custom-sidebar.active {
            left: 0;
        }
    }

    /* Styles pour la sidebar réduite (collapsed) sur Desktop */
    @media (min-width: 992px) {
        .body-wrapper.sidebar-collapsed .custom-sidebar {
            width: 70px;
        }

        .body-wrapper.sidebar-collapsed .custom-sidebar .sidebar-user-info,
        .body-wrapper.sidebar-collapsed .custom-sidebar .sidebar-link span,
        .body-wrapper.sidebar-collapsed .custom-sidebar .sidebar-divider {
            display: none;
        }

        .body-wrapper.sidebar-collapsed .custom-sidebar .sidebar-logo img {
            width: 40px;
        }

        .body-wrapper.sidebar-collapsed .custom-sidebar .sidebar-link {
            justify-content: center;
            padding: 12px;
            margin: 4px 8px;
        }

        .body-wrapper.sidebar-collapsed .custom-sidebar .sidebar-link i {
            margin-right: 0;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cette partie sera gérée via le script dans la navbar ou le template 
        // pour assurer une synchronisation avec le main-wrapper
    });
</script>
