<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{asset('assets/assets/img/logo plateau.png')}}" />
    <title>Finaliser le profil - Utilisateur</title>
    <style>
        :root {
            --primary-color: #1f4083;
            --secondary-color: #1f4083;
            --accent-color: #3b5fa6;
            --error-color: #f72585;
            --success-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #1f4083;
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
            background: linear-gradient(135deg, #f0f4fa 0%, #dbe5f5 100%);
            padding: 20px;
        }

        .form-container {
            background-color: white;
            padding: 40px;
            width: 100%;
            max-width: 850px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .illustration {
            text-align: center;
            margin-bottom: 20px;
        }

        .illustration-circle {
            width: 80px;
            height: 80px;
            background: #eef2fa;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .illustration-circle i {
            font-size: 30px;
            color: var(--primary-color);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px 20px;
        }

        .full-width {
            grid-column: span 2;
        }

        .section-title {
            grid-column: span 2;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 25px 0 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1.5px;
            background: #f1f3f5;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
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
            left: 16px;
            color: var(--primary-color);
            font-size: 1rem;
        }

        .input-field {
            width: 100%;
            height: 52px;
            padding: 0 45px;
            background: #fff;
            border: 1.5px solid #e1e8ef;
            border-radius: 12px;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(31, 64, 131, 0.08);
        }

        .input-field:read-only {
            background: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }

        .checkbox-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 0;
            cursor: pointer;
            transition: 0.3s;
            height: 52px;
            align-self: end;
        }

        .checkbox-card:hover {
            background: #eef2fa;
        }

        .checkbox-card input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-color);
        }

        .diaspora-fields {
            display: none;
            grid-column: span 2;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            animation: fadeIn 0.4s ease;
        }

        .diaspora-fields.active {
            display: grid;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .submit-btn {
            grid-column: span 2;
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
            margin-top: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 16px rgba(31, 64, 131, 0.15);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(31, 64, 131, 0.25);
        }

        .error-msg {
            color: var(--error-color);
            font-size: 0.8rem;
            margin-top: 5px;
            font-weight: 600;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1rem;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color var(--transition-speed);
        }

        .toggle-password:hover {
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .form-container {
                max-width: 100%;
                padding: 30px 20px;
            }
            form {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            .full-width {
                grid-column: span 1;
            }
            .section-title {
                grid-column: span 1;
            }
            .diaspora-fields {
                grid-column: span 1;
                grid-template-columns: 1fr;
                gap: 15px;
            }
            .checkbox-card {
                align-self: stretch;
            }
            .submit-btn {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>
    @php
        $pendingData = $pendingData ?? [];
        $isSocial    = $isSocial    ?? false;
        $nameVal     = old('name',   $pendingData['name']   ?? $user?->name   ?? '');
        $prenomVal   = old('prenom', $pendingData['prenom'] ?? $user?->prenom ?? '');
        $emailVal    = $pendingData['email'] ?? $user?->email ?? '';
        $nniVal      = old('NNI',    $user?->NNI   ?? '');
        $isNewSocial = !empty($pendingData);
    @endphp

    <div class="form-container">
        <div class="illustration">
            <div class="illustration-circle">
                <i class="fas fa-user-check"></i>
            </div>
        </div>

        <div class="form-header">
            <h1 class="title">Finalisez votre profil</h1>
            <p class="subtitle">Encore quelques détails pour sécuriser votre compte</p>
        </div>

        <form action="{{ route('user.auth.profile.submit') }}" method="POST">
            @csrf

            <div class="section-title"><i class="fas fa-info-circle"></i> Personnel</div>

            <div class="form-group">
                <label class="form-label">Nom de famille</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="name" class="input-field" value="{{ $nameVal }}" required placeholder="Votre nom">
                </div>
                @error('name') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Prénom(s)</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="prenom" class="input-field" value="{{ $prenomVal }}" required placeholder="Vos prénoms">
                </div>
                @error('prenom') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Adresse Email {{ $isSocial ? '' : '(Optionnelle)' }}</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="input-field" value="{{ $emailVal }}" 
                           {{ $isSocial ? 'readonly' : '' }} placeholder="email@exemple.com">
                </div>
                @error('email') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            @if($isNewSocial)
            <div class="section-title"><i class="fas fa-phone"></i> Contact</div>
            <div class="form-group full-width">
                <label class="form-label">Numéro de téléphone</label>
                <div class="input-wrapper">
                    <input type="hidden" name="indicatif" value="+225">
                    <i class="fas fa-mobile-alt input-icon"></i>
                    <input type="text" name="contact" class="input-field" value="{{ old('contact') }}" required placeholder="Ex: 0700000000">
                </div>
                @error('contact') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
            @endif

            @if(!$isSocial)
            <div class="section-title"><i class="fas fa-lock"></i> Sécurité</div>
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <div class="input-wrapper">
                    <i class="fas fa-key input-icon"></i>
                    <input type="password" name="password" id="password" class="input-field" required placeholder="Définissez un mot de passe">
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirmation</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="input-field" required placeholder="Confirmez le mot de passe">
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password_confirmation', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            @endif

            <div class="section-title"><i class="fas fa-id-card"></i> Identité & Localisation</div>
            <div class="form-group">
                <label class="form-label">Numéro NNI (Facultatif)</label>
                <div class="input-wrapper">
                    <i class="fas fa-fingerprint input-icon"></i>
                    <input type="text" name="NNI" class="input-field" value="{{ $nniVal }}" placeholder="Votre numéro national d'identité">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Ville de résidence</label>
                <div class="input-wrapper">
                    <i class="fas fa-city input-icon"></i>
                    <input type="text" name="ville_residence" class="input-field" value="{{ old('ville_residence', $user?->ville_residence ?? '') }}" placeholder="Ex: Paris">
                </div>
            </div>

            @php
                $isSocial = $isSocial ?? false;
                $isDiasporaChecked = old('diaspora', $pendingPhone['diaspora'] ?? $user?->diaspora ?? false);
                $showDiaspora = $isSocial || (!empty($pendingPhone) && !empty($pendingPhone['diaspora']));
            @endphp

            @if($showDiaspora)
            <label class="checkbox-card full-width" for="diaspora">
                <input type="checkbox" name="diaspora" id="diaspora" value="1" {{ $isDiasporaChecked ? 'checked' : '' }}>
                <span style="font-weight: 700; color: #1a1a1a;">Je réside à l'étranger (Diaspora)</span>
            </label>

            <div class="diaspora-fields {{ $isDiasporaChecked ? 'active' : '' }}" id="diasporaBox">
                <div class="form-group full-width">
                    <label class="form-label">Pays de résidence</label>
                    <div class="input-wrapper">
                        <i class="fas fa-globe input-icon"></i>
                        <input type="text" name="pays_residence" class="input-field" value="{{ old('pays_residence', $pendingPhone['pays_residence'] ?? $user?->pays_residence ?? '') }}" placeholder="Ex: France">
                    </div>
                </div>
            </div>
            @endif

            <button type="submit" class="submit-btn">
                <i class="fas fa-check-circle"></i> Terminer l'inscription
            </button>
        </form>
    </div>

    <script>
        const diasporaCheckbox = document.getElementById('diaspora');
        if (diasporaCheckbox) {
            diasporaCheckbox.addEventListener('change', function() {
                const diasporaBox = document.getElementById('diasporaBox');
                if (diasporaBox) {
                    diasporaBox.classList.toggle('active', this.checked);
                }
            });
        }

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        @if(Session::has('error'))
            Swal.fire({ icon: 'error', title: 'Oups', text: '{{ Session::get('error') }}', confirmButtonColor: 'var(--primary-color)' });
        @endif
    </script>
</body>
</html>
