<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('assets/assets/img/logo plateau.png') }}" />
    <title>Connexion - Plateau App</title>
    <style>
        :root {
            --primary-color: #1f4083;
            --secondary-color: #1f4083;
            --accent-color: #1f4083;
            --error-color: #f72585;
            --success-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --text-muted: #6c757d;
            --border-color: #e9ecef;
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #f0f4ff 0%, #1f4083 100%);
            padding: 20px;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            width: 100%;
            max-width: 500px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            position: relative;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .back-btn {
            position: absolute;
            top: 30px;
            left: 30px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            z-index: 10;
        }

        .back-btn:hover {
            transform: scale(1.1);
            background: var(--primary-color);
            color: white;
        }

        .illustration {
            text-align: center;
            margin-bottom: 5px;
        }

        .illustration-circle {
            width: 100px;
            height: 100px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: transform 0.3s ease;
        }

        .illustration-circle:hover {
            transform: scale(1.05);
        }

        .illustration-circle img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        .illustration-circle i {
            font-size: 40px;
            color: var(--primary-color);
            transform: rotate(-15deg);
        }

        .form-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 2.2rem;
            font-weight: 400;
            color: #1a1a1a;
            margin-bottom: 8px;
            position: relative;
            display: inline-block;
        }

        .title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 1rem;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            font-size: 0.95rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .input-field {
            width: 100%;
            height: 56px;
            padding: 0 50px;
            background: #fff;
            border: 1.5px solid #e1e8ef;
            border-radius: 14px;
            font-size: 1rem;
            color: #1a1a1a;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(26, 102, 255, 0.05);
        }

        .password-toggle {
            position: absolute;
            right: 18px;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .forgot-password {
            text-align: right;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .submit-btn {
            width: 100%;
            height: 56px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 16px rgba(26, 102, 255, 0.15);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(26, 102, 255, 0.2);
            filter: brightness(1.05);
        }

        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #adb5bd;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1.5px solid #f1f3f5;
        }

        .separator::before {
            margin-right: 15px;
        }

        .separator::after {
            margin-left: 15px;
        }

        .social-btns {
            display: flex;
            flex-direction: row;
            gap: 12px;
        }

        .social-btn {
            flex: 1;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .google-btn {
            background: white;
            border: 1.5px solid #e1e8ef;
            color: #1a1a1a;
        }

        .google-btn:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .apple-btn {
            background: #000;
            border: none;
            color: white;
        }

        .apple-btn:hover {
            background: #1a1a1a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.95rem;
            color: var(--text-muted);
        }

        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 700;
        }

        /* Notifications style */
        .swal2-popup {
            border-radius: 20px !important;
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 30px 20px;
            }

            .title {
                font-size: 1.8rem;
            }

            .social-btn {
                font-size: 0.85rem;
                padding: 0 5px;
            }
        }
    </style>
</head>

