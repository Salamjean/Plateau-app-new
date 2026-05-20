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
                    <h1 class="brand-title">Espace État Civil</h1>
                    <span class="brand-subtitle">Responsable</span>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="navbar-right">
            <!-- User Profile -->
            <div class="navbar-profile-wrapper">
                <button class="navbar-profile-btn" id="profileMenuBtn">
                    <div class="profile-avatar">
                        <img src="{{ optional(Auth::guard('etatCivil')->user())->profile_picture
                            ? asset('storage/' . Auth::guard('etatCivil')->user()->profile_picture)
                            : asset('assets/assets/img/logo plateau.png') }}"
                            alt="Avatar" />
                        <span class="status-indicator"></span>
                    </div>
                    <div class="profile-info">
                        <span class="profile-name">{{ Auth::guard('etatCivil')->user()->name_respo }}</span>
                        <span class="profile-role">Responsable</span>
                    </div>
                    <i class="material-icons profile-arrow">keyboard_arrow_down</i>
                </button>

                <!-- Dropdown Menu -->
                <div class="modern-dropdown" id="profileDropdown">
                    <div class="dropdown-user-header">
                        <div class="dropdown-avatar-wrapper">
                            <img src="{{ optional(Auth::guard('etatCivil')->user())->profile_picture
                                ? asset('storage/' . Auth::guard('etatCivil')->user()->profile_picture)
                                : asset('assets/assets/img/logo plateau.png') }}"
                                alt="Avatar" />
                        </div>
                        <div class="dropdown-user-details">
                            <h6 class="dropdown-username">{{ Auth::guard('etatCivil')->user()->name_respo }}</h6>
                            <p class="dropdown-useremail">{{ Auth::guard('etatCivil')->user()->email }}</p>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <ul class="dropdown-menu-list">
                        <li>
                            <a href="{{ route('etat_civil.logout') }}" class="dropdown-item logout-item">
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
    /* ========== MODERN NAVBAR STYLES (Copied from Agent) ========== */
    .modern-navbar {
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        position: fixed;
        top: 0;
        right: 0;
        left: 260px;
        z-index: 1000;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid #e2e8f0;
        transition: left 0.3s ease;
    }

    .body-wrapper.sidebar-collapsed .modern-navbar {
        left: 70px;
    }

    @media (max-width: 991px) {
        .modern-navbar {
            left: 0 !important;
        }
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
        transition: all 0.2s ease;
    }

    .menu-toggle-btn:hover {
        background: #1f4083;
        color: #ffffff;
        border-color: #1f4083;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .navbar-logo {
        height: 40px;
        width: auto;
    }

    .brand-text {
        display: flex;
        flex-direction: column;
    }

    .brand-title {
        font-size: 18px;
        font-weight: 800;
        color: #1f4083;
        margin: 0;
        line-height: 1.2;
    }

    .brand-subtitle {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Right Section */
    .navbar-right {
        display: flex;
        align-items: center;
        gap: 15px;
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
        padding: 6px 12px 6px 6px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.2s ease;
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
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .status-indicator {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 12px;
        height: 12px;
        background: #10b981;
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
        font-weight: 700;
        color: #1f4083;
        line-height: 1;
        margin-bottom: 2px;
    }

    .profile-role {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
    }

    .profile-arrow {
        color: #94a3b8;
        font-size: 20px;
        transition: transform 0.2s ease;
    }

    /* Dropdown Menu */
    .modern-dropdown {
        position: absolute;
        top: calc(100% + 15px);
        right: 0;
        width: 280px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        border: 1px solid #e2e8f0;
        visibility: hidden;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .modern-dropdown.show {
        visibility: visible;
        opacity: 1;
        transform: translateY(0);
    }

    .dropdown-user-header {
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        background: #f8fafc;
        border-radius: 16px 16px 0 0;
    }

    .dropdown-avatar-wrapper {
        width: 50px;
        height: 50px;
    }

    .dropdown-avatar-wrapper img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ffffff;
    }

    .dropdown-user-details h6 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #1f4083;
    }

    .dropdown-useremail {
        margin: 0;
        font-size: 12px;
        color: #64748b;
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
        padding: 12px 16px;
        color: #475569;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background: #f1f5f9;
        color: #1f4083;
    }

    .dropdown-item i {
        font-size: 20px;
        color: #64748b;
    }

    .logout-item {
        color: #ef4444;
    }

    .logout-item:hover {
        background: #fef2f2;
        color: #ef4444;
    }

    .logout-item i {
        color: #ef4444;
    }

    @media (max-width: 768px) {

        .navbar-brand,
        .profile-info,
        .profile-arrow {
            display: none;
        }

        .navbar-container {
            padding: 8px 16px;
        }

        .navbar-profile-btn {
            padding: 4px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileBtn = document.getElementById('profileMenuBtn');
        const dropdown = document.getElementById('profileDropdown');

        if (profileBtn && dropdown) {
            profileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target) && !profileBtn.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        }
    });

    // Handle Sidebar Toggle (Mobile and Desktop)
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggler = document.querySelector('.sidebar-toggler');
        const sidebar = document.querySelector('.custom-sidebar');
        const bodyWrapper = document.querySelector('.body-wrapper');

        if (sidebarToggler && sidebar && bodyWrapper) {
            sidebarToggler.addEventListener('click', function(e) {
                e.preventDefault();

                // On mobile: toggle 'active' class to show/hide
                if (window.innerWidth <= 991) {
                    sidebar.classList.toggle('active');
                } else {
                    // On desktop: toggle 'sidebar-collapsed' class on wrapper
                    bodyWrapper.classList.toggle('sidebar-collapsed');
                }
            });
        }
    });
</script>
