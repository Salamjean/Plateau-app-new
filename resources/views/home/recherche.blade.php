@extends('home.layouts.tamplate')
@section('content')

<div class="container py-5" >
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-white text-center py-4">
                    <div class="icon-container mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#1977cc" class="bi bi-clipboard-check" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                            <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                            <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                        </svg>
                    </div>
                    <h1 class="h3 mb-2 fw-bold" style="color: #1977cc">Suivi de votre demande</h1>
                    <p class="mb-0 text-muted">Vérifiez l'état d'avancement de votre demande en temps réel</p>
                </div>
                
                <div class="card-body px-4 px-md-5 py-4">
                    <form method="POST" action="{{ route('recherche.demande') }}" class="needs-validation" novalidate>
                        @csrf

                        <div class="mb-4">
                            <label for="reference_naissance" class="form-label fw-medium text-dark mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#1977cc" class="bi bi-search me-2" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                                Numéro de référence
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg py-3 px-4" 
                                   id="reference_naissance" 
                                   name="reference_naissance" 
                                   placeholder="Ex: REF12345678"
                                   required
                                   style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s;">
                            <div class="invalid-feedback">
                                Veuillez saisir votre référence.
                            </div>
                            <small class="text-muted mt-2 d-block">
                                Cette référence vous a été communiquée lors du dépôt de votre demande.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-lg w-100 py-3 rounded-3 shadow-sm fw-medium text-white border-0" style="background-color: #1977cc">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search me-2" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                            Vérifier l'état
                        </button>
                    </form>

                    <div id="resultat-recherche" class="mt-4">
                        @if(isset($etatDemande))
                            <div class="text-center">
                                <h4 class="mb-4" style="color: #1977cc">Résultat de votre recherche</h4>
                                @if($etatDemande)
                                    <div class="alert border-0 rounded-3 p-4 shadow-sm" style="background-color: rgba(25, 119, 204, 0.1);" role="alert">
                                        <div class="d-flex align-items-center justify-content-center mb-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#1977cc" class="bi bi-check-circle-fill me-3" viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                            </svg>
                                            <h5 class="mb-0" style="color: #1977cc">Référence: {{ request('reference_naissance') }}</h5>
                                        </div>
                                        <hr>
                                        <p class="fw-medium mb-1">État actuel:</p>
                                        <p class="h4 fw-bold mb-3" style="color: #1977cc">{{ $etatDemande }}</p>
                                        <div class="progress mt-3" style="height: 10px; border-radius: 10px;">
                                            <div class="progress-bar" role="progressbar" style="width: 75%; background-color: #1977cc;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert border-0 rounded-3 p-4 shadow-sm" style="background-color: rgba(255, 193, 7, 0.1);" role="alert">
                                        <div class="d-flex align-items-center justify-content-center mb-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#fd7e14" class="bi bi-exclamation-triangle-fill me-3" viewBox="0 0 16 16">
                                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                            </svg>
                                            <h5 class="mb-0" style="color: #fd7e14">Référence non trouvée</h5>
                                        </div>
                                        <hr>
                                        <p class="fw-medium">
                                            Aucune demande trouvée pour <strong>"{{ request('reference_naissance') }}"</strong>.
                                            <br>Veuillez vérifier votre référence ou contacter notre support.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
        border: none;
        transition: transform 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .form-control {
        border: 2px solid #e9ecef;
    }
    .form-control:focus {
        border-color: #1977cc;
        box-shadow: 0 0 0 0.25rem rgba(25, 119, 204, 0.25);
    }
    .btn {
        transition: all 0.3s;
    }
    .btn:hover {
        background-color: #1565b8 !important;
        transform: translateY(-2px);
    }
    .icon-container {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background-color: rgba(25, 119, 204, 0.1);
    }
    .progress {
        border-radius: 10px;
    }
    .invalid-feedback {
        font-size: 0.9rem;
    }
</style>

<script>
    // Validation du formulaire côté client
    (function() {
        'use strict'
        
        const forms = document.querySelectorAll('.needs-validation')
        
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>

@endsection