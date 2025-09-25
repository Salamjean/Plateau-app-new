@extends('dhl.layouts.template')
@section('content')
<style>
    :root {
        --primary-color: #d40511;
        --secondary-color: #f9cf03;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
    }
    
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        background-color: #f5f5f5;
    }
    
    .center-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex: 1;
        padding: 2rem 0;
    }
    
    .attribution-card {
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: none;
        overflow: hidden;
        width: 100%;
        max-width: 800px;
        margin: auto;
        background: white;
    }
    
    .attribution-header {
        background: linear-gradient(135deg, var(--primary-color), #d40511);
        color: white;
        padding: 1.5rem;
        font-size: 1.5rem;
        font-weight: 600;
        text-align: center;
        border-bottom: 4px solid var(--secondary-color);
    }
    
    .attribution-body {
        padding: 2.5rem;
        background-color: white;
    }
    
    .form-label {
        color: var(--primary-color);
        font-weight: 500;
        display: block;
        margin-bottom: 0.75rem;
    }
    
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        transition: all 0.3s;
        width: 100%;
    }
    
    .form-control:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 0.2rem rgba(249, 207, 3, 0.25);
    }
    
    .btn-attribuer {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s;
        text-transform: uppercase;
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
        display: block;
    }
    
    .btn-attribuer:hover {
        background-color: #d40511;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(6, 99, 78, 0.3);
    }
    
    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary-color);
    }
    
    .input-group {
        position: relative;
        margin-bottom: 0.5rem;
    }
    
    .illustration {
        max-width: 180px;
        margin: 0 auto 30px;
        display: block;
    }
    
    .help-link {
        color: var(--primary-color);
        transition: color 0.3s;
    }
    
    .help-link:hover {
        color: #d7343d;
        text-decoration: none;
    }
    
    /* Styles pour le scanner QR Code */
    .qr-scanner-container {
        margin: 20px 0;
        text-align: center;
    }
    
    .qr-toggle {
        background-color: var(--secondary-color);
        color: var(--dark-color);
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .qr-toggle:hover {
        background-color: #e6b800;
        transform: translateY(-2px);
    }
    
    .scanner-wrapper {
        position: relative;
        width: 100%;
        max-width: 400px;
        margin: 20px auto;
        display: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    #qr-video {
        width: 100%;
        height: 300px;
        object-fit: cover;
        background-color: #f0f0f0;
    }
    
    .scanner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }
    
    .scanner-frame {
        width: 200px;
        height: 200px;
        border: 3px solid var(--secondary-color);
        border-radius: 12px;
        box-shadow: 0 0 0 4000px rgba(0, 0, 0, 0.3);
    }
    
    .scanner-active .scanner-wrapper {
        display: block;
    }
    
    .scanner-active .manual-input {
        display: none;
    }
    
    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 20px 0;
        color: #6c757d;
    }
    
    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #dee2e6;
    }
    
    .divider::before {
        margin-right: .5em;
    }
    
    .divider::after {
        margin-left: .5em;
    }
    
    .scanner-actions {
        margin-top: 15px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    
    .btn-scanner {
        padding: 8px 15px;
        border-radius: 6px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-scanner-primary {
        background-color: var(--primary-color);
        color: white;
    }
    
    .btn-scanner-secondary {
        background-color: #6c757d;
        color: white;
    }
    
    .scanning-line {
        position: absolute;
        height: 2px;
        width: 180px;
        background-color: var(--secondary-color);
        top: 50%;
        animation: scan 2s infinite ease-in-out;
    }
    
    @keyframes scan {
        0% { top: 20%; }
        50% { top: 80%; }
        100% { top: 20%; }
    }
    
    #qr-canvas {
        display: none;
    }
    
    @media (max-width: 768px) {
        .attribution-body {
            padding: 1.75rem;
        }
        
        .center-container {
            padding: 1rem;
        }
        
        .illustration {
            max-width: 150px;
        }
        
        .scanner-wrapper {
            max-width: 300px;
        }
        
        #qr-video {
            height: 250px;
        }
        
        .scanner-frame {
            width: 180px;
            height: 180px;
        }
        
        .scanning-line {
            width: 160px;
        }
    }
