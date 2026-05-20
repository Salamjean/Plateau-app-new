<nav class="modern-navbar">
    <div class="navbar-container">
        <!-- Toggle Button -->
        <button class="navbar-toggle" id="sidebarToggle" title="Menu">
            <i class="material-icons">menu</i>
        </button>

        <!-- Right Section -->
        <div class="navbar-right">
            <!-- Profile Dropdown -->
            <div class="profile-dropdown">
                <button class="profile-trigger" id="profileTrigger">
                    <div class="profile-avatar">
                        <img src="{{ optional(Auth::guard('comptable')->user())->profile_picture
                            ? asset('storage/' . Auth::guard('comptable')->user()->profile_picture)
                            : asset('assets/assets/img/logo plateau.png') }}"
                            alt="Avatar" />
                    </div>
                    <div class="profile-info">
                        <span class="profile-name">{{ Auth::guard('comptable')->user()->name }}
                            {{ Auth::guard('comptable')->user()->prenom }}</span>
                        <span class="profile-role">Comptable</span>
                    </div>
                    <i class="material-icons dropdown-icon">keyboard_arrow_down</i>
                </button>

                <!-- Dropdown Menu -->
                <div class="profile-menu" id="profileMenu">
                    <div class="profile-menu-header">
                        <div class="profile-menu-avatar">
                            <img src="{{ optional(Auth::guard('comptable')->user())->profile_picture
                                ? asset('storage/' . Auth::guard('comptable')->user()->profile_picture)
                                : asset('assets/assets/img/logo plateau.png') }}"
                                alt="Avatar" />
                        </div>
                        <div class="profile-menu-info">
                            <h4>{{ Auth::guard('comptable')->user()->name }}
                                {{ Auth::guard('comptable')->user()->prenom }}</h4>
                            <p>{{ Auth::guard('comptable')->user()->email }}</p>
                        </div>
                    </div>
                    <div class="profile-menu-divider"></div>
                    <a href="{{ route('comptable.logout') }}" class="profile-menu-item">
                        <i class="material-icons">logout</i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Modern Navbar Styles */
    .modern-navbar {
        position: fixed;
        top: 0;
        left: 260px;
        right: 0;
        height: 70px;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        z-index: 999;
        transition: left 0.3s ease;
    }

    .custom-sidebar.collapsed~.main-wrapper .modern-navbar {
        left: 70px;
    }

    .navbar-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
        padding: 0 2rem;
    }

    /* Toggle Button */
    .navbar-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: none;
        background: transparent;
        color: #1f4083;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .navbar-toggle:hover {
        background: rgba(31, 64, 131, 0.1);
    }

    .navbar-toggle i {
        font-size: 24px;
    }

    /* Right Section */
    .navbar-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    /* Profile Dropdown */
    .profile-dropdown {
        position: relative;
    }

    .profile-trigger {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px;
        background: transparent;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .profile-trigger:hover {
        background: rgba(31, 64, 131, 0.05);
    }

    .profile-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #1f4083;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .profile-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
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
        line-height: 1.2;
    }

    .dropdown-icon {
        font-size: 20px;
        color: #718096;
        transition: transform 0.3s ease;
    }

    .profile-dropdown.active .dropdown-icon {
        transform: rotate(180deg);
    }

    /* Profile Menu */
    .profile-menu {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 280px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .profile-dropdown.active .profile-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .profile-menu-header {
        background: linear-gradient(135deg, #1f4083 0%, #2d5aa8 100%);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .profile-menu-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid rgba(255, 255, 255, 0.3);
        flex-shrink: 0;
    }

    .profile-menu-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .profile-menu-info {
        flex: 1;
        min-width: 0;
    }

    .profile-menu-info h4 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: white;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-menu-info p {
        margin: 4px 0 0;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.8);
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-menu-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 0;
    }

    .profile-menu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: #2d3748;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .profile-menu-item:hover {
        background: rgba(31, 64, 131, 0.05);
        color: #1f4083;
    }

    .profile-menu-item i {
        font-size: 20px;
        color: inherit;
    }

    .profile-menu-item span {
        font-size: 14px;
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .modern-navbar {
            left: 0;
        }

        .navbar-container {
            padding: 0 1rem;
        }

        .profile-info {
            display: none;
        }

        .profile-menu {
            right: -10px;
        }
    }

    /* Page Wrapper Adjustment */
    .page-wrapper {
        padding-top: 90px;
        min-height: 100vh;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.custom-sidebar');

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });
        }

        // Profile Dropdown Toggle
        const profileTrigger = document.getElementById('profileTrigger');
        const profileDropdown = document.querySelector('.profile-dropdown');

        if (profileTrigger && profileDropdown) {
            profileTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('active');
                }
            });
        }
    });
</script>
