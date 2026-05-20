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
                    <h1 class="brand-title">Agent - État Civil</h1>
                    <span class="brand-subtitle">Espace Agent</span>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="navbar-right">
            <!-- User Profile -->
            <div class="navbar-profile-wrapper">
                <button class="navbar-profile-btn" id="profileMenuBtn">
                    <div class="profile-avatar">
                        <img src="{{ optional(Auth::guard('agent')->user())->profile_picture
                            ? asset('storage/' . Auth::guard('agent')->user()->profile_picture)
                            : asset('assets/assets/img/logo plateau.png') }}"
                            alt="Avatar" />
                        <span class="status-indicator"></span>
                    </div>
                    <div class="profile-info">
                        <span class="profile-name">{{ Auth::guard('agent')->user()->name }}
                            {{ Auth::guard('agent')->user()->prenom }}</span>
                        <span class="profile-role">Agent</span>
                    </div>
                    <i class="material-icons profile-arrow">keyboard_arrow_down</i>
                </button>

                <!-- Dropdown Menu -->
                <div class="modern-dropdown" id="profileDropdown">
                    <div class="dropdown-user-header">
                        <div class="dropdown-avatar-wrapper">
                            <img src="{{ optional(Auth::guard('agent')->user())->profile_picture
                                ? asset('storage/' . Auth::guard('agent')->user()->profile_picture)
                                : asset('assets/assets/img/logo plateau.png') }}"
                                alt="Avatar" />
                        </div>
                        <div class="dropdown-user-details">
                            <h6 class="dropdown-username">{{ Auth::guard('agent')->user()->name }}
                                {{ Auth::guard('agent')->user()->prenom }}</h6>
                            <p class="dropdown-useremail">{{ Auth::guard('agent')->user()->email }}</p>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <ul class="dropdown-menu-list">
                        <li>
                            <a href="{{ route('agent.logout') }}" class="dropdown-item logout-item">
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
        bottom: 0;
        right: 0;
        width: 12px;
        height: 12px;
        background: #2ecc71;
        border: 2px solid #1f4083;
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
        color: #718096;
        font-size: 20px;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Dropdown Menu */
    .modern-dropdown {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        min-width: 280px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 12px 48px rgba(0, 0, 0, 0.18);
        overflow: hidden;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-15px) scale(0.95);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
        z-index: 1000;
    }

    .modern-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: all;
    }

    .dropdown-user-header {
        background: linear-gradient(135deg, #1f4083 0%, #2d5aa8 100%);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .dropdown-avatar-wrapper {
        flex-shrink: 0;
    }

    .dropdown-avatar-wrapper img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.9);
        background: white;
        object-fit: cover;
        display: block;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .dropdown-user-details {
        flex: 1;
        min-width: 0;
    }

    .dropdown-username {
        font-size: 15px;
        font-weight: 700;
        color: white;
        margin: 0 0 4px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dropdown-useremail {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.85);
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dropdown-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #e2e8f0, transparent);
        margin: 0;
    }

    .dropdown-menu-list {
        padding: 12px;
        margin: 0;
        list-style: none;
    }

    .dropdown-menu-list li {
        list-style: none;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 16px;
        color: #2d3748;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.2s ease;
        font-size: 14px;
        font-weight: 500;
    }

    .dropdown-item:hover {
        background: #f7fafc;
        transform: translateX(4px);
    }

    .dropdown-item i {
        font-size: 22px;
        color: #718096;
        transition: all 0.2s ease;
    }

    .logout-item {
        color: #e53e3e;
        font-weight: 600;
    }

    .logout-item:hover {
        background: linear-gradient(135deg, #fff5f5 0%, #fee 100%);
        color: #c53030;
    }

    .logout-item i {
        color: #e53e3e;
    }

    .logout-item:hover i {
        color: #c53030;
        transform: rotate(90deg);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .brand-text {
            display: none;
        }

        .profile-info {
            display: none;
        }

        .navbar-profile-btn {
            padding: 6px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileBtn = document.getElementById('profileMenuBtn');
        const dropdown = document.getElementById('profileDropdown');
        const profileArrow = profileBtn.querySelector('.profile-arrow');

        // Toggle dropdown on button click
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');

            // Rotate arrow
            if (dropdown.classList.contains('show')) {
                profileArrow.style.transform = 'rotate(180deg)';
            } else {
                profileArrow.style.transform = 'rotate(0deg)';
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
                profileArrow.style.transform = 'rotate(0deg)';
            }
        });

        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
                profileArrow.style.transform = 'rotate(0deg)';
            }
        });

        // Sidebar toggle functionality
        const sidebarToggler = document.querySelector('.sidebar-toggler');
        const sidebar = document.querySelector('.custom-sidebar');
        const mainWrapper = document.querySelector('.main-wrapper');

        if (sidebarToggler && sidebar) {
            sidebarToggler.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');

                // Adjust main wrapper margin
                if (mainWrapper) {
                    if (sidebar.classList.contains('collapsed')) {
                        mainWrapper.style.marginLeft = '70px';
                    } else {
                        mainWrapper.style.marginLeft = '260px';
                    }
                }
            });
        }
    });
</script>
