<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{asset('assets/assets/img/logo plateau.png')}}" />
    <title>Inscription - Plateau App</title>
    <style>
        :root {
            --primary-color: #1a66ff;
            --secondary-color: #1a66ff;
            --accent-color: #4895ef;
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
            background: linear-gradient(135deg, #f0f4ff 0%, #e0eaff 100%);
            padding: 20px;
        }

        .form-container {
            background-color: white;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            position: relative;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
            margin-bottom: 20px;
        }

        .illustration-circle {
            width: 100px;
            height: 100px;
            background: #f0fdf4;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .illustration-circle i {
            font-size: 40px;
            color: #16a34a;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .title {
            font-size: 2rem;
            font-weight: 800;
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
            font-size: 0.95rem;
            margin-top: 15px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            font-size: 0.95rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
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
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(26, 102, 255, 0.2);
        }

        .submit-btn:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            box-shadow: none;
        }

        .otp-box {
            display: none;
            margin-top: 20px;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0;
            color: #adb5bd;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .separator::before, .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1.5px solid #f1f3f5;
        }

        .separator::before { margin-right: 15px; }
        .separator::after { margin-left: 15px; }

        .social-btns {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .social-btn {
            width: 100%;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .google-btn {
            background: white;
            border: 1.5px solid #e1e8ef;
            color: #1a1a1a;
        }

        .apple-btn {
            background: #000;
            border: none;
            color: white;
        }

        .form-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.95rem;
            color: var(--text-muted);
        }

        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 700;
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <!-- Bouton Retour -->
        <a href="{{ route('home') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="illustration">
            <div class="illustration-circle">
                <i class="fas fa-user-plus"></i>
            </div>
        </div>

        <div class="form-header">
            <h1 class="title">Inscription</h1>
            <p class="subtitle">Créez votre compte en quelques secondes</p>
        </div>

        <div id="phone-section">
            <div class="form-group">
                <label class="form-label">Numéro de téléphone</label>
                <div class="input-wrapper">
                    <input type="hidden" id="otp_indicatif" value="+225">
                    <i class="fas fa-mobile-alt input-icon"></i>
                    <input id="otp_contact" class="input-field" type="tel" placeholder="Ex: 0708325027">
                </div>
            </div>

            <button type="button" class="submit-btn" id="btnSendOtp">
                <i class="fas fa-paper-plane"></i> Recevoir le code par SMS
            </button>

            <div class="otp-box" id="otpBox">
                <div class="form-group">
                    <label class="form-label">Code de vérification</label>
                    <div class="input-wrapper">
                        <i class="fas fa-shield-alt input-icon"></i>
                        <input class="input-field" type="text" id="otp_code" placeholder="Entrez les 6 chiffres" maxlength="6">
                    </div>
                </div>
                <button type="button" class="submit-btn" id="btnVerifyOtp">
                    Valider et continuer <i class="fas fa-arrow-right"></i>
                </button>
                <p style="text-align: center; margin-top: 15px; font-size: 0.85rem;">
                    Pas reçu ? <a href="javascript:void(0)" id="resendOtp" style="color: var(--primary-color); font-weight: 700;">Renvoyer</a>
                </p>
            </div>
        </div>

        <div class="separator">OU</div>

        <div class="social-btns">
            <button type="button" class="social-btn google-btn" id="googlePhoneBtn">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="22">
                S'inscrire avec Google
            </button>

            <button type="button" class="social-btn apple-btn" id="applePhoneBtn">
                <svg width="20" height="20" viewBox="0 0 814 1000" fill="white"><path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-57.8-155.5-127.4C46.5 700 0 571.8 0 449.3c0-152.5 99.5-233.1 197.3-233.1 69.1 0 126.4 45.3 170 45.3 42.1 0 108.5-47.9 188.2-47.9 30.1 0 108.2 2.6 168.6 80.6zm-80.6-171.4c31.5-38.5 53.9-89.2 53.9-139.9 0-7.1-.6-14.3-1.9-20.1-50.6 1.9-110.8 33.7-147.1 75.8-28.5 32.4-55.1 83.1-55.1 134.5 0 7.8 1.3 15.6 1.9 18.1 3.2.6 8.4 1.3 13.6 1.3 45.4 0 102.5-30.4 134.7-69.7z"/></svg>
                S'inscrire avec Apple
            </button>
        </div>

        <div class="form-footer">
            <p>Déjà inscrit ? <a href="{{route('login')}}">Se connecter</a></p>
        </div>
    </div>

    <!-- Firebase SDKs -->
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                    }).then(r => r.json())
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
                    }).catch(() => {
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
                    }).then(r => r.json())
                    .then(data => {
                        if(data.success) {
                            window.location.href = data.redirect;
                        } else {
                            Swal.fire('Erreur', data.message, 'error');
                            this.disabled = false;
                            this.innerHTML = 'Valider et continuer <i class="fas fa-arrow-right"></i>';
                        }
                    }).catch(() => {
                        this.disabled = false;
                        this.innerHTML = 'Valider et continuer <i class="fas fa-arrow-right"></i>';
                    });
                });
            }

            // Firebase Logic
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

            function handleSocial(btnId, endpoint, providerName) {
                const btn = document.getElementById(btnId);
                if (!btn) return;
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (!auth) return Swal.fire('Erreur', 'Firebase non configuré', 'error');
                    
                    const provider = providerName === 'google' ? new firebase.auth.GoogleAuthProvider() : new firebase.auth.OAuthProvider('apple.com');
                    if (providerName === 'apple') { provider.addScope('email'); provider.addScope('name'); }

                    auth.signInWithPopup(provider).then((result) => {
                        Swal.fire({ title: 'Vérification...', allowOutsideClick: false, showConfirmButton: false, willOpen: () => Swal.showLoading() });
                        return result.user.getIdToken();
                    }).then((idToken) => {
                        return fetch(endpoint, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                            body: JSON.stringify({ id_token: idToken })
                        });
                    }).then(r => r.json())
                    .then(data => {
                        if (data.success) window.location.href = data.redirect;
                        else Swal.fire('Erreur', data.message, 'error');
                    }).catch(() => {
                        Swal.fire('Erreur', 'Action annulée ou erreur réseau', 'error');
                    });
                });
            }

            handleSocial('googlePhoneBtn', '/user/auth/google', 'google');
            handleSocial('applePhoneBtn', '/user/auth/apple', 'apple');
        });
    </script>
</body>
</html>