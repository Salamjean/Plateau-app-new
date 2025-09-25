<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <link rel="shortcut icon" href="{{asset('assets/assets/img/logo plateau.png')}}" />
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
            <form method="POST" action="{{route('user.handleRegister')}}" enctype="multipart/form-data">
                @csrf

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

                <!-- Informations personnelles -->
                <h3 class="section-title">
                    <i class="fas fa-user-circle"></i> Informations personnelles
                </h3>
                
                <div class="form-row">
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input class="input-field" type="text" name="name" placeholder=" " value="{{ old('name') }}" />
                        <label class="input-label" for="name">Nom</label>
                        @error('name')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input class="input-field" type="text" name="prenom" placeholder=" " value="{{ old('prenom') }}" />
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
                    <input class="input-field" type="email" name="email" placeholder=" " value="{{ old('email') }}" />
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
                        <input class="input-field" type="password" name="password" id="password" placeholder=" " />
                        <label class="input-label" for="password">Mot de passe</label>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
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
                    </div>
                </div>

                <!-- Informations supplémentaires -->
                <h3 class="section-title">
                    <i class="fas fa-info-circle"></i> Informations supplémentaires
                </h3>

                <!-- Option Diaspora -->
                <div class="checkbox-group">
                    <input type="checkbox" id="diaspora" name="diaspora" value="1" {{ old('diaspora') ? 'checked' : '' }}>
                    <label for="diaspora">Je suis de la diaspora</label>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <div class="phone-group">
                            <select name="indicatif">
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
                            <input class="input-field" type="tel" name="contact" placeholder=" " value="{{ old('contact') }}" />
                        </div>
                        @error('contact')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="input-group">
                        <i class="fas fa-map-marker-alt input-icon"></i>
                        <select class="input-field" name="commune">
                            <option value="">Sélectionnez votre commune</option>
                            <option value="abobo" {{ old('commune') == 'abobo' ? 'selected' : '' }}>Abobo</option>
                            <option value="adjame" {{ old('commune') == 'adjame' ? 'selected' : '' }}>Adjamé</option>
                            <option value="anyama" {{ old('commune') == 'anyama' ? 'selected' : '' }}>Anyama</option>
                            <option value="cocody" {{ old('commune') == 'cocody' ? 'selected' : '' }}>Cocody</option>
                            <option value="plateau" {{ old('commune') == 'plateau' ? 'selected' : '' }}>Plateau</option>
                            <option value="yopougon" {{ old('commune') == 'yopougon' ? 'selected' : '' }}>Yopougon</option>
                            <option value="abengourou" {{ old('commune') == 'abengourou' ? 'selected' : '' }}>Abengourou</option>
                            <option value="bouake" {{ old('commune') == 'bouake' ? 'selected' : '' }}>Bouaké</option>
                            <option value="daloa" {{ old('commune') == 'daloa' ? 'selected' : '' }}>Daloa</option>
                            <option value="san-pedro" {{ old('commune') == 'san-pedro' ? 'selected' : '' }}>San-Pédro</option>
                            <option value="yamoussoukro" {{ old('commune') == 'yamoussoukro' ? 'selected' : '' }}>Yamoussoukro</option>
                        </select>
                        <label class="input-label" for="commune">Commune de naissance</label>
                        @error('commune')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <i class="fas fa-id-card input-icon"></i>
                        <input class="input-field" type="text" name="CMU" placeholder=" " value="{{ old('CMU') }}" />
                        <label class="input-label" for="CMU">N°CMU</label>
                        @error('CMU')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input class="input-field" type="file" name="profile_picture" placeholder=" " value="{{ old('profile_picture') }}" accept="image/jpeg, image/png, image/jpg, image/gif" />
                        <label class="input-label" for="profile_picture">Photo de profil</label>
                        @error('profile_picture')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Champs Diaspora (cachés par défaut) -->
                <div class="diaspora-fields" id="diasporaFields">
                    <h3 style="color: #059652; margin-bottom: 15px; border-bottom: 2px solid #059652; padding-bottom: 5px;">
                        <i class="fas fa-globe"></i> Informations de la diaspora
                    </h3>
                    
                    <div class="form-row">
                        <div class="input-group">
                            <i class="fas fa-globe input-icon"></i>
                            <select class="input-field" name="pays_residence">
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
                            <input class="input-field" type="text" name="ville_residence" placeholder=" " value="{{ old('ville_residence') }}" />
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
                        <textarea class="input-field" name="adresse_etrangere" placeholder=" " rows="3">{{ old('adresse_etrangere') }}</textarea>
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

                <div class="login-link">
                    Vous avez déjà un compte ? <a href="{{route('login')}}">Se connecter</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            
            const togglePasswordConfirmation = document.querySelector('#togglePasswordConfirmation');
            const passwordConfirmation = document.querySelector('#password_confirmation');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });

            togglePasswordConfirmation.addEventListener('click', function() {
                const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmation.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });

            // Gestion de l'affichage des champs diaspora
            const diasporaCheckbox = document.getElementById('diaspora');
            const diasporaFields = document.getElementById('diasporaFields');
            
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

            // Masquer les erreurs quand on modifie le champ
            document.querySelectorAll('input, select, textarea').forEach(input => {
                input.addEventListener('input', function() {
                    const errorElement = this.parentElement.querySelector('.error-message');
                    if(errorElement) {
                        errorElement.style.display = 'none';
                    }
                });
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
        });
    </script>
</body>
</html>