<body>
    <div class="form-container">
        <!-- Bouton Retour -->
        <a href="/" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="illustration">
            <div class="illustration-circle">
                <img src="{{ asset('assets/assets/img/logo plateau.png') }}" alt="Logo Plateau">
            </div>
        </div>

        <div class="form-header">
            <h1 class="title">Connexion</h1>
            <p class="subtitle">Heureux de vous revoir parmi nous</p>
        </div>

        @if (Session::get('success'))
            <div class="success-message animate__animated animate__fadeIn"
                style="color: var(--success-color); text-align: center; margin-bottom: 15px;">
                <i class="fas fa-check-circle"></i> {{ Session::get('success') }}
            </div>
        @endif

        @if (Session::get('error'))
            <div class="error-message animate__animated animate__shakeX"
                style="color: var(--error-color); text-align: center; margin-bottom: 15px;">
                <i class="fas fa-exclamation-circle"></i> {{ Session::get('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error-message animate__animated animate__shakeX"
                style="color: var(--error-color); text-align: center; margin-bottom: 15px;">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
            </div>
        @endif

        <!-- Section Téléphone -->
        <div id="phone-section">
            <form method="POST" action="{{ route('user.handleLogin') }}">
                @csrf
                <input type="hidden" name="indicatif" value="+225">

                <div class="form-group">
                    <label class="form-label">Numéro de téléphone</label>
                    <div class="input-wrapper">
                        <i class="fas fa-mobile-alt input-icon"></i>
                        <input name="contact" class="input-field" type="tel" placeholder="Ex: 0700000000" required
                            value="{{ old('contact') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input class="input-field" type="password" name="password" id="password"
                            placeholder="Entrez votre mot de passe" required>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                </div>

                <div class="forgot-password">
                    <a href="{{ route('user.password.request') }}">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>
        </div>

        <div class="separator">OU</div>

        <div class="social-btns">
            <button type="button" class="social-btn google-btn" id="googleLoginBtn">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google"
                    width="22">
                Google
            </button>

            <button type="button" class="social-btn apple-btn" id="appleLoginBtn">
                <svg width="20" height="20" viewBox="0 0 814 1000" fill="white">
                    <path
                        d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-57.8-155.5-127.4C46.5 700 0 571.8 0 449.3c0-152.5 99.5-233.1 197.3-233.1 69.1 0 126.4 45.3 170 45.3 42.1 0 108.5-47.9 188.2-47.9 30.1 0 108.2 2.6 168.6 80.6zm-80.6-171.4c31.5-38.5 53.9-89.2 53.9-139.9 0-7.1-.6-14.3-1.9-20.1-50.6 1.9-110.8 33.7-147.1 75.8-28.5 32.4-55.1 83.1-55.1 134.5 0 7.8 1.3 15.6 1.9 18.1 3.2.6 8.4 1.3 13.6 1.3 45.4 0 102.5-30.4 134.7-69.7z" />
                </svg>
                Apple
            </button>
        </div>

        <div class="form-footer">
            <p>Vous n'avez pas de compte ? <a href="{{ route('user.register') }}">S'inscrire</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>

    <!-- Firebase SDKs -->
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>

    <script>
        // Configuration Firebase
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}"
        };

        // Initialiser Firebase
        let auth = null;
        if (firebaseConfig.apiKey) {
            firebase.initializeApp(firebaseConfig);
            auth = firebase.auth();
        }

        function handleApple(btnId, endpoint) {
            const btn = document.getElementById(btnId);
            if (!btn) return;
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!auth) {
                    Swal.fire('Erreur système', 'Configuration Firebase manquante.', 'error');
                    return;
                }
                const provider = new firebase.auth.OAuthProvider('apple.com');
                provider.addScope('email');
                provider.addScope('name');

                auth.signInWithPopup(provider).then((result) => {
                        Swal.fire({
                            title: 'Vérification...',
                            text: 'Veuillez patienter',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => Swal.showLoading()
                        });
                        return result.user.getIdToken();
                    }).then((idToken) => {
                        return fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'ngrok-skip-browser-warning': 'true'
                            },
                            body: JSON.stringify({
                                id_token: idToken
                            })
                        });
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect;
                        } else {
                            Swal.fire('Erreur', data.message || 'Authentification Apple échouée', 'error');
                        }
                    }).catch((error) => {
                        if (error.code === 'auth/popup-closed-by-user') return;
                        Swal.fire('Erreur', 'Impossible de se connecter avec Apple.', 'error');
                    });
            });
        }
        handleApple('appleLoginBtn', '/user/auth/apple');

        document.getElementById('googleLoginBtn').addEventListener('click', function(e) {
            e.preventDefault();
            if (!auth) {
                Swal.fire('Erreur système', 'La configuration Google est manquante.', 'error');
                return;
            }
            const provider = new firebase.auth.GoogleAuthProvider();

            auth.signInWithPopup(provider).then((result) => {
                    Swal.fire({
                        title: 'Vérification...',
                        text: 'Veuillez patienter',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    return result.user.getIdToken();
                }).then((idToken) => {
                    return fetch("/user/auth/google", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'ngrok-skip-browser-warning': 'true'
                        },
                        body: JSON.stringify({
                            id_token: idToken
                        })
                    });
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        Swal.fire('Erreur', data.message || 'Authentification Google échouée', 'error');
                    }
                }).catch((error) => {
                    if (error.code === 'auth/popup-closed-by-user') return;
                    Swal.fire('Erreur', 'Impossible de se connecter avec Google.', 'error');
                });
        });
    </script>
</body>

</html>
