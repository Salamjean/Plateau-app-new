@extends('user.layouts.template')
@section('content')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Votre CSS existant reste le même */
    :root {
        --primary: #1977cc;
        --primary-dark: #125ea3;
        --navy: #2b3674;
        --navy-light: #3a478c;
        --secondary: #10B981;
        --accent: #F59E0B;
        --danger: #EE5D50;
        --warning: #F59E0B;
        --info: #06B6D4;
        --light: #f4f7fe;
        --dark: #2b3674;
        --grey: #a3aed0;
        --gray: #a3aed0;
        --gray-light: #e0e5f2;
        --shadow: 0 5px 14px rgba(0, 0, 0, 0.05);
        --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.08);
        --radius: 20px;
        --radius-sm: 12px;
        --transition: all 0.3s ease;
    }

    .profile-cards-container {
        min-height: 100vh;
        padding: 30px 20px;
    }

    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
        gap: 30px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: none;
        transition: var(--transition);
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .card-header {
        background: transparent;
        color: var(--navy);
        padding: 25px 30px 15px;
        border-bottom: none;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--navy);
    }

    .card-title i {
        color: var(--primary);
        font-size: 1.4rem;
        background: rgba(25, 119, 204, 0.1);
        padding: 10px;
        border-radius: var(--radius-sm);
    }

    .card-body {
        padding: 15px 30px 30px;
    }

    .form-group {
        margin-bottom: 22px;
        position: relative;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--navy);
        font-size: 0.9rem;
    }

    .form-control1 {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid var(--gray-light);
        border-radius: var(--radius-sm);
        font-size: 0.95rem;
        color: var(--navy);
        transition: var(--transition);
        background: var(--light);
    }

    .form-control1:focus {
        outline: none;
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(25, 119, 204, 0.1);
    }

    .form-control1.has-icon {
        padding-left: 50px;
    }

    .input-icon {
        position: absolute;
        left: 18px;
        top: 42px;
        color: var(--grey);
        transition: var(--transition);
    }

    .form-control1:focus ~ .input-icon {
        color: var(--primary);
    }

    .phone-group {
        display: flex;
        gap: 12px;
    }

    .phone-select {
        flex: 0 0 110px;
        border: 2px solid var(--gray-light);
        border-radius: var(--radius-sm);
        padding: 14px;
        background: var(--light);
        color: var(--navy);
        font-weight: 600;
    }

    .phone-select:focus {
        border-color: var(--primary);
        background: white;
        outline: none;
    }

    .checkbox-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px;
        background: var(--light);
        border-radius: var(--radius-sm);
        border-left: 4px solid var(--primary);
        margin-bottom: 25px;
    }

    .checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid var(--gray);
        border-radius: 4px;
        position: relative;
        cursor: pointer;
    }

    .checkbox input {
        opacity: 0;
        position: absolute;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .checkbox i {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 0.8rem;
        opacity: 0;
        transition: var(--transition);
    }

    .checkbox input:checked {
        background: var(--primary);
        border-color: var(--primary);
    }

    .checkbox input:checked + i {
        opacity: 1;
    }

    .avatar-card {
        text-align: center;
    }

    .avatar-container {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        margin: 0 auto 20px;
        overflow: hidden;
        border: 4px solid var(--light);
        box-shadow: 0 8px 16px rgba(43, 54, 116, 0.1);
        position: relative;
        cursor: pointer;
        transition: var(--transition);
        background: white;
    }

    .avatar-container:hover {
        border-color: var(--primary);
        transform: scale(1.05);
        box-shadow: 0 12px 20px rgba(25, 119, 204, 0.2);
    }

    .avatar-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--light), #e0e5f2);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-placeholder i {
        font-size: 3.5rem;
        color: var(--grey);
    }

    .avatar-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin: 10px auto;
        border: 4px solid var(--primary);
        box-shadow: 0 8px 16px rgba(25, 119, 204, 0.2);
        display: none;
    }

    .btn {
        padding: 14px 28px;
        border: none;
        border-radius: var(--radius-sm);
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        box-shadow: 0 4px 12px rgba(25, 119, 204, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(25, 119, 204, 0.4);
        color: white;
    }

    .btn-block {
        width: 100%;
        justify-content: center;
    }

    .diaspora-fields {
        display: none;
        margin-top: 20px;
        padding: 20px;
        background: linear-gradient(135deg, #F0FDF4, #DCFCE7);
        border-radius: 8px;
        border-left: 4px solid var(--secondary);
    }

    .diaspora-fields.active {
        display: block;
        animation: slideDown 0.3s ease;
    }

    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid;
    }

    .alert-success {
        background: #F0FDF4;
        border-color: var(--secondary);
        color: #065F46;
    }

    .alert-error {
        background: #FEF2F2;
        border-color: var(--danger);
        color: #991B1B;
    }

    .error-message {
        color: var(--danger);
        font-size: 0.85rem;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .bouton {
        margin-top: 50px;
        display: flex;
        justify-content: center;
    }

    .bouton .btn-primary {
        width: 100%;
        max-width: 400px;
        padding: 16px;
        font-size: 1.1rem;
        border-radius: var(--radius);
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .cards-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .phone-group {
            flex-direction: column;
        }
        
        .phone-select {
            flex: 1;
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .profile-cards-container {
            padding: 20px 15px;
        }
        
        .card-header {
            padding: 15px 20px;
        }
        
        .card-body {
            padding: 15px;
        }
    }
</style>

@php use Illuminate\Support\Str; @endphp

<div class="profile-cards-container">
    <!-- Le formulaire DOIT englober toutes les cartes -->
    <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" id="profileForm">
        @csrf
        @method('PUT') <!-- Changé de POST à PUT -->

        <input type="file" name="profile_picture" id="profile_picture" accept="image/*" style="display: none;">

        <div class="cards-grid">
            <!-- Carte Avatar -->
            <div class="card avatar-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-camera"></i>
                        Photo de Profil
                    </h3>
                </div>
                <div class="card-body">
                    <div class="avatar-container" onclick="document.getElementById('profile_picture').click()">
                        @if(auth()->user()->profile_picture)
                            @php
                                $pic = auth()->user()->profile_picture;
                                $picUrl = Str::startsWith($pic, ['http://', 'https://'])
                                    ? $pic
                                    : asset('storage/' . $pic);
                            @endphp
                            <img src="{{ $picUrl }}" alt="Photo de profil" id="currentAvatar">
                        @else
                            <div class="avatar-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <img src="" alt="Aperçu" class="avatar-preview" id="avatarPreview">
                    
                    <p style="color: var(--gray); font-size: 0.9rem; margin-top: 10px;">
                        Cliquez sur l'avatar pour changer la photo
                    </p>
                </div>
            </div>

            <!-- Carte Informations Personnelles -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-circle"></i>
                        Informations Personnelles
                    </h3>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label" for="name">Nom</label>
                        <input type="text" class="form-control1 has-icon" name="name" 
                               value="{{ old('name', auth()->user()->name) }}" placeholder="Votre nom" required>
                        <i class="fas fa-user input-icon"></i>
                        @error('name')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="prenom">Prénom</label>
                        <input type="text" class="form-control1 has-icon" name="prenom" 
                               value="{{ old('prenom', auth()->user()->prenom) }}" placeholder="Votre prénom" required>
                        <i class="fas fa-user input-icon"></i>
                        @error('prenom')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Adresse Email (Optionnelle)</label>
                        <input type="email" class="form-control1 has-icon" name="email" 
                               value="{{ old('email', auth()->user()->email) }}" placeholder="votre@email.com">
                        <i class="fas fa-envelope input-icon"></i>
                        @error('email')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Carte Contact -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-address-book"></i>
                        Informations de Contact
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Numéro de Téléphone</label>
                        <div class="phone-group">
                            <select name="indicatif" class="phone-select" required>
                                <option value="+225" {{ old('indicatif', auth()->user()->indicatif) == '+225' ? 'selected' : '' }}>+225</option>
                                <option value="+33" {{ old('indicatif', auth()->user()->indicatif) == '+33' ? 'selected' : '' }}>+33</option>
                                <option value="+1" {{ old('indicatif', auth()->user()->indicatif) == '+1' ? 'selected' : '' }}>+1</option>
                                <option value="+32" {{ old('indicatif', auth()->user()->indicatif) == '+32' ? 'selected' : '' }}>+32</option>
                            </select>
                            <input type="tel" class="form-control1" name="contact" 
                                   value="{{ old('contact', auth()->user()->contact) }}" placeholder="Votre numéro" required>
                        </div>
                        @error('contact')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="commune">Commune de Naissance</label>
                        <select class="form-control1" name="commune" required>
                            <option value="">Sélectionnez votre commune</option>
                            <option value="abobo" {{ old('commune', auth()->user()->commune) == 'abobo' ? 'selected' : '' }}>Abobo</option>
                            <option value="plateau" {{ old('commune', auth()->user()->commune) == 'plateau' ? 'selected' : '' }}>Plateau</option>
                            <option value="cocody" {{ old('commune', auth()->user()->commune) == 'cocody' ? 'selected' : '' }}>Cocody</option>
                            <option value="yopougon" {{ old('commune', auth()->user()->commune) == 'yopougon' ? 'selected' : '' }}>Yopougon</option>
                        </select>
                        @error('commune')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="CMU">Numéro CMU</label>
                        <input type="text" class="form-control1 has-icon" name="CMU" 
                               value="{{ old('CMU', auth()->user()->CMU) }}" placeholder="Votre numéro CMU">
                        <i class="fas fa-id-card input-icon"></i>
                        @error('CMU')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Carte Diaspora -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-globe-americas"></i>
                        Statut Diaspora
                    </h3>
                </div>
                <div class="card-body">
                    <div class="checkbox-card">
                        <div class="checkbox">
                            <input type="checkbox" id="diaspora" name="diaspora" value="1" 
                                   {{ old('diaspora', auth()->user()->diaspora) ? 'checked' : '' }}>
                            <i class="fas fa-check"></i>
                        </div>
                        <label for="diaspora" style="font-weight: 500; cursor: pointer;">
                            Je réside à l'étranger (Diaspora)
                        </label>
                    </div>

                    <div class="diaspora-fields {{ old('diaspora', auth()->user()->diaspora) ? 'active' : '' }}" id="diasporaFields">
                        <div class="form-group">
                            <label class="form-label" for="pays_residence">Pays de Résidence</label>
                            <select class="form-control1" name="pays_residence">
                                <option value="">Sélectionnez votre pays</option>
                                <option value="france" {{ old('pays_residence', auth()->user()->pays_residence) == 'france' ? 'selected' : '' }}>France</option>
                                <option value="usa" {{ old('pays_residence', auth()->user()->pays_residence) == 'usa' ? 'selected' : '' }}>États-Unis</option>
                                <option value="canada" {{ old('pays_residence', auth()->user()->pays_residence) == 'canada' ? 'selected' : '' }}>Canada</option>
                                <option value="belgique" {{ old('pays_residence', auth()->user()->pays_residence) == 'belgique' ? 'selected' : '' }}>Belgique</option>
                            </select>
                            @error('pays_residence')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="ville_residence">Ville de Résidence</label>
                            <input type="text" class="form-control1" name="ville_residence" 
                                   value="{{ old('ville_residence', auth()->user()->ville_residence) }}" placeholder="Votre ville">
                            @error('ville_residence')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="adresse_etrangere">Adresse Complète</label>
                            <textarea class="form-control1" name="adresse_etrangere" rows="3" 
                                      placeholder="Votre adresse à l'étranger">{{ old('adresse_etrangere', auth()->user()->adresse_etrangere) }}</textarea>
                            @error('adresse_etrangere')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bouton">
            <button type="button" class="btn btn-primary btn-block" onclick="confirmPassword()">
                <i class="fas fa-save"></i>
                Enregistrer les Modifications
            </button>
        </div>
    </form> <!-- Fermeture du formulaire après toutes les cartes -->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de la diaspora
        const diasporaCheckbox = document.getElementById('diaspora');
        const diasporaFields = document.getElementById('diasporaFields');
        
        diasporaCheckbox.addEventListener('change', function() {
            if (this.checked) {
                diasporaFields.classList.add('active');
            } else {
                diasporaFields.classList.remove('active');
            }
        });

        // Prévisualisation de l'avatar
        const profilePictureInput = document.getElementById('profile_picture');
        const avatarPreview = document.getElementById('avatarPreview');
        const currentAvatar = document.getElementById('currentAvatar');

        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                    avatarPreview.style.display = 'block';
                    if (currentAvatar) {
                        currentAvatar.style.display = 'none';
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    });

    // Détecter si l'utilisateur est connecté via Google (pas de vrai mot de passe)
    const isGoogleUser = {{ auth()->user()->google_id ? 'true' : 'false' }};

    function confirmPassword() {
        // Les utilisateurs Google n'ont pas de mot de passe → soumettre directement
        if (isGoogleUser) {
            document.getElementById('profileForm').submit();
            return;
        }

        Swal.fire({
            title: '🔒 Confirmation de sécurité',
            html: `
                <div style="text-align: center;">
                    <p>Pour modifier votre profil, veuillez confirmer votre identité</p>
                    <input type="password" id="password" class="swal2-input" placeholder="Votre mot de passe actuel" style="width: 80%">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Confirmer',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#3B82F6',
            cancelButtonColor: '#6B7280',
            focusConfirm: false,
            preConfirm: () => {
                const password = Swal.getPopup().querySelector('#password').value;
                if (!password) {
                    Swal.showValidationMessage('Veuillez entrer votre mot de passe');
                    return false;
                }
                return { password: password };
            },
            didOpen: () => {
                const popup = Swal.getPopup();
                const input = popup.querySelector('#password');
                input.focus();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Afficher un indicateur de chargement
                Swal.showLoading();

                // Vérification du mot de passe via AJAX
                fetch('{{ route("user.verify.password") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ password: result.value.password })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Créer un champ caché pour le mot de passe
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'password';
                        hiddenInput.value = result.value.password;
                        document.getElementById('profileForm').appendChild(hiddenInput);

                        // Soumettre le formulaire après vérification réussie
                        document.getElementById('profileForm').submit();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: data.message || 'Mot de passe incorrect',
                            confirmButtonColor: '#EF4444'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Une erreur est survenue lors de la vérification',
                        confirmButtonColor: '#EF4444'
                    });
                });
            }
        });
    }
</script>

@endsection