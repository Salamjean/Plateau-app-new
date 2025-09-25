@extends('mairie.layouts.template')
@section('content')
<!-- Inclusion des liens CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-lg border-0 rounded-20">
                <div class="card-header py-4 text-center bg-gradient-primary rounded-top-20">
                    <h2 class="mb-0 fw-bold text-white"><i class="fas fa-home me-2 text-white"></i>Enregistrement le service de livraison</h2>
                    <p class="mb-0 mt-2 opacity-75 text-white">Formulaire d'enregistrement par la Mairie</p>
                </div>

                <div class="card-body px-4 px-md-5 py-4">
                    <form method="POST" action="{{route('post.store')}}" enctype="multipart/form-data" id="registrationForm">
                        @csrf
                        
                        <!-- Progress bar -->
                        <div class="mb-5">
                            <div class="progress-steps">
                                <div class="step active" data-step="1">
                                    <span class="step-number" >1</span>
                                    <span class="step-label">Informations</span>
                                </div>
                                <div class="step" data-step="2">
                                    <span class="step-number">2</span>
                                    <span class="step-label">Photo</span>
                                </div>
                                <div class="step" data-step="3">
                                    <span class="step-number">3</span>
                                    <span class="step-label">Confirmation</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 1: Personal Information -->
                        <div class="step-content" id="step1">
                            <div class="mb-4">
                                <h4 class="border-bottom pb-2" style="color:#1977cc"><i class="fas fa-user-circle me-2"></i>Informations Personnelles</h4>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name_respo" class="form-label">Nom complet du responsable <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-user " style="color:#1977cc"></i></span>
                                        <input type="text" class="form-control rounded-end" id="name_respo" name="name" value="Service de livraison" readonly>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-envelope" style="color:#1977cc"></i></span>
                                        <input type="email" class="form-control rounded-end" id="email" name="email" required 
                                               placeholder="adresse@exemple.com">
                                    </div>
                                    <div class="form-text">Un lien de confirmation sera envoyé à cette adresse</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contact" class="form-label">Numéro de téléphone <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-phone" style="color:#1977cc"></i></span>
                                        <input type="text" class="form-control rounded-end" id="contact" name="contact" required 
                                               placeholder="Ex: +225 07 08 09 10 11">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="commune" class="form-label">Commune <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt" style="color:#1977cc"></i></span>
                                        <input type="text" class="form-control rounded-end" id="commune" name="commune" required 
                                               placeholder="Nom de la commune">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <div></div> <!-- Empty div for spacing -->
                                <button type="button" class="btn btn-primary btn-next" data-next="2">
                                    Suivant <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Profile Picture -->
                        <div class="step-content d-none" id="step2">
                            <div class="mb-4">
                                <h4 class="border-bottom pb-2" style="color:#1977cc"><i class="fas fa-camera me-2"></i>Photo de profil</h4>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <div class="file-upload-container text-center">
                                        <input type="file" class="file-input" id="profile_picture" name="profile_picture" 
                                               accept="image/*" hidden>
                                        <label for="profile_picture" class="file-upload-label">
                                            <div class="file-upload-icon">
                                                <i class="fas fa-cloud-upload-alt fa-3x  style="color:#1977cc"mb-3"></i>
                                            </div>
                                            <h5>Glissez-déposez votre photo ou cliquez pour parcourir</h5>
                                            <p class="text-muted">Format JPG, PNG ou GIF - Max 2MB</p>
                                        </label>
                                        <div id="file-preview" class="mt-3 d-none">
                                            <img src="" alt="Aperçu" class="preview-image rounded-circle shadow" width="120" height="120">
                                            <p class="file-name mt-2 fw-medium"></p>
                                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="remove-file">
                                                <i class="fas fa-times me-1"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev" data-prev="1">
                                    <i class="fas fa-arrow-left me-2"></i>Précédent
                                </button>
                                <button type="button" class="btn btn-primary btn-next" data-next="3">
                                    Suivant <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Confirmation -->
                        <div class="step-content d-none" id="step3">
                            <div class="mb-4">
                                <h4 class="border-bottom pb-2" style="color:#1977cc"><i class="fas fa-check-circle me-2"></i>Confirmation</h4>
                            </div>
                            
                            <div class="confirmation-summary bg-light rounded-15 p-4 mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="fw-medium">Nom du service</p>
                                        <p id="summary-name" class="text-muted mb-3">-</p>
                                        
                                        <p class="fw-medium">Email:</p>
                                        <p id="summary-email" class="text-muted mb-3">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="fw-medium">Téléphone:</p>
                                        <p id="summary-contact" class="text-muted mb-3">-</p>
                                        
                                        <p class="fw-medium">Commune:</p>
                                        <p id="summary-commune" class="text-muted mb-3">-</p>
                                    </div>
                                </div>
                                <div class="text-center mt-3" id="summary-photo-container">
                                    <p class="fw-medium">Photo de profil:</p>
                                    <div id="summary-photo" class="d-inline-block"></div>
                                </div>
                            </div>
                            
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="confirmationCheck" required>
                                <label class="form-check-label" for="confirmationCheck">
                                    Je confirme que les informations ci-dessus sont correctes
                                </label>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-prev" data-prev="2">
                                    <i class="fas fa-arrow-left me-2"></i>Précédent
                                </button>
                                <button type="submit" class="btn btn-success px-4 py-2 fw-bold" id="submit-btn">
                                    <i class="fas fa-save me-2"></i>Enregistrer le responsable
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #1977cc;
        --secondary: #1977cc;
        --light-bg: #f8f9fa;
        --dark-text: #333;
        --border-radius: 12px;
    }
    
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        font-family: 'Poppins', sans-serif;
        color: #333;
    }
    
    .card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--primary) 0%, #1977cc 100%) !important;
    }
    
    .rounded-20 {
        border-radius: 20px !important;
    }
    
    .rounded-top-20 {
        border-top-left-radius: 20px !important;
        border-top-right-radius: 20px !important;
    }
    
    .form-control, .form-select {
        border-radius: 10px;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 0.25rem rgba(255, 136, 0, 0.15);
    }
    
    .input-group-text {
        background-color: var(--light-bg);
        border: 1px solid #e2e8f0;
        border-right: none;
    }
    
    .input-group .form-control.rounded-end {
        border-left: none;
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    
    .btn {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, #1977cc 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #61819d 0%, #61819d 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 126, 0, 0.2);
    }
    
    .btn-success {
        background: linear-gradient(135deg, var(--secondary) 0%, #1977cc 100%);
        border: none;
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #1977cc 0%, #1977cc 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 136, 0, 0.2);
    }
    
    .progress-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-bottom: 30px;
        counter-reset: step;
    }
    
    .progress-steps::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 0;
        width: 100%;
        height: 4px;
        background: #e2e8f0;
        z-index: 1;
    }
    
    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
    }
    
    .step-number {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: white;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }
    
    .step.active .step-number {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }
    
    .step-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: #64748b;
    }
    
    .step.active .step-label {
        color: var(--primary);
        font-weight: 600;
    }
    
    .file-upload-container {
        margin: 0 auto;
    }
    
    .file-upload-label {
        border: 2px dashed #dbeafe;
        border-radius: 15px;
        padding: 40px 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f8fafc;
    }
    
    .file-upload-label:hover {
        border-color: var(--primary);
        background: #f0f9ff;
    }
    
    .preview-image {
        object-fit: cover;
    }
    
    .confirmation-summary {
        background: #f8fafc !important;
        border-left: 4px solid var(--primary);
    }
    
    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }
        
        .progress-steps {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }
        
        .progress-steps::before {
            display: none;
        }
        
        .step {
            flex-direction: row;
            gap: 12px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Multi-step form functionality
        const form = document.getElementById('registrationForm');
        const steps = document.querySelectorAll('.step-content');
        const progressSteps = document.querySelectorAll('.step');
        
        // Next button click handler
        document.querySelectorAll('.btn-next').forEach(button => {
            button.addEventListener('click', function() {
                const currentStep = this.closest('.step-content');
                const nextStepId = this.getAttribute('data-next');
                const nextStep = document.getElementById('step' + nextStepId);
                
                // Validate current step before proceeding
                if (validateStep(currentStep.id)) {
                    // Hide current step and show next
                    currentStep.classList.add('d-none');
                    nextStep.classList.remove('d-none');
                    
                    // Update progress indicator
                    updateProgress(nextStepId);
                    
                    // If moving to confirmation step, update summary
                    if (nextStepId === '3') {
                        updateSummary();
                    }
                }
            });
        });
        
        // Previous button click handler
        document.querySelectorAll('.btn-prev').forEach(button => {
            button.addEventListener('click', function() {
                const currentStep = this.closest('.step-content');
                const prevStepId = this.getAttribute('data-prev');
                const prevStep = document.getElementById('step' + prevStepId);
                
                // Hide current step and show previous
                currentStep.classList.add('d-none');
                prevStep.classList.remove('d-none');
                
                // Update progress indicator
                updateProgress(prevStepId);
            });
        });
        
        // Update progress indicator
        function updateProgress(stepId) {
            progressSteps.forEach(step => {
                step.classList.remove('active');
                if (parseInt(step.getAttribute('data-step')) <= parseInt(stepId)) {
                    step.classList.add('active');
                }
            });
        }
        
        // Validate step fields
        function validateStep(stepId) {
            let isValid = true;
            const currentStep = document.getElementById(stepId);
            
            // Check all required fields in this step
            const requiredFields = currentStep.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    
                    // Add error message if not exists
                    if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = 'Ce champ est obligatoire';
                        field.parentNode.appendChild(errorDiv);
                    }
                } else {
                    field.classList.remove('is-invalid');
                    
                    // Remove error message if exists
                    if (field.nextElementSibling && field.nextElementSibling.classList.contains('invalid-feedback')) {
                        field.nextElementSibling.remove();
                    }
                }
            });
            
            return isValid;
        }
        
        // File upload preview functionality
        const fileInput = document.getElementById('profile_picture');
        const filePreview = document.getElementById('file-preview');
        const previewImage = document.querySelector('.preview-image');
        const fileName = document.querySelector('.file-name');
        const removeFileBtn = document.getElementById('remove-file');
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Check file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Le fichier est trop volumineux. Taille maximale: 2MB');
                    this.value = '';
                    return;
                }
                
                // Check file type
                if (!file.type.match('image.*')) {
                    alert('Veuillez sélectionner une image valide');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    fileName.textContent = file.name;
                    filePreview.classList.remove('d-none');
                }
                
                reader.readAsDataURL(file);
            }
        });
        
        // Remove file button
        removeFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            filePreview.classList.add('d-none');
        });
        
        // Drag and drop for file upload
        const fileUploadLabel = document.querySelector('.file-upload-label');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileUploadLabel.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            fileUploadLabel.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            fileUploadLabel.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            fileUploadLabel.classList.add('highlight');
        }
        
        function unhighlight() {
            fileUploadLabel.classList.remove('highlight');
        }
        
        fileUploadLabel.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            
            // Trigger change event manually
            const event = new Event('change');
            fileInput.dispatchEvent(event);
        }
        
        // Update confirmation summary
        function updateSummary() {
            document.getElementById('summary-name').textContent = document.getElementById('name_respo').value || '-';
            document.getElementById('summary-email').textContent = document.getElementById('email').value || '-';
            document.getElementById('summary-contact').textContent = document.getElementById('contact').value || '-';
            document.getElementById('summary-commune').textContent = document.getElementById('commune').value || '-';
            
            // Update photo preview in summary
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = "Aperçu de la photo";
                    img.className = "preview-image rounded-circle shadow";
                    img.width = 100;
                    img.height = 100;
                    
                    document.getElementById('summary-photo').innerHTML = '';
                    document.getElementById('summary-photo').appendChild(img);
                }
                
                reader.readAsDataURL(file);
            } else {
                document.getElementById('summary-photo').innerHTML = '<span class="text-muted">Aucune photo sélectionnée</span>';
            }
        }
        
        // Form submission
        form.addEventListener('submit', function(e) {
            if (!document.getElementById('confirmationCheck').checked) {
                e.preventDefault();
                alert('Veuillez confirmer que les informations sont correctes avant de soumettre le formulaire.');
            }
        });
    });
</script>
@endsection