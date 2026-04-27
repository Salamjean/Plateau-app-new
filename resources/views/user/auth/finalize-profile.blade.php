<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{asset('assets/assets/img/logo plateau.png')}}" />
    <title>Finaliser mon profil</title>
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
                linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.8)),
                url('{{ asset('assets/assets/img/bavk.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 600px;
            background-color: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .header {
            text-align: center;
            padding: 30px;
            background: var(--primary-color);
            color: white;
        }

        .header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .content {
            padding: 40px;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            transition: 0.3s;
        }

        .input-field {
            width: 100%;
            height: 50px;
            padding-left: 45px;
            padding-right: 15px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            outline: none;
            font-size: 1rem;
            transition: 0.3s;
        }

        .input-field:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(25, 119, 204, 0.1);
        }

        .input-field:focus ~ .input-icon {
            color: var(--primary-color);
        }

        .label {
            position: absolute;
            left: 45px;
            top: 15px;
            color: #adb5bd;
            pointer-events: none;
            transition: 0.3s;
            background: white;
            padding: 0 5px;
        }

        .input-field:focus ~ .label,
        .input-field:not(:placeholder-shown) ~ .label {
            top: -10px;
            left: 35px;
            font-size: 0.8rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        .btn-submit {
            width: 100%;
            height: 55px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(25, 119, 204, 0.2);
        }

        .section-title {
            font-size: 1.1rem;
            color: var(--primary-color);
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 5px;
            font-weight: 600;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .diaspora-fields {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .diaspora-fields.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-msg {
            color: var(--error-color);
            font-size: 0.8rem;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container animate__animated animate__zoomIn">
        <div class="header">
            <img src="{{asset('assets/assets/img/logo plateau.png')}}" height="60" alt="Logo" style="margin-bottom: 15px; filter: brightness(0) invert(1);">
            <h1>Finalisez votre profil</h1>
            <p>Quelques informations de plus pour sécuriser votre compte</p>
        </div>

        <form action="{{ route('user.auth.profile.submit') }}" method="POST" class="content">
            @csrf

            <div class="section-title">Informations Personnelles</div>
            
            <div class="input-group">
                <input type="text" name="name" class="input-field" placeholder=" " value="{{ old('name', $user->name) }}" required>
                <label class="label">Nom de famille</label>
                <i class="fas fa-user input-icon"></i>
                @error('name') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="input-group">
                <input type="text" name="prenom" class="input-field" placeholder=" " value="{{ old('prenom', $user->prenom) }}" required>
                <label class="label">Prénom(s)</label>
                <i class="fas fa-user input-icon"></i>
                @error('prenom') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="input-group">
                <input type="email" name="email" class="input-field" placeholder=" " value="{{ old('email', $user->email) }}" {{ $user->google_id ? 'readonly style=background-color:#f0f0f0;color:#888;cursor:not-allowed;' : '' }}>
                <label class="label">Adresse Email {{ $user->google_id ? '' : '(Optionnelle)' }}</label>
                <i class="fas fa-envelope input-icon"></i>
                @error('email') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            @if(!$user->google_id)
            <div class="section-title">Sécurité</div>

            <div class="input-group">
                <input type="password" name="password" class="input-field" placeholder=" " required>
                <label class="label">Définir un mot de passe</label>
                <i class="fas fa-lock input-icon"></i>
                @error('password') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="input-group">
                <input type="password" name="password_confirmation" class="input-field" placeholder=" " required>
                <label class="label">Confirmer le mot de passe</label>
                <i class="fas fa-lock input-icon"></i>
            </div>
            @endif

            <div class="input-group">
                <input type="text" name="NNI" class="input-field" placeholder=" " value="{{ old('NNI', $user->NNI) }}">
                <label class="label">Numéro NNI (Fakultatif)</label>
                <i class="fas fa-id-card input-icon"></i>
                @error('NNI') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="checkbox-group">
                <input type="checkbox" name="diaspora" id="diaspora" value="1" {{ old('diaspora', $user->diaspora) ? 'checked' : '' }}>
                <label for="diaspora" style="cursor: pointer; font-weight: 600;">Je suis de la diaspora</label>
            </div>

            <div class="diaspora-fields {{ old('diaspora', $user->diaspora) ? 'active' : '' }}" id="diasporaBox">
                <div class="input-group">
                    <input type="text" name="pays_residence" class="input-field" placeholder=" " value="{{ old('pays_residence', $user->pays_residence) }}">
                    <label class="label">Pays de résidence</label>
                    <i class="fas fa-globe input-icon"></i>
                </div>
                <div class="input-group">
                    <input type="text" name="ville_residence" class="input-field" placeholder=" " value="{{ old('ville_residence', $user->ville_residence) }}">
                    <label class="label">Ville de résidence</label>
                    <i class="fas fa-city input-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-check-circle"></i> Finaliser l'inscription
            </button>
        </form>
    </div>

    <script>
        document.getElementById('diaspora').addEventListener('change', function() {
            const box = document.getElementById('diasporaBox');
            if (this.checked) {
                box.classList.add('active');
            } else {
                box.classList.remove('active');
            }
        });

        @if(Session::has('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oups...',
                text: '{{ Session::get('error') }}',
                confirmButtonColor: '#1977cc'
            });
        @endif
    </script>
</body>
</html>
