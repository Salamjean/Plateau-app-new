<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{asset('assets/assets/img/logo plateau.png')}}" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <title>User register</title>
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
            position: relative;
        }

        /* Bouton de retour à l'accueil */
        .home-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .home-btn:hover {
            background: var(--secondary-color);
            transform: scale(1.1);
        }

        .register-container {
            width: 100%;
            max-width: 800px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .form-header {
            text-align: center;
            padding: 40px 40px 20px 40px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .form-header img {
            height: 80px;
            width: auto;
            margin-bottom: 15px;
        }

        .title {
            font-size: 2.2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 10px;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }

        .form-content {
            padding: 40px;
        }

        .input-group {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 25px; /* Fix: Match half of input height (50px) */
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
            padding-right: 45px; /* Increased right padding for eye icon */
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
            top: 25px; /* Fix: Match half of input height (50px) */
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
            width: 100%;
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

        .input-field.is-invalid {
            border-color: var(--error-color);
        }
        
        .input-field.is-invalid ~ .input-icon {
            color: var(--error-color);
        }

        .input-field.is-invalid ~ .input-label {
            color: var(--error-color);
        }

        .success-message {
            color: var(--success-color);
            text-align: center;
            margin-bottom: 15px;
            font-weight: 500;
            animation: fadeIn var(--transition-speed) ease;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .input-group {
            flex: 1;
        }

        /* Styles pour les nouveaux éléments */
        .phone-group {
            display: flex;
            gap: 10px;
        }

        .phone-group select {
            flex: 0 0 120px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0 10px;
            background: transparent;
            height: 50px;
        }

        .phone-group input {
            flex: 1;
        }

        select.input-field {
            padding-left: 45px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: rgba(25, 119, 204, 0.05);
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            accent-color: var(--primary-color);
        }

        .checkbox-group label {
            color: var(--dark-color);
            font-weight: 500;
            cursor: pointer;
        }

        /* Custom File Input Styling */
        .file-input-wrapper {
            position: relative;
            width: 100%;
            height: 50px;
            cursor: pointer;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 5;
        }

        .custom-file-btn {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            padding-left: 45px;
            padding-right: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            background: white;
            color: #adb5bd;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .file-input-wrapper:hover .custom-file-btn {
            border-color: var(--primary-color);
        }

        .input-field:focus ~ .custom-file-btn {
            border-color: var(--primary-color);
        }

        /* Force label to top for file inputs to avoid overlap */
        .file-input-wrapper + .input-label {
            top: -10px !important;
            left: 35px !important;
            font-size: 0.8rem !important;
            color: var(--primary-color) !important;
            background-color: white !important;
            z-index: 3 !important;
        }

        .diaspora-fields {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background-color: rgba(5, 150, 82, 0.05);
            border-radius: 10px;
            border-left: 4px solid #059652;
        }

        .diaspora-fields.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        .select2-container .select2-selection--single {
            height: 50px !important;
            border: 2px solid #e9ecef !important;
            border-radius: 10px !important;
            display: flex !important;
            align-items: center !important;
            padding-left: 10px !important;
            font-size: 1rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: 10px;
        }

        .select2-container--default .select2-selection--single:focus,
        .select2-container--default .select2-selection--single:hover {
            border-color: var(--primary-color) !important;
        }


        textarea.input-field {
            height: auto;
            min-height: 80px;
            padding-top: 15px;
            resize: vertical;
        }

        .section-title {
            color: var(--primary-color);
            margin: 25px 0 15px 0;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 8px;
            font-size: 1.2rem;
        }

        .section-title i {
            margin-right: 10px;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: var(--dark-color);
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all var(--transition-speed) ease;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .form-content {
                padding: 30px;
            }

            .form-header {
                padding: 30px 30px 15px 30px;
            }

            .title {
                font-size: 1.8rem;
            }

            .phone-group {
                flex-direction: column;
            }

            .phone-group select {
                flex: 1;
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .title {
                font-size: 1.5rem;
            }

            .home-btn {
                top: 10px;
                left: 10px;
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .form-content {
                padding: 20px;
            }
        }

        /* Password Strength & Hints */
        .password-requirements {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            margin-top: 10px;
            border: 1px solid #e9ecef;
            display: none;
            transition: all 0.3s ease;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }

        .requirement i {
            font-size: 0.75rem;
        }

        .requirement.valid {
            color: #2ec4b6;
        }

        .requirement.valid i::before {
            content: "\f00c"; /* check icon */
        }

        .password-strength-meter {
            height: 4px;
            width: 100%;
            background: #e9ecef;
            margin-top: 8px;
            border-radius: 2px;
            overflow: hidden;
            display: none;
        }

        .strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }

        .strength-weak { background: #ff4d4d; width: 33% !important; }
        .strength-medium { background: #ffa500; width: 66% !important; }
        .strength-strong { background: #2ec4b6; width: 100% !important; }

        .requirement.invalid {
            color: #ff4d4d;
        }

        .match-indicator {
            font-size: 0.8rem;
            margin-top: 4px;
            display: none;
        }
        
        .match-indicator.valid { color: #2ec4b6; display: block; }
        .match-indicator.invalid { color: #ff4d4d; display: block; }

        /* Nouveaux styles pour le Toggle et Google */
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
            margin-top: 10px;
        }

        .google-btn:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

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

        .otp-box {
            display: none;
            margin-top: 15px;
        }

        .btn-send-otp {
            width: 100%;
            padding: 15px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            margin-top: 10px;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(25, 119, 204, 0.2);
        }

        .btn-send-otp:hover {
            background: var(--accent-color);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <!-- Bouton de retour à l'accueil -->
    <button class="home-btn" onclick="window.location.href='{{route('home')}}'">
        <i class="fas fa-home"></i>
    </button>

    <div class="register-container animate__animated animate__fadeIn">
        <!-- En-tête du formulaire -->
        <div class="form-header">
            <img src="{{asset('assets/assets/img/logo plateau.png')}}" alt="Logo">
            <h1 class="title">Inscription</h1>
            <p class="subtitle">Créez votre compte pour accéder à la plateforme</p>
        </div>

        <!-- Contenu du formulaire -->
        <div class="form-content">
            <!-- Toggle Selection -->
            <div class="auth-toggle">
                <button type="button" class="toggle-btn active" data-target="email-section">
                    <i class="fas fa-envelope"></i> Par Email
                </button>
                <button type="button" class="toggle-btn" data-target="phone-section">
                    <i class="fas fa-phone"></i> Par Téléphone
                </button>
            </div>

            <div id="email-section" class="auth-section active">
                <form method="POST" action="{{route('user.handleRegister')}}" enctype="multipart/form-data">
                    @csrf
                
                <div class="form-row">
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input class="input-field @error('name') is-invalid @enderror" type="text" name="name" placeholder=" " value="{{ old('name') }}" />
                        <label class="input-label" for="name">Nom</label>
                        @error('name')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input class="input-field @error('prenom') is-invalid @enderror" type="text" name="prenom" placeholder=" " value="{{ old('prenom') }}" />
                        <label class="input-label" for="prenom">Prénom</label>
                        @error('prenom')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Informations de connexion -->
                <h3 class="section-title">
                    <i class="fas fa-key"></i> Informations de connexion
                </h3>

                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input class="input-field @error('email') is-invalid @enderror" type="email" name="email" placeholder=" " value="{{ old('email') }}" />
                    <label class="input-label" for="email">Adresse Email</label>
                    @error('email')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <i class="fas fa-key input-icon"></i>
                        <input class="input-field @error('password') is-invalid @enderror" type="password" name="password" id="password" placeholder=" " />
                        <label class="input-label" for="password">Mot de passe</label>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        
                        <div class="password-strength-meter" id="strengthMeter">
                            <div class="strength-bar" id="strengthBar"></div>
                        </div>

                        <div class="password-requirements" id="passwordRequirements">
                            <div class="requirement" id="reqLength">
                                <i class="fas fa-circle"></i> Au moins 8 caractères
                            </div>
                            <div class="requirement" id="reqUpper">
                                <i class="fas fa-circle"></i> Une lettre majuscule
                            </div>
                            <div class="requirement" id="reqLower">
                                <i class="fas fa-circle"></i> Une lettre minuscule
                            </div>
                            <div class="requirement" id="reqNumber">
                                <i class="fas fa-circle"></i> Un chiffre
                            </div>
                            <div class="requirement" id="reqSpecial">
                                <i class="fas fa-circle"></i> Un caractère spécial (@$!%*#?&.)
                            </div>
                        </div>

                        @error('password')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="input-group">
                        <i class="fas fa-key input-icon"></i>
                        <input class="input-field" type="password" name="password_confirmation" id="password_confirmation" placeholder=" " />
                        <label class="input-label" for="password_confirmation">Confirmation</label>
                        <i class="fas fa-eye password-toggle" id="togglePasswordConfirmation"></i>
                        <div id="matchIndicator" class="match-indicator"></div>
                    </div>
                </div>

                <!-- Informations supplémentaires -->
                <h3 class="section-title">
                    <i class="fas fa-info-circle"></i> Informations supplémentaires
                </h3>

                

                <div class="form-row">
                    <div class="input-group">
                        <div class="phone-group">
                            <select name="indicatif" class="@error('indicatif') is-invalid @enderror">
                                <option value="+225" {{ old('indicatif', '+225') == '+225' ? 'selected' : '' }}>Côte d'Ivoire (+225)</option>
                                <option value="+33" {{ old('indicatif') == '+33' ? 'selected' : '' }}>France (+33)</option>
                                <option value="+1" {{ old('indicatif') == '+1' ? 'selected' : '' }}>États-Unis/Canada (+1)</option>
                                <option value="+32" {{ old('indicatif') == '+32' ? 'selected' : '' }}>Belgique (+32)</option>
                                <option value="+41" {{ old('indicatif') == '+41' ? 'selected' : '' }}>Suisse (+41)</option>
                                <option value="+44" {{ old('indicatif') == '+44' ? 'selected' : '' }}>Royaume-Uni (+44)</option>
                                <option value="+49" {{ old('indicatif') == '+49' ? 'selected' : '' }}>Allemagne (+49)</option>
                                <option value="+39" {{ old('indicatif') == '+39' ? 'selected' : '' }}>Italie (+39)</option>
                                <option value="+34" {{ old('indicatif') == '+34' ? 'selected' : '' }}>Espagne (+34)</option>
                                <option value="+31" {{ old('indicatif') == '+31' ? 'selected' : '' }}>Pays-Bas (+31)</option>
                                <option value="+351" {{ old('indicatif') == '+351' ? 'selected' : '' }}>Portugal (+351)</option>
                            </select>
                            <input class="input-field @error('contact') is-invalid @enderror" type="tel" name="contact" placeholder="Numéro de contact" value="{{ old('contact') }}" />
                        </div>
                        <label class="input-label" for="contact" style="top: -10px; left: 35px; font-size: 0.8rem; color: var(--primary-color); background-color: white; z-index: 3;">Contact</label>
                        @error('contact')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                        @error('indicatif')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <!-- --- MODIFICATION --- -->
                    <!-- Le champ "Commune" a été supprimé de la vue -->
                    <!-- 
                    <div class="input-group">
                        <i class="fas fa-map-marker-alt input-icon"></i>
                        <select class... name="commune"> ... </select>
                        <label ...>Commune de naissance</label>
                        @error('commune') ... @enderror
                    </div>
                    -->
                    <!-- --- FIN MODIFICATION --- -->
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <i class="fas fa-id-card input-icon"></i>
                        <input class="input-field @error('CMU') is-invalid @enderror" type="text" name="CMU" placeholder=" " value="{{ old('CMU') }}" />
                        <label class="input-label" for="CMU">N° NNI (Optionnel)</label>
                        @error('CMU')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <div class="file-input-wrapper">
                            <input class="input-field @error('profile_picture') is-invalid @enderror" type="file" name="profile_picture" id="profile_picture" accept="image/jpeg, image/png, image/jpg, image/gif" />
                            <div class="custom-file-btn" id="fileBtnText">Choisir une photo de profil...</div>
                        </div>
                        <label class="input-label" for="profile_picture">Photo de profil (Optionnel)</label>
                        @error('profile_picture')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Option Diaspora -->
                <div class="checkbox-group">
                    <input type="checkbox" id="diaspora" name="diaspora" value="1" {{ old('diaspora') ? 'checked' : '' }}>
                    <label for="diaspora">Je suis de la diaspora</label>
                </div>

                <!-- Champs Diaspora (cachés par défaut) -->
                <div class="diaspora-fields" id="diasporaFields">
                    <h3 style="color: #059652; margin-bottom: 15px; border-bottom: 2px solid #059652; padding-bottom: 5px;">
                        <i class="fas fa-globe"></i> Informations de la diaspora
                    </h3>
                    
                    <div class="form-row">
                        <div class="input-group">
                            <i class="fas fa-globe input-icon"></i>
                            <select class="input-field searchable-select @error('pays_residence') is-invalid @enderror" name="pays_residence">
                                <option value="">Sélectionnez votre pays de résidence</option>
                                <option value="france" {{ old('pays_residence') == 'france' ? 'selected' : '' }}>France</option>
                                <option value="usa" {{ old('pays_residence') == 'usa' ? 'selected' : '' }}>États-Unis</option>
                                <option value="canada" {{ old('pays_residence') == 'canada' ? 'selected' : '' }}>Canada</option>
                                <option value="belgique" {{ old('pays_residence') == 'belgique' ? 'selected' : '' }}>Belgique</option>
                                <option value="suisse" {{ old('pays_residence') == 'suisse' ? 'selected' : '' }}>Suisse</option>
                                <option value="allemagne" {{ old('pays_residence') == 'allemagne' ? 'selected' : '' }}>Allemagne</option>
                                <option value="angleterre" {{ old('pays_residence') == 'angleterre' ? 'selected' : '' }}>Angleterre</option>
                                <option value="autre" {{ old('pays_residence') == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            <label class="input-label" for="pays_residence">Pays de résidence</label>
                            @error('pays_residence')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="input-group">
                            <i class="fas fa-city input-icon"></i>
                            <input class="input-field @error('ville_residence') is-invalid @enderror" type="text" name="ville_residence" placeholder=" " value="{{ old('ville_residence') }}" />
                            <label class="input-label" for="ville_residence">Ville de résidence</label>
                            @error('ville_residence')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group">
                        <i class="fas fa-map-marker-alt input-icon"></i>
                        <textarea class="input-field @error('adresse_etrangere') is-invalid @enderror" name="adresse_etrangere" placeholder=" " rows="3">{{ old('adresse_etrangere') }}</textarea>
                        <label class="input-label" for="adresse_etrangere">Adresse à l'étranger</label>
                        @error('adresse_etrangere')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                    <button type="submit" class="submit-btn animate__animated animate__pulse animate__infinite animate__slower">
                        <i class="fas fa-user-plus"></i> S'inscrire
                    </button>

                    <div class="separator" style="display: flex; align-items: center; text-align: center; margin: 25px 0; color: #adb5bd;">
                        <span style="flex: 1; border-bottom: 1px solid #e9ecef;"></span>
                        <span style="padding: 0 10px; font-size: 0.85rem;">OU</span>
                        <span style="flex: 1; border-bottom: 1px solid #e9ecef;"></span>
                    </div>

                    <button type="button" class="google-btn" id="googleRegisterBtn">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="20">
                        S'inscrire avec Google
                    </button>

                    <div class="login-link">
                        Vous avez déjà un compte ? <a href="{{route('login')}}">Se connecter</a>
                    </div>
                </form>
            </div>

            <!-- Phone Section -->
            <div id="phone-section" class="auth-section">
                <div class="input-group">
                    <div class="phone-group">
                        <select id="otp_indicatif">
                            <option value="+225">Côte d'Ivoire (+225)</option>
                            <option value="+33">France (+33)</option>
                            <option value="+1">USA (+1)</option>
                        </select>
                        <input id="otp_contact" class="input-field" type="tel" placeholder="Ex: 0708325027">
                    </div>
                    <label class="input-label" style="top: -10px; left: 35px; font-size: 0.8rem; color: var(--primary-color); background-color: white; z-index: 3;">Votre numéro de téléphone</label>
                </div>

                <button type="button" class="btn-send-otp" id="btnSendOtp">
                    <i class="fas fa-paper-plane"></i> Recevoir le code par SMS
                </button>

                <div class="otp-box" id="otpBox">
                    <div class="input-group">
                        <i class="fas fa-shield-alt input-icon"></i>
                        <input class="input-field" type="text" id="otp_code" placeholder="Code à 6 chiffres" maxlength="6">
                        <label class="input-label">Code de vérification</label>
                    </div>
                    <button type="button" class="submit-btn" id="btnVerifyOtp">
                        Valider et continuer <i class="fas fa-arrow-right"></i>
                    </button>
                    <p style="text-align: center; margin-top: 15px; font-size: 0.85rem; color: #6c757d;">
                        Pas reçu ? <a href="javascript:void(0)" id="resendOtp" style="color: var(--primary-color); text-decoration: none;">Renvoyer</a>
                    </p>
                </div>

                <div class="separator" style="display: flex; align-items: center; text-align: center; margin: 25px 0; color: #adb5bd;">
                    <span style="flex: 1; border-bottom: 1px solid #e9ecef;"></span>
                    <span style="padding: 0 10px; font-size: 0.85rem;">OU</span>
                    <span style="flex: 1; border-bottom: 1px solid #e9ecef;"></span>
                </div>

                <button type="button" class="google-btn" id="googlePhoneBtn">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="20">
                    S'inscrire avec Google
                </button>

                <div class="login-link">
                    Vous avez déjà un compte ? <a href="{{route('login')}}">Se connecter</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            
            const togglePasswordConfirmation = document.querySelector('#togglePasswordConfirmation');
            const passwordConfirmation = document.querySelector('#password_confirmation');

            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.classList.toggle('fa-eye-slash');
                });
            }

            if (togglePasswordConfirmation) {
                togglePasswordConfirmation.addEventListener('click', function() {
                    const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordConfirmation.setAttribute('type', type);
                    this.classList.toggle('fa-eye-slash');
                });
            }

            $(document).ready(function() {
                $('.searchable-select').select2({
                    placeholder: "Sélectionnez une option",
                    allowClear: true,
                    width: '100%', // pour qu’il prenne toute la largeur
                    language: {
                        noResults: function() {
                            return "Aucun résultat trouvé";
                        }
                    }
                });
            });

            // Gestion de l'affichage des champs diaspora
            const diasporaCheckbox = document.getElementById('diaspora');
            const diasporaFields = document.getElementById('diasporaFields');
            
            if(diasporaCheckbox) {
                // Afficher/masquer les champs diaspora selon l'état initial
                if (diasporaCheckbox.checked) {
                    diasporaFields.classList.add('active');
                }
                
                // Écouter les changements de la checkbox
                diasporaCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        diasporaFields.classList.add('active');
                    } else {
                        diasporaFields.classList.remove('active');
                    }
                });
            }

            // Masquer les erreurs quand on modifie le champ
            document.querySelectorAll('input, select, textarea').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    const inputGroup = this.closest('.input-group') || this.closest('.phone-group');
                    if (inputGroup) {
                        const errorElement = inputGroup.querySelector('.error-message') || inputGroup.parentElement.querySelector('.error-message');
                        if(errorElement) {
                            errorElement.style.display = 'none';
                        }
                    }
                });
            });

            // Password validation & strength meter
            const strengthMeter = document.querySelector('#strengthMeter');
            const strengthBar = document.querySelector('#strengthBar');
            const passwordRequirements = document.querySelector('#passwordRequirements');
            
            const reqs = {
                length: document.querySelector('#reqLength'),
                upper: document.querySelector('#reqUpper'),
                lower: document.querySelector('#reqLower'),
                number: document.querySelector('#reqNumber'),
                special: document.querySelector('#reqSpecial')
            };

            password.addEventListener('focus', function() {
                passwordRequirements.style.display = 'block';
                strengthMeter.style.display = 'block';
            });

            password.addEventListener('input', function() {
                const val = this.value;
                
                // Validate requirements
                const checks = {
                    length: val.length >= 8,
                    upper: /[A-Z]/.test(val),
                    lower: /[a-z]/.test(val),
                    number: /[0-9]/.test(val),
                    special: /[@$!%*#?&.]/.test(val)
                };

                let score = 0;
                for (const key in checks) {
                    if (checks[key]) {
                        reqs[key].classList.add('valid');
                        const icon = reqs[key].querySelector('i');
                        icon.className = 'fas fa-check';
                        score++;
                    } else {
                        reqs[key].classList.remove('valid');
                        const icon = reqs[key].querySelector('i');
                        icon.className = 'fas fa-circle';
                    }
                }

                // Update strength bar
                strengthBar.className = 'strength-bar';
                if (val.length > 0) {
                    if (score <= 2) strengthBar.classList.add('strength-weak');
                    else if (score <= 4) strengthBar.classList.add('strength-medium');
                    else strengthBar.classList.add('strength-strong');
                } else {
                    strengthBar.style.width = '0';
                }

                validateMatch();
            });

            const matchIndicator = document.querySelector('#matchIndicator');
            function validateMatch() {
                if (passwordConfirmation.value.length > 0) {
                    if (password.value === passwordConfirmation.value) {
                        matchIndicator.textContent = 'Les mots de passe correspondent';
                        matchIndicator.className = 'match-indicator valid';
                    } else {
                        matchIndicator.textContent = 'Les mots de passe ne correspondent pas';
                        matchIndicator.className = 'match-indicator invalid';
                    }
                } else {
                    matchIndicator.style.display = 'none';
                }
            }

            passwordConfirmation.addEventListener('input', validateMatch);

            // Phone number & Country code logic
            const indicatifSelect = document.querySelector('select[name="indicatif"]');
            const contactInput = document.querySelector('input[name="contact"]');

            function applyPhoneLogic() {
                if (indicatifSelect.value === '+225') {
                    contactInput.setAttribute('maxlength', '10');
                    contactInput.setAttribute('placeholder', 'Ex: 0708325027');
                } else {
                    contactInput.removeAttribute('maxlength');
                    contactInput.setAttribute('placeholder', 'Numéro de contact');
                }
            }

            indicatifSelect.addEventListener('change', function() {
                applyPhoneLogic();
                // If switching to CI, truncate existing value if longer than 10
                if (this.value === '+225' && contactInput.value.length > 10) {
                    contactInput.value = contactInput.value.substring(0, 10);
                }
            });

            contactInput.addEventListener('input', function(e) {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // File input logic
            const profilePictureInput = document.querySelector('#profile_picture');
            const fileBtnText = document.querySelector('#fileBtnText');

            profilePictureInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    fileBtnText.textContent = this.files[0].name;
                    fileBtnText.style.color = 'var(--dark-color)';
                } else {
                    fileBtnText.textContent = 'Choisir une photo de profil...';
                    fileBtnText.style.color = '#adb5bd';
                }
            });

            // Initial call
            applyPhoneLogic();

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

            @if (Session::has('error') || $errors->any())
                @php
                    $errorText = Session::get('error') ?? ($errors->any() ? 'Veuillez vérifier les informations saisies.' : '');
                @endphp
                Swal.fire({
                    icon: 'error',
                    title: 'Oups...',
                    text: '{!! $errorText !!}',
            @endif
        });

        // Script de Toggle
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.auth-section').forEach(s => s.classList.remove('active'));
                
                this.classList.add('active');
                document.getElementById(this.dataset.target).classList.add('active');
            });
        });
    </script>

    <!-- Firebase SDKs -->
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const firebaseConfig = {
                    apiKey: "{{ config('services.firebase.api_key') }}",
                    authDomain: "{{ config('services.firebase.auth_domain') }}",
                    projectId: "{{ config('services.firebase.project_id') }}",
                    storageBucket: "{{ config('services.firebase.storage_bucket') }}",
                    messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
                    appId: "{{ config('services.firebase.app_id') }}"
                };

                let auth = null;
                if (firebaseConfig.apiKey) {
                    firebase.initializeApp(firebaseConfig);
                    auth = firebase.auth();
                }

                // Google Handler
                function handleGoogle(btnId) {
                    const btn = document.getElementById(btnId);
                    if (!btn) return;
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (!auth) {
                            Swal.fire('Erreur système', 'La configuration Google. Veuillez contacter l\'administrateur.', 'error');
                            return;
                        }
                        const provider = new firebase.auth.GoogleAuthProvider();
                        Swal.fire({ title: 'Connexion Google...', allowOutsideClick: false, willOpen: () => { Swal.showLoading(); }});

                        auth.signInWithPopup(provider).then((result) => {
                            return result.user.getIdToken();
                        }).then((idToken) => {
                            return fetch("/user/auth/google", {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'ngrok-skip-browser-warning': 'true' },
                                body: JSON.stringify({ id_token: idToken })
                            });
                        }).then(response => {
                            const contentType = response.headers.get('content-type');
                            if (!response.ok || !contentType || !contentType.includes('application/json')) {
                                throw { message: 'Le serveur a renvoyé une réponse inattendue (code ' + response.status + '). Rechargez la page et réessayez.' };
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                window.location.href = data.redirect;
                            } else {
                                Swal.fire('Erreur', data.message, 'error');
                            }
                        }).catch(error => {
                            console.error('Google Auth Error:', error);
                            let errorMsg = 'Authentification Google échouée.';
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
                }

                handleGoogle('googleRegisterBtn');
                handleGoogle('googlePhoneBtn');
            } catch (e) {
                console.error("Firebase Auth initialization error", e);
            }

            // OTP Handler
            const btnSendOtp = document.getElementById('btnSendOtp');
            const btnVerifyOtp = document.getElementById('btnVerifyOtp');
            const otpBox = document.getElementById('otpBox');

            if (btnSendOtp) {
                btnSendOtp.addEventListener('click', function() {
                    const indicatif = document.getElementById('otp_indicatif').value;
                    const contact = document.getElementById('otp_contact').value;

                    if(!contact) return Swal.fire('Oups', 'Veuillez saisir votre numéro', 'warning');

                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';

                    fetch("{{ route('user.auth.otp.send') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                        body: JSON.stringify({ indicatif, contact })
                    }).then(r => {
                        // Throw error if HTTP response is not OK (e.g., 500 Error server)
                        if (!r.ok) {
                            return r.json().then(errData => { throw errData; });
                        }
                        return r.json();
                    })
                    .then(data => {
                        if(data.success) {
                            Swal.fire('Succès', data.message, 'success');
                            otpBox.style.display = 'block';
                            btnSendOtp.style.display = 'none';
                        } else {
                            Swal.fire('Erreur', data.message || 'Erreur inconnue', 'error');
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-paper-plane"></i> Recevoir le code par SMS';
                        }
                    }).catch(error => {
                        console.error('Fetch OTP Error:', error);
                        Swal.fire('Erreur', error.message || 'Une erreur est survenue lors de l\'envoi du SMS.', 'error');
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-paper-plane"></i> Recevoir le code par SMS';
                    });
                });
            }

            if (btnVerifyOtp) {
                btnVerifyOtp.addEventListener('click', function() {
                    const indicatif = document.getElementById('otp_indicatif').value;
                    const contact = document.getElementById('otp_contact').value;
                    const otp = document.getElementById('otp_code').value;

                    if(!otp) return Swal.fire('Oups', 'Veuillez saisir le code reçu', 'warning');

                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Vérification...';

                    fetch("{{ route('user.auth.otp.verify') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                        body: JSON.stringify({ indicatif, contact, otp })
                    }).then(r => {
                        if (!r.ok) {
                            return r.json().then(errData => { throw errData; });
                        }
                        return r.json();
                    })
                    .then(data => {
                        if(data.success) {
                            window.location.href = data.redirect;
                        } else {
                            Swal.fire('Erreur', data.message, 'error');
                            this.disabled = false;
                            this.innerHTML = 'Valider et continuer <i class="fas fa-arrow-right"></i>';
                        }
                    }).catch(error => {
                        console.error('Verify OTP Error:', error);
                        Swal.fire('Erreur', error.message || 'Une erreur est survenue.', 'error');
                        this.disabled = false;
                        this.innerHTML = 'Valider et continuer <i class="fas fa-arrow-right"></i>';
                    });
                });
            }
        });

    </script>
</body>
</html>