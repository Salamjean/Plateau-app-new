<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <link rel="shortcut icon" href="{{asset('assets/assets/img/logo plateau.png')}}" />
    <title>User login</title>
    <style>
        :root {
            --primary-color: #1977cc;
            --secondary-color: #1977cc;
            --accent-color: #4895ef;
            --error-color: #f72585;
            --success-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: 
                linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.7)),
                url('{{ asset('assets/assets/img/bavk.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            padding: 20px;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            gap: 20px;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform-style: preserve-3d;
            transition: all var(--transition-speed) ease;
        }

        /* Le reste du CSS reste inchangé */
        .form-container:hover {
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.3);
        }

        .form-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
            background: linear-gradient(to right, #1977cc, #1977cc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            display: inline-block;
        }

        .title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(to right, #1977cc, #1977cc);
            border-radius: 3px;
        }

        .subtitle {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .input-group {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            transition: all var(--transition-speed) ease;
            z-index: 2;
        }

        .input-field {
            width: 100%;
            outline: none;
            border-radius: 10px;
            height: 50px;
            border: 2px solid #e9ecef;
            background: transparent;
            padding-left: 45px;
            padding-right: 15px;
            font-size: 1rem;
            transition: all var(--transition-speed) ease;
            color: var(--dark-color);
        }

        .input-field:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .input-field:focus ~ .input-icon {
            color: var(--primary-color);
        }

        .input-label {
            position: absolute;
            top: 15px;
            left: 45px;
            color: #adb5bd;
            transition: all var(--transition-speed) ease;
            pointer-events: none;
            background-color: transparent;
            padding: 0 5px;
            z-index: 1;
        }

        .input-field:focus ~ .input-label,
        .input-field:not(:placeholder-shown) ~ .input-label {
            top: -10px;
            left: 35px;
            font-size: 0.8rem;
            color: var(--primary-color);
            background-color: white;
            z-index: 3;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .submit-btn {
            margin-top: 20px;
            height: 55px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            outline: none;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s ease;
        }

        .submit-btn:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .error-message {
            color: var(--error-color);
            font-size: 0.85rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
            animation: fadeIn var(--transition-speed) ease;
        }

        .success-message {
            color: var(--success-color);
            text-align: center;
            margin-bottom: 15px;
            font-weight: 500;
            animation: fadeIn var(--transition-speed) ease;
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all var(--transition-speed) ease;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .forgot-password {
            text-align: right;
            margin-top: -5px;
            margin-bottom: 20px;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all var(--transition-speed) ease;
        }

        .forgot-password a:hover {
            text-decoration: underline;
            color: var(--secondary-color);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 576px) {
            .form-container {
                padding: 30px 20px;
            }
            
            .title {
                font-size: 1.5rem;
            }
        }

        /* Floating animation */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        /* Nouveaux styles pour le bouton retour */
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .back-btn:hover {
            background: var(--secondary-color);
            transform: translateX(-3px);
        }

        .no-account {
            text-align: center;
            margin-top: 15px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .no-account a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all var(--transition-speed) ease;
        }

        .no-account a:hover {
            text-decoration: underline;
        }

        /* Styles pour le bouton Google */
        .google-btn {
            width: 100%;
            height: 55px;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }

        .google-btn:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
            transform: translateY(-2px);
        }

        .google-btn img {
            width: 24px;
            height: 24px;
        }

        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #adb5bd;
        }

        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e9ecef;
        }

        .separator:not(:empty)::before {
            margin-right: .5em;
        }

        .separator:not(:empty)::after {
            margin-left: .5em;
        }

        /* Toggle Styles */
        .auth-toggle {
            display: flex;
            background: #f8f9fa;
            padding: 4px;
            border-radius: 14px;
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
        }

        .toggle-btn {
            flex: 1;
            padding: 10px;
            border: none;
            background: transparent;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .toggle-btn.active {
            background: white;
            color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .auth-section {
            display: none;
            animation: slideIn 0.4s ease-out;
        }

        .auth-section.active {
            display: block;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Amélioration des boutons */
        .submit-btn {
            width: 100%;
            height: 55px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 8px 20px rgba(25, 119, 204, 0.2);
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(25, 119, 204, 0.3);
            filter: brightness(1.1);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .input-group {
            margin-bottom: 20px !important;
        }

        .password-toggle {
            top: 50% !important;
            transform: translateY(-50%) !important;
            right: 15px !important;
            cursor: pointer;
            color: #adb5bd;
            transition: 0.3s;
        }
    </style>
</head>
<body>
    <div class="form-container animate__animated animate__fadeIn">
        <!-- Bouton Retour -->
        <a href="/" class="back-btn animate__animated animate__fadeInLeft">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="form-header" style="margin-bottom: 30px;">
            <h1 class="title" style="font-size: 2.2rem; color: #1a1a1a;">Bon retour !</h1>
            <p class="subtitle" style="font-size: 1rem; color: #6c757d;">Heureux de vous revoir parmi nous</p>
        </div>

        @if (Session::get('success'))
            <div class="success-message animate__animated animate__bounceIn">
                <i class="fas fa-check-circle"></i> {{ Session::get('success') }}
            </div>
        @endif

        @if (Session::get('error'))
            <div class="error-message animate__animated animate__shakeX">
                <i class="fas fa-exclamation-circle"></i> {{ Session::get('error') }}
            </div>
        @endif

        <!-- Toggle Selection -->
        <div class="auth-toggle">
            <button type="button" class="toggle-btn active" data-target="email-section">
                <i class="fas fa-envelope"></i> Email
            </button>
            <button type="button" class="toggle-btn" data-target="phone-section">
                <i class="fas fa-phone"></i> Numéro
            </button>
        </div>

        <!-- Section Email -->
        <div id="email-section" class="auth-section active">
            <form method="POST" action="{{route('user.handleLogin')}}">
                @csrf
                <!-- Email Field -->
                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input class="input-field" type="email" name="email" placeholder=" " value="{{ old('email') }}" required />
                    <label class="input-label" for="email">Adresse Email</label>
                    @error('email')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="input-group">
                    <i class="fas fa-key input-icon"></i>
                    <input class="input-field" type="password" name="password" id="password" placeholder=" " required />
                    <label class="input-label" for="password">Mot de passe</label>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    @error('password')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="forgot-password">
                    <a href="{{ route('user.password.request') }}">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>
        </div>

        <!-- Section Téléphone (Mot de passe) -->
        <div id="phone-section" class="auth-section">
            <form method="POST" action="{{route('user.handleLogin')}}">
                @csrf
                <div class="input-group">
                    <div style="display: flex; gap: 10px;">
                        <select name="indicatif" class="input-field" style="flex: 0 0 110px;">
                            <option value="+225">+225</option>
                            <option value="+33">+33</option>
                            <option value="+1">+1</option>
                        </select>
                        <input name="contact" class="input-field" type="tel" placeholder="Ex: 0708325027" required>
                    </div>
                    <label class="input-label" style="top: -10px; left: 35px; font-size: 0.8rem; color: var(--primary-color); background-color: white; z-index: 3;">Numéro de téléphone</label>
                </div>

                <!-- Password Field -->
                <div class="input-group">
                    <i class="fas fa-key input-icon"></i>
                    <input class="input-field" type="password" name="password" placeholder=" " required />
                    <label class="input-label">Mot de passe</label>
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

        <button type="button" class="google-btn" id="googleLoginBtn">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google">
            Continuer avec Google
        </button>

        <div class="no-account">
            <p>Vous n'avez pas de compte ? <a href="{{route('user.register')}}">Créer un compte</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });

            // SweetAlert notifications
            @if (Session::has('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Succès',
                    text: '{{ Session::get('success') }}',
                    confirmButtonText: 'OK',
                    background: 'var(--light-color)',
                });
            @endif

            @if (Session::has('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: '{{ Session::get('error') }}',
                    confirmButtonText: 'OK',
                    background: 'var(--light-color)',
                    
                });
            @endif

            @if (Session::has('no_account'))
                Swal.fire({
                    icon: 'info',
                    title: 'Information',
                    text: '{{ Session::get('no_account') }}',
                    confirmButtonText: 'Créer un compte',
                    background: 'var(--light-color)',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('register') }}';
                    }
                });
            @endif

            // Add floating animation to form on hover
            const formContainer = document.querySelector('.form-container');
            formContainer.addEventListener('mouseenter', () => {
                formContainer.classList.add('animate__animated', 'animate__pulse');
            });
            
            formContainer.addEventListener('animationend', () => {
                formContainer.classList.remove('animate__animated', 'animate__pulse');
            });

            // Toggle Script
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.auth-section').forEach(s => s.classList.remove('active'));
                    
                    this.classList.add('active');
                    document.getElementById(this.dataset.target).classList.add('active');
                });
            });
        });
    </script>
    
    <!-- Firebase SDKs -->
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>

    <script>
        // Configuration Firebase (Doit être synchronisée avec vos clés)
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

        document.getElementById('googleLoginBtn').addEventListener('click', function(e) {
            e.preventDefault();
            if (!auth) {
                Swal.fire('Erreur système', 'La configuration Google. Veuillez contacter l\'administrateur.', 'error');
                return;
            }
            const provider = new firebase.auth.GoogleAuthProvider();
            
            Swal.fire({
                title: 'Connexion Google...',
                text: 'Veuillez patienter',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            auth.signInWithPopup(provider).then((result) => {
                return result.user.getIdToken();
            }).then((idToken) => {
                // Envoyer le jeton au Laravel (URL relative pour compatibilité local/prod)
                return fetch("/user/auth/google", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ id_token: idToken })
                });
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    Swal.fire('Erreur', data.message || 'Authentification échouée', 'error');
                }
            }).catch((error) => {
                console.error('Google Auth Error:', error);
                let errorMsg = 'Impossible de se connecter avec Google.';
                if (error.code === 'auth/unauthorized-domain') {
                    errorMsg = 'Ce domaine n\'est pas autorisé dans Firebase. Ajoutez-le dans la console Firebase > Authentication > Settings > Authorized domains.';
                } else if (error.code === 'auth/popup-closed-by-user') {
                    errorMsg = 'La fenêtre de connexion a été fermée.';
                } else if (error.code === 'auth/popup-blocked') {
                    errorMsg = 'La fenêtre popup a été bloquée par le navigateur. Autorisez les popups.';
                } else if (error.message) {
                    errorMsg = error.message;
                }
                Swal.fire('Erreur', errorMsg, 'error');
            });
        });
    </script>
</body>
</html>