<header class="modern-navbar">
    <div class="navbar-container">
        <!-- Left Section -->
        <div class="navbar-left">
            <button class="menu-toggle-btn sidebar-toggler">
                <i class="material-icons">menu</i>
            </button>

            <div class="navbar-brand">
                <img src="{{ asset('assets/assets/img/logo plateau.png') }}" alt="Logo Plateau" class="navbar-logo" />
                <div class="brand-text">
                    <h1 class="brand-title">Régie des taxes</h1>
                    <span class="brand-subtitle">Espace Finance</span>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="navbar-right">
            <!-- User Profile -->
            <div class="navbar-profile-wrapper">
                <button class="navbar-profile-btn" id="profileMenuBtn">
                    <div class="profile-avatar">
                        <img src="{{ optional(Auth::guard('finance')->user())->profile_picture
                            ? asset('storage/' . Auth::guard('finance')->user()->profile_picture)
                            : asset('assets/assets/img/logo plateau.png') }}"
                            alt="Avatar" />
                        <span class="status-indicator"></span>
                    </div>
                    <div class="profile-info">
                        <span class="profile-name">{{ Auth::guard('finance')->user()->name }}
                            {{ Auth::guard('finance')->user()->prenom }}</span>
                        <span class="profile-role">Régie des taxes</span>
                    </div>
                    <i class="material-icons profile-arrow">keyboard_arrow_down</i>
                </button>

                <!-- Dropdown Menu -->
                <div class="modern-dropdown" id="profileDropdown">
                    <div class="dropdown-user-header">
                        <div class="dropdown-avatar-wrapper">
                            <img src="{{ optional(Auth::guard('finance')->user())->profile_picture
                                ? asset('storage/' . Auth::guard('finance')->user()->profile_picture)
                                : asset('assets/assets/img/logo plateau.png') }}"
                                alt="Avatar" />
                        </div>
                        <div class="dropdown-user-details">
                            <h6 class="dropdown-username">{{ Auth::guard('finance')->user()->name }}
                                {{ Auth::guard('finance')->user()->prenom }}</h6>
                            <p class="dropdown-useremail">{{ Auth::guard('finance')->user()->email }}</p>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <ul class="dropdown-menu-list">
                        <li>
                            <a href="{{ route('finance.logout') }}" class="dropdown-item logout-item">
                                <i class="material-icons">power_settings_new</i>
                                <span>Se déconnecter</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
    /* ========== MODERN NAVBAR STYLES ========== */
    .modern-navbar {
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 0;
        z-index: 1000;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid #e2e8f0;
    }

    .navbar-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 24px;
        max-width: 100%;
    }

    /* Left Section */
    .navbar-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .menu-toggle-btn {
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

    .menu-toggle-btn:hover {
        background: #1f4083;
        color: white;
        transform: scale(1.05);
    }

    .menu-toggle-btn i {
        font-size: 24px;
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
        font-size: 12px;
        color: #718096;
        font-weight: 500;
    }

    /* Right Section */
    .navbar-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    /* Profile Section */
    .navbar-profile-wrapper {
        position: relative;
    }

    .navbar-profile-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 50px;
        padding: 6px 16px 6px 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .navbar-profile-btn:hover {
        background: #edf2f7;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .profile-avatar {
        position: relative;
        width: 40px;
        height: 40px;
        flex-shrink: 0;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 2px solid #1f4083;
        background: white;
        object-fit: cover;
        display: block;
    }

    .status-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 10px;
        height: 10px;
        background: #38a169;
        border: 2px solid white;
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
        line-height: 1.2;
    }

    .profile-role {
        font-size: 12px;
        color: #718096;
    }

    .profile-arrow {
        font-size: 20px;
        color: #718096;
        transition: transform 0.3s ease;
    }

    .navbar-profile-btn:hover .profile-arrow {
        transform: rotate(180deg);
    }

    /* Dropdown Menu */
    .modern-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        background: white;
        border-radius: 16px;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        min-width: 280px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 2000;
    }

    .modern-dropdown.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-user-header {
        background: linear-gradient(135deg, #1f4083 0%, #2d5aa8 100%);
        padding: 20px;
        border-radius: 16px 16px 0 0;
        display: flex;
        gap: 14px;
        align-items: center;
    }

    .dropdown-avatar-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.3);
        overflow: hidden;
        flex-shrink: 0;
    }

    .dropdown-avatar-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .dropdown-user-details {
        flex: 1;
    }

    .dropdown-username {
        font-size: 16px;
        font-weight: 700;
        color: white;
        margin: 0 0 4px 0;
    }

    .dropdown-useremail {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.85);
        margin: 0;
    }

    .dropdown-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 0;
    }

    .dropdown-menu-list {
        list-style: none;
        padding: 8px;
        margin: 0;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        color: #2d3748;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.2s ease;
        font-size: 14px;
        font-weight: 500;
    }

    .dropdown-item:hover {
        background: #f7fafc;
        color: #1f4083;
        transform: translateX(4px);
    }

    .dropdown-item i {
        font-size: 20px;
        color: #718096;
    }

    .dropdown-item:hover i {
        color: #1f4083;
    }

    .logout-item {
        color: #e53e3e;
    }

    .logout-item:hover {
        background: #fff5f5;
        color: #c53030;
    }

    .logout-item i {
        color: #e53e3e;
    }

    .logout-item:hover i {
        color: #c53030;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .navbar-container {
            padding: 10px 16px;
        }

        .brand-text {
            display: none;
        }

        .profile-info {
            display: none;
        }

        .profile-arrow {
            display: none;
        }

        .navbar-profile-btn {
            padding: 6px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile Dropdown Toggle
        const profileBtn = document.getElementById('profileMenuBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('active');
                }
            });
        }

        // Sidebar Toggle
        const sidebarToggler = document.querySelector('.sidebar-toggler');
        const sidebar = document.querySelector('.custom-sidebar');

        if (sidebarToggler && sidebar) {
            sidebarToggler.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });
        }
    });
</script>