</style>
<div class="center-container">
    <div class="attribution-card">
        <div class="attribution-header">
            <i class="fas fa-tasks mr-2"></i> Enregistrer les colis
        </div>
        
        <div class="attribution-body">
            <img src="{{ asset('assets/images/profiles/dhl.png') }}" alt="Illustration" class="illustration">
            
            <div class="qr-scanner-container">
                <button type="button" class="qr-toggle" id="toggleScanner">
                    <i class="fas fa-qrcode mr-2"></i> Scanner le QR Code
                </button>
                
                <div class="scanner-wrapper" id="scannerWrapper">
                    <div id="qr-video-container">
                        <video id="qr-video" playsinline></video>
                        <canvas id="qr-canvas" hidden></canvas>
                        <div class="scanner-overlay">
                            <div class="scanner-frame">
                                <div class="scanning-line"></div>
                            </div>
                        </div>
                    </div>
                    <div class="scanner-actions">
                        <button type="button" class="btn-scanner btn-scanner-secondary" id="switchCamera">
                            <i class="fas fa-sync-alt mr-1"></i> Changer caméra
                        </button>
                    </div>
                </div>
                
                <div class="divider">OU</div>
            </div>
            
            <form method="POST" action="{{ route('dhl.attribuer-demande') }}" class="manual-input" id="manualForm">
                @csrf

                <div class="mb-4">
                    <label for="reference" class="form-label">
                        <i class="fas fa-search mr-2"></i> Saisissez la référence ou le code de livraison
                    </label>
                    
                    <div class="input-group">
                        <input id="reference" type="text" 
                               class="form-control @error('reference') is-invalid @enderror" 
                               name="reference" 
                               value="{{ old('reference') }}" 
                               placeholder="Ex: LIV..56..231" 
                               required 
                               autocomplete="off">
                        <i class="fas fa-barcode search-icon"></i>
                        
                        @error('reference')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <small class="text-muted">
                        Le code de livraison se trouve sur l'enveloppe.
                    </small>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-attribuer" style="cursor: pointer">
                        <i class="fas fa-paper-plane mr-2"></i> Enregistrer un colis à ma dhl
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4 pt-3">
                <a href="#" class="help-link">
                    <i class="fas fa-question-circle mr-2"></i> Aide sur l'attribution des demandes
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<!-- Bibliothèque jsQR pour la détection de QR codes -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputRef = document.getElementById('reference');
        const searchIcon = inputRef.parentElement.querySelector('.search-icon');
        const toggleScannerBtn = document.getElementById('toggleScanner');
        const scannerWrapper = document.getElementById('scannerWrapper');
        const manualForm = document.getElementById('manualForm');
        const attributionCard = document.querySelector('.attribution-card');
        const switchCameraBtn = document.getElementById('switchCamera');
        
        const video = document.getElementById('qr-video');
        const canvas = document.getElementById('qr-canvas');
        const canvasContext = canvas.getContext('2d');
        
        let stream = null;
        let isScannerActive = false;
        let useFrontCamera = false;
        let scanningInterval = null;
        
        inputRef.addEventListener('focus', function() {
            searchIcon.style.color = '#f9cf03';
            this.parentElement.style.boxShadow = '0 0 0 2px rgba(249, 207, 3, 0.3)';
        });
        
        inputRef.addEventListener('blur', function() {
            searchIcon.style.color = '#d40511';
            this.parentElement.style.boxShadow = 'none';
        });

        // Toggle QR Scanner
        toggleScannerBtn.addEventListener('click', function() {
            isScannerActive = !isScannerActive;
            
            if (isScannerActive) {
                // Activer le scanner
                attributionCard.classList.add('scanner-active');
                toggleScannerBtn.innerHTML = '<i class="fas fa-keyboard mr-2"></i> Saisie manuelle';
                toggleScannerBtn.style.backgroundColor = '#d40511';
                toggleScannerBtn.style.color = 'white';
                
                // Initialiser le scanner
                initCamera();
            } else {
                // Désactiver le scanner
                stopCamera();
                attributionCard.classList.remove('scanner-active');
                toggleScannerBtn.innerHTML = '<i class="fas fa-qrcode mr-2"></i> Scanner le QR Code';
                toggleScannerBtn.style.backgroundColor = '#f9cf03';
                toggleScannerBtn.style.color = '#343a40';
            }
        });
        
        // Changer de caméra
        switchCameraBtn.addEventListener('click', function() {
            useFrontCamera = !useFrontCamera;
            stopCamera();
            initCamera();
        });
        
        // Initialiser la caméra
        function initCamera() {
            // Demander l'accès à la caméra
            navigator.mediaDevices.getUserMedia({
                video: { 
                    facingMode: useFrontCamera ? "user" : "environment",
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            })
            .then(function(mediaStream) {
                stream = mediaStream;
                video.srcObject = mediaStream;
                video.play();
                
                // Démarrer la détection de QR code
                startQRDetection();
            })
            .catch(function(error) {
                console.error("Erreur d'accès à la caméra:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur de caméra',
                    text: 'Impossible d\'accéder à la caméra. Veuillez vérifier les permissions.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
                
                // Revenir en mode manuel en cas d'erreur
                isScannerActive = false;
                attributionCard.classList.remove('scanner-active');
                toggleScannerBtn.innerHTML = '<i class="fas fa-qrcode mr-2"></i> Scanner le QR Code';
                toggleScannerBtn.style.backgroundColor = '#f9cf03';
                toggleScannerBtn.style.color = '#343a40';
            });
        }
        
        // Arrêter la caméra
        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
                stream = null;
            }
            
            // Arrêter l'intervalle de détection
            if (scanningInterval) {
                clearInterval(scanningInterval);
                scanningInterval = null;
            }
        }
        
        // Démarrer la détection de QR code
        function startQRDetection() {
            // Ajuster la taille du canvas à celle de la vidéo
            video.addEventListener('loadedmetadata', function() {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                
                // Démarrer la détection périodique
                scanningInterval = setInterval(scanQRCode, 100);
            });
        }
        
        // Scanner pour les QR codes
        function scanQRCode() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                // Dessiner l'image de la vidéo sur le canvas
                canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                // Obtenir les données d'image du canvas
                const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
                
                // Essayer de détecter un QR code
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: "dontInvert",
                });
                
                // Si un QR code est détecté
                if (code) {
                    // Arrêter la détection
                    clearInterval(scanningInterval);
                    scanningInterval = null;
                    
                    // Traiter le code détecté
                    processScannedCode(code.data);
                }
            }
        }
        
        // Traiter le code scanné
       function processScannedCode(code) {
            // Arrêter la caméra
            stopCamera();
            
            // Remplir le champ de référence avec le code scanné
            document.getElementById('reference').value = code;
            
            // Soumettre automatiquement le formulaire
            document.getElementById('manualForm').submit();
        }
        
        // Afficher les messages SweetAlert2 si nécessaire
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: '{{ session('success') }}',
                confirmButtonColor: '#d40511',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                html: `@foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach`,
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        @endif
    });
</script>
@endsection