<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{asset('assets/assets/img/logo plateau.png')}}" />
    <title>Mot de passe oublié</title>
    <style>
        /* Reprenez le même CSS que votre vue de login */
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
            line-height: 1.4;
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
            text-decoration: none;
        }

        .back-btn:hover {
            background: var(--secondary-color);
            transform: translateX(-3px);
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

        .info-box {
            background: linear-gradient(to right, #e3f2fd, #f3e5f5);
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #5a5a5a;
        }

        .info-box i {
            color: var(--primary-color);
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <form id="resetForm" class="form-container animate__animated animate__fadeIn">
        <!-- Bouton Retour -->
        <a href="{{ route('login') }}" class="back-btn animate__animated animate__fadeInLeft">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="form-header">
            <h1 class="title">Mot de passe oublié</h1>
            <p class="subtitle">Entrez votre email ou numéro de téléphone pour recevoir un code OTP</p>
        </div>

        @csrf

        <!-- Message d'information -->
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            Un code OTP (6 chiffres) vous sera envoyé par email ou par SMS. Ce code expirera dans 60 minutes.
        </div>

        <!-- Identifier Field -->
        <div class="input-group">
            <i class="fas fa-user input-icon"></i>
            <input class="input-field" type="text" id="login_identifier" name="login_identifier" placeholder=" " required />
            <label class="input-label" for="login_identifier">Email ou Numéro de téléphone</label>
        </div>

        <button type="submit" class="submit-btn animate__animated animate__pulse">
            <i class="fas fa-paper-plane"></i> Obtenir mon code de réinitialisation
        </button>

        <div class="form-footer">
            <p>Vous vous souvenez de votre mot de passe ? <a href="{{ route('login') }}">Se connecter</a></p>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('resetForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const identifier = document.getElementById('login_identifier').value;
                const submitBtn = this.querySelector('.submit-btn');
                
                // Loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement en cours...';

                try {
                    // Etape 1: Envoyer la demande
                    const response = await fetch('/api/utilisateurs/forgot-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ login_identifier: identifier })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Une erreur est survenue lors de la demande.');
                    }

                    // Etape 2: Demander le code OTP
                    const { value: otpCode } = await Swal.fire({
                        title: 'Code de vérification',
                        text: data.message, 
                        input: 'text',
                        inputPlaceholder: 'Entrez le code à 6 chiffres',
                        showCancelButton: true,
                        confirmButtonText: 'Vérifier',
                        cancelButtonText: 'Annuler',
                        inputValidator: (value) => {
                            if (!value) return 'Vous devez entrer le code !';
                        }
                    });

                    if (otpCode) {
                        // Vérifier le code
                        Swal.showLoading();
                        const verifyResponse = await fetch('/api/utilisateurs/verify-reset-code', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ login_identifier: identifier, token: otpCode })
                        });
                        
                        const verifyData = await verifyResponse.json();
                        
                        if (!verifyResponse.ok) {
                            throw new Error(verifyData.message || 'Code invalide ou expiré');
                        }

                        const secureToken = verifyData.reset_token;

                        // Etape 3: Nouveau mot de passe
                        const { value: formValues } = await Swal.fire({
                            title: 'Nouveau mot de passe',
                            html:
                                '<input id="swal-input1" type="password" class="swal2-input" placeholder="Nouveau mot de passe">' +
                                '<input id="swal-input2" type="password" class="swal2-input" placeholder="Confirmer le mot de passe">',
                            focusConfirm: false,
                            showCancelButton: true,
                            confirmButtonText: 'Réinitialiser',
                            preConfirm: () => {
                                const pwd1 = document.getElementById('swal-input1').value;
                                const pwd2 = document.getElementById('swal-input2').value;
                                if (!pwd1 || !pwd2) {
                                    Swal.showValidationMessage('Veuillez remplir les deux champs');
                                    return false;
                                }
                                if (pwd1 !== pwd2) {
                                    Swal.showValidationMessage('Les mots de passe ne correspondent pas');
                                    return false;
                                }
                                if (pwd1.length < 8) {
                                    Swal.showValidationMessage('Le mot de passe doit faire au moins 8 caractères');
                                    return false;
                                }
                                return [pwd1, pwd2];
                            }
                        });

                        if (formValues) {
                            Swal.showLoading();
                            const resetResponse = await fetch('/api/utilisateurs/reset-password', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ 
                                    login_identifier: identifier, 
                                    token: secureToken,
                                    password: formValues[0],
                                    password_confirmation: formValues[1]
                                })
                            });

                            const resetData = await resetResponse.json();

                            if (!resetResponse.ok) {
                                throw new Error(resetData.message || 'Erreur lors de la réinitialisation');
                            }

                            await Swal.fire({
                                icon: 'success',
                                title: 'Succès !',
                                text: 'Votre mot de passe a été réinitialisé avec succès.',
                                confirmButtonText: 'Se connecter'
                            });
                            window.location.href = "{{ route('login') }}";
                        }
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Action impossible',
                        text: error.message,
                        confirmButtonText: 'OK',
                        background: 'var(--light-color)'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Obtenir mon code de réinitialisation';
                }
            });
        });
    </script>
</body>
</html>