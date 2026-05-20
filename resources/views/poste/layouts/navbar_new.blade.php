<header class="custom-navbar">
    <div class="navbar-left">
        <button class="menu-toggle" id="sidebarToggle">
            <i class="material-icons">menu</i>
        </button>
        <div class="navbar-brand">
            <img src="{{ asset('assets/assets/img/logo plateau.png') }}" alt="Logo Plateau" class="navbar-logo" />
            <div class="brand-text">
                <h1 class="brand-title">Courrier</h1>
                <span class="brand-subtitle">Espace Distribution</span>
            </div>
        </div>
    </div>

    <div class="navbar-right">
        <!-- User Profile -->
        <div class="navbar-profile-wrapper">
            <button class="navbar-profile-btn" id="profileMenuBtn">
                <div class="profile-avatar">
                    <img src="{{ asset('assets/assets/img/logo plateau.png') }}" alt="Avatar" />
                    <span class="status-indicator"></span>
                </div>
                <div class="profile-info">
                    <span class="profile-name">{{ Auth::guard('poste')->user()->name }}</span>
                    <span class="profile-role">Service Courrier</span>
                </div>
                <i class="material-icons profile-arrow">keyboard_arrow_down</i>
            </button>

            <!-- Dropdown Menu -->
            <div class="modern-dropdown" id="profileDropdown">
                <div class="dropdown-user-header">
                    <div class="dropdown-avatar-wrapper">
                        <img src="{{ asset('assets/assets/img/logo plateau.png') }}" alt="Avatar" />
                    </div>
                    <div class="dropdown-user-details">
                        <h6 class="dropdown-username">{{ Auth::guard('poste')->user()->name }}</h6>
                        <p class="dropdown-useremail">Connect�</p>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <ul class="dropdown-menu-list">
                    <li>
                        <form action="{{ route('post.logout') }}" method="GET" style="margin:0;">
                            @csrf
                            <button type="submit" class="dropdown-item logout-item"
                                style="width:100%; border:none; background:none; text-align:left; cursor:pointer; display:flex; align-items:center; gap:10px; padding:12px 20px; color:#e53e3e; font-weight:500;">
                                <i class="material-icons">power_settings_new</i>
                                <span>Se d�connecter</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<style>
    /* ========== SYNCED MODERN NAVBAR STYLES FOR POSTE ========== */
    .custom-navbar {
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        position: fixed;
        top: 0;
        left: var(--sidebar-width, 260px);
        right: 0;
        z-index: 998;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        height: 70px;
    }

    .navbar-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .menu-toggle {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        color: #1f4083;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .menu-toggle:hover {
        background: #1f4083;
        color: #ffffff;
        transform: scale(1.05);
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .navbar-logo {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: white;
        padding: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .brand-text {
        display: flex;
        flex-direction: column;
    }

    .brand-title {
        font-size: 18px;
        font-weight: 700;
        color: #1f4083;
        margin: 0;
        line-height: 1.2;
    }

    .brand-subtitle {
        font-size: 11px;
        color: #718096;
        font-weight: 500;
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .navbar-profile-wrapper {
        position: relative;
    }

    .navbar-profile-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 6px 16px 6px 6px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .navbar-profile-btn:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .profile-avatar {
        position: relative;
        width: 38px;
        height: 38px;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .status-indicator {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 12px;
        height: 12px;
        background: #48bb78;
        border: 2px solid #ffffff;
        border-radius: 50%;
    }

    .profile-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .profile-name {
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
        line-height: 1;
    }

    .profile-role {
        font-size: 11px;
        color: #718096;
        font-weight: 500;
    }

    .profile-arrow {
        font-size: 18px;
        color: #94a3b8;
        transition: transform 0.3s ease;
    }


    .modern-dropdown {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        width: 250px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        border: 1px solid #e2e8f0;
        padding: 8px;
        visibility: hidden;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s ease;
    }

    .navbar-profile-wrapper:hover .modern-dropdown {
        visibility: visible;
        opacity: 1;
        transform: translateY(0);
    }

    .navbar-profile-wrapper:hover .profile-arrow {
        transform: rotate(180deg);
    }

    .dropdown-user-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #f8fafc;
        border-radius: 12px;
        margin-bottom: 8px;
    }

    .dropdown-avatar-wrapper {
        width: 40px;
        height: 40px;
    }

    .dropdown-avatar-wrapper img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }

    .dropdown-username {
        font-size: 14px;
        font-weight: 700;
        color: #1a202c;
        margin: 0;
    }

    .dropdown-useremail {
        font-size: 11px;
        color: #718096;
        margin: 0;
    }

    .dropdown-divider {
        height: 1px;
        background: #edf2f7;
        margin: 8px 0;
    }

    .dropdown-menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    @media (max-width: 991px) {
        .custom-navbar {
            left: 0;
        }
    }
</style>
