@extends('user.layouts.template')

@section('content')
<!-- Styles -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    :root {
        --primary-color: #1977cc;
        --secondary-color: #1977cc;
        --success-color: #2ecc71;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --light-bg: #f8f9fa;
        --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .form-background {
        padding: 30px;
        margin-top: 50px;
        border-radius: 16px;
    }

    .card-rounded {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        border: none;
    }

    .section-header {
        background-color: var(--primary-color);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .form-label {
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid #ddd;
        transition: all 0.3s;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(25, 119, 204, 0.25);
    }

    .btn-primary {
        background-color: var(--primary-color);
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
    }

    .btn-outline-secondary {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
    }

    .progress-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-bottom: 30px;
    }

    .progress-line {
        position: absolute;
        height: 4px;
        top: 15px;
        left: 0;
        right: 0;
        background-color: #e9ecef;
        z-index: 1;
    }

    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
        transition: all 0.3s ease;
    }

    .step-circle.active {
        background-color: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }

    .step-number {
        font-weight: bold;
    }

    .step-label {
        position: absolute;
        top: 45px;
        font-size: 0.75rem;
        white-space: nowrap;
        color: var(--primary-color);
        font-weight: 500;
    }

    .step {
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .required-field::after {
        content: " *";
        color: var(--danger-color);
    }
</style>

<div class="row flex-grow form-background">
    <div class="col-12 grid-margin stretch-card">
        <div class="card card-rounded">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold" style="color: var(--primary-color);">
                        <i class="fas fa-heart me-2"></i>Demande de Rendez-vous de Mariage
                    </h2>
                    <p class="text-muted">Remplissez le formulaire en quelques étapes simples</p>
                </div>

                <!-- Progress Steps -->
                <div class="progress-steps">
                    <div class="progress-line"></div>
                    @for($i = 1; $i <= 4; $i++)
                    <div class="step-circle {{ $i === 1 ? 'active' : '' }}" data-step="{{ $i }}">
                        <span class="step-number">{{ $i }}</span>
                        <small class="step-label d-none d-md-block">
                            @if($i === 1) Époux @endif
                            @if($i === 2) Épouse @endif
                            @if($i === 3) Contact @endif
                            @if($i === 4) Mariage @endif
                        </small>
                    </div>
                    @endfor
                </div>

                <form action="{{ route('user.rendezvous.store') }}" method="POST" id="multiStepForm">
                    @csrf

                    <!-- Step 1 -->
                    <div class="step">
                        <h4 class="section-header"><i class="fas fa-user me-2"></i>Informations sur l'époux</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required-field">Nom</label>
                                <input type="text" name="nom_epoux" class="form-control" value="{{ old('nom_epoux') }}" required>
                                @error('nom_epoux')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Prénom</label>
                                <input type="text" name="prenom_epoux" class="form-control" value="{{ old('prenom_epoux') }}" required>
                                @error('prenom_epoux')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Date de naissance</label>
                                <input type="date" name="date_naissance_epoux" class="form-control" required>
                                @error('date_naissance_epoux')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Lieu de naissance</label>
                                <input type="text" name="lieu_naissance_epoux" class="form-control" required>
                                @error('lieu_naissance_epoux')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="step d-none">
                        <h4 class="section-header"><i class="fas fa-user-female me-2"></i>Informations sur l'épouse</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required-field">Nom</label>
                                <input type="text" name="nom_epouse" class="form-control" required>
                                @error('nom_epouse')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Prénom</label>
                                <input type="text" name="prenom_epouse" class="form-control" required>
                                @error('prenom_epouse')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Date de naissance</label>
                                <input type="date" name="date_naissance_epouse" class="form-control" required>
                                @error('date_naissance_epouse')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Lieu de naissance</label>
                                <input type="text" name="lieu_naissance_epouse" class="form-control" required>
                                @error('lieu_naissance_epouse')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="step d-none">
                        <h4 class="section-header"><i class="fas fa-address-book me-2"></i>Coordonnées</h4>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label required-field">Adresse</label>
                                <input type="text" name="adresse" class="form-control" required>
                                @error('adresse')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Téléphone</label>
                                <input type="tel" name="telephone" class="form-control" required>
                                @error('telephone')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Email</label>
                                <input type="email" name="email" class="form-control" required>
                                @error('email')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="step d-none">
                        <h4 class="section-header"><i class="fas fa-heart me-2"></i>Informations sur le mariage</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required-field">Date souhaitée</label>
                                <input type="date" name="date_mariage_souhaitee" class="form-control" required>
                                @error('date_mariage_souhaitee')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">Heure souhaitée</label>
                                <input type="time" name="heure_souhaitee" class="form-control" required>
                                @error('heure_souhaitee')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                    <label class="form-check-label" for="termsCheck">
                                        Je certifie que les informations fournies sont exactes
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="mt-5 d-flex justify-content-between">
                        <button type="button" id="prevBtn" class="btn btn-outline-secondary d-none">
                            <i class="fas fa-arrow-left me-2"></i>Précédent
                        </button>
                        <button type="button" id="nextBtn" class="btn btn-primary ms-auto">
                            Suivant <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        <button type="submit" id="submitBtn" class="btn btn-success d-none">
                            <i class="fas fa-check-circle me-2"></i>Soumettre la demande
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const steps = document.querySelectorAll('.step');
        const stepCircles = document.querySelectorAll('.step-circle');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        let currentStep = 0;

        function updateProgress() {
            // Update step circles
            stepCircles.forEach((circle, index) => {
                if (index <= currentStep) {
                    circle.classList.add('active');
                } else {
                    circle.classList.remove('active');
                }
            });
            
            // Update buttons visibility
            prevBtn.classList.toggle('d-none', currentStep === 0);
            nextBtn.classList.toggle('d-none', currentStep === steps.length - 1);
            submitBtn.classList.toggle('d-none', currentStep !== steps.length - 1);
            
            // Animate step transition
            steps.forEach((step, index) => {
                if (index === currentStep) {
                    step.classList.remove('d-none');
                    step.style.animation = 'fadeIn 0.5s ease';
                } else {
                    step.classList.add('d-none');
                }
            });
        }

        nextBtn.addEventListener('click', () => {
            // Validate current step before proceeding
            const currentStepInputs = steps[currentStep].querySelectorAll('[required]');
            let isValid = true;
            
            currentStepInputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                Swal.fire({
                    title: 'Champs requis',
                    text: 'Veuillez remplir tous les champs obligatoires avant de continuer.',
                    icon: 'warning',
                    confirmButtonColor: '#1977cc'
                });
                return;
            }

            if (currentStep < steps.length - 1) {
                currentStep++;
                updateProgress();
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                updateProgress();
            }
        });

        // Initialize
        updateProgress();

        // Form submission handling
        document.getElementById('multiStepForm').addEventListener('submit', function(e) {
            if (!document.getElementById('termsCheck').checked) {
                e.preventDefault();
                Swal.fire({
                    title: 'Validation requise',
                    text: 'Vous devez certifier que les informations sont exactes avant de soumettre.',
                    icon: 'warning',
                    confirmButtonColor: '#1977cc'
                });
            }
        });

        @if(session('success'))
            Swal.fire({
                title: 'Succès',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#1977cc'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'Erreur',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#1977cc'
            });
        @endif
    });
</script>

@endsection