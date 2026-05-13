<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{asset('assets/assets/img/logo plateau.png')}}" />
    <title>Réinitialisation - Plateau App</title>
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
            background: #fef9c3; /* Yellowish for warning/reset feel */
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .illustration-circle i {
            font-size: 40px;
            color: #eab308;
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
            line-height: 1.5;
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

        .info-box {
            background: #f0f7ff;
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            color: #4a5568;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .info-box i {
            color: var(--primary-color);
            margin-top: 2px;
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

        .submit-btn:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            box-shadow: none;
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
            .title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <!-- Bouton Retour -->
        <a href="{{ route('login') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="illustration">
            <div class="illustration-circle">
                <i class="fas fa-key"></i>
            </div>
        </div>

        <div class="form-header">
            <h1 class="title">Mot de passe oublié</h1>
            <p class="subtitle">Entrez votre numéro pour réinitialiser votre accès</p>
        </div>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p>Un code OTP (6 chiffres) vous sera envoyé par SMS. Ce code expirera dans 60 minutes.</p>
        </div>

        <form id="resetForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Numéro de téléphone</label>
                <div class="input-wrapper">
                    <i class="fas fa-mobile-alt input-icon"></i>
                    <input class="input-field" type="tel" id="login_identifier" name="login_identifier" placeholder="Ex: 0708325027" required />
                </div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <i class="fas fa-paper-plane"></i> Obtenir mon code
            </button>
        </form>

        <div class="form-footer">
            <p>Vous vous souvenez du mot de passe ? <a href="{{ route('login') }}">Se connecter</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('resetForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const identifier = document.getElementById('login_identifier').value;
                const submitBtn = document.getElementById('submitBtn');
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';

                try {
                    const response = await fetch('/api/utilisateurs/forgot-password', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ login_identifier: identifier })
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Erreur lors de la demande.');

                    const { value: otpCode } = await Swal.fire({
                        title: 'Code de vérification',
                        text: data.message, 
                        input: 'text',
                        inputPlaceholder: 'Entrez le code à 6 chiffres',
                        showCancelButton: true,
                        confirmButtonText: 'Vérifier',
                        confirmButtonColor: 'var(--primary-color)',
                        cancelButtonText: 'Annuler',
                        inputValidator: (value) => { if (!value) return 'Le code est obligatoire !'; }
                    });

                    if (otpCode) {
                        Swal.showLoading();
                        const verifyResponse = await fetch('/api/utilisateurs/verify-reset-code', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ login_identifier: identifier, token: otpCode })
                        });
                        
                        const verifyData = await verifyResponse.json();
                        if (!verifyResponse.ok) throw new Error(verifyData.message || 'Code invalide');

                        const secureToken = verifyData.reset_token;

                        const { value: formValues } = await Swal.fire({
                            title: 'Nouveau mot de passe',
                            html:
                                '<input id="swal-input1" type="password" class="swal2-input" placeholder="Nouveau mot de passe">' +
                                '<input id="swal-input2" type="password" class="swal2-input" placeholder="Confirmer le mot de passe">',
                            focusConfirm: false,
                            showCancelButton: true,
                            confirmButtonText: 'Réinitialiser',
                            confirmButtonColor: 'var(--primary-color)',
                            preConfirm: () => {
                                const p1 = document.getElementById('swal-input1').value;
                                const p2 = document.getElementById('swal-input2').value;
                                if (!p1 || !p2) return Swal.showValidationMessage('Remplissez les deux champs');
                                if (p1 !== p2) return Swal.showValidationMessage('Les mots de passe diffèrent');
                                if (p1.length < 8) return Swal.showValidationMessage('Minimum 8 caractères');
                                return [p1, p2];
                            }
                        });

                        if (formValues) {
                            Swal.showLoading();
                            const resetResponse = await fetch('/api/utilisateurs/reset-password', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                                body: JSON.stringify({ 
                                    login_identifier: identifier, 
                                    token: secureToken,
                                    password: formValues[0],
                                    password_confirmation: formValues[1]
                                })
                            });

                            const resetData = await resetResponse.json();
                            if (!resetResponse.ok) throw new Error(resetData.message || 'Erreur finale');

                            await Swal.fire({ icon: 'success', title: 'Réussi !', text: 'Mot de passe mis à jour.', confirmButtonColor: 'var(--primary-color)' });
                            window.location.href = "{{ route('login') }}";
                        }
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: 'Oups', text: error.message, confirmButtonColor: 'var(--primary-color)' });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Obtenir mon code';
                }
            });
        });
    </script>
</body>
</html>