@extends('home.layouts.tamplate')
@section('content')
<div class="extrait-page">
    <!-- Hero Section -->
    <section class="extrait-hero">
        <div class="extrait-container">
            <h1>Extraits de Naissance</h1>
            <p>Obtenez votre extrait de naissance en ligne - Service officiel de la Mairie du Plateau</p>
        </div>
    </section>

    <!-- Navigation Tabs -->
    <section class="extrait-tabs">
        <div class="extrait-container">
            <div class="tabs">
                <button class="tab-btn active" data-tab="simple-integral">Demande simple ou intégral</button>
            </div>
        </div>
    </section>

    <!-- Tab Content -->
    <div class="extrait-container">
        <!-- Avec Certificat Médical -->
        <div class="tab-content " id="avec-certificat">
            <div class="extrait-card">
                <div class="card-header">
                    <h2>Extrait avec certificat médical</h2>
                    <p>Pour les nouveau-nés avec certificat médical de naissance</p>
                </div>

                <div class="card-body">
                    <div class="info-alert">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Important :</strong> Vous devez disposer d'un certificat médical de naissance délivré par l'hôpital ou la maternité.
                        </div>
                    </div>

                    <div class="description">
                        <p>Pour faire une demande d'extrait de naissance pour votre enfant, un certificat médical de naissance est nécessaire. Ce document officiel est délivré par l'hôpital ou la maternité où a eu lieu l'accouchement.</p>
                    </div>

                    <div class="requirements">
                        <h3><i class="fas fa-file-alt"></i> Documents requis</h3>
                        <div class="requirements-grid">
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Nom et prénoms complets du nouveau-né</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Nom et prénom du père</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Date de naissance du père</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Copie de la pièce d'identité du père</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Certificat médical de naissance original</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Timbre fiscal (500 FCFA par copie)</span>
                            </div>
                        </div>
                    </div>

                    <div class="action-btn">
                        <a href="{{ route('user.dashboard') }}" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Démarrer la demande
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Simple ou Intégral -->
        <div class="tab-content active" id="simple-integral">
            <div class="extrait-card">
                <div class="card-header">
                    <h2>Extrait simple ou intégral</h2>
                    <p>Pour les personnes déjà enregistrées à l'état civil</p>
                </div>

                <div class="card-body">
                    <div class="info-alert">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>À savoir :</strong> Choisissez le type d'extrait en fonction de vos besoins administratifs.
                        </div>
                    </div>

                    <div class="description">
                        <p>Un acte de naissance peut donner lieu à la délivrance de 2 documents différents : <strong>la copie intégrale</strong> et <strong>l'extrait simple</strong>. La copie intégrale comporte des informations complètes sur la personne concernée (nom, prénoms, date et lieu de naissance), des informations sur ses parents et les mentions marginales lorsqu'elles existent. L'extrait simple contient uniquement les informations de base sur la personne concernée.</p>
                    </div>

                    <div class="requirements">
                        <h3><i class="fas fa-list-check"></i> Documents requis</h3>
                        <div class="requirements-grid">
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Nom et prénoms de la personne concernée</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Numéro de registre</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Date du registre</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Commune de naissance</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Copie de la pièce d'identité</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Timbre fiscal (500 FCFA par copie)</span>
                            </div>
                        </div>
                    </div>

                    <div class="action-btn">
                        <a href="{{ route('user.dashboard') }}" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Démarrer la demande
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="extrait-container">
            <h2>Questions Fréquentes</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h3>Quelle est la différence entre un extrait simple et une copie intégrale ?</h3>
                    <p>L'extrait simple contient les informations de base (nom, prénoms, date et lieu de naissance) tandis que la copie intégrale inclut également des informations sur les parents et les mentions marginales.</p>
                </div>
                <div class="faq-item">
                    <h3>Combien de temps faut-il pour obtenir un extrait de naissance ?</h3>
                    <p>Le traitement d'une demande en ligne prend généralement 24 à 48 heures. Vous recevrez une notification dès que votre document sera prêt.</p>
                </div>
                <div class="faq-item">
                    <h3>Où puis-je obtenir un timbre fiscal ?</h3>
                    <p>Les timbres fiscaux sont disponibles dans les bureaux de poste, les trésoreries et certaines administrations.</p>
                </div>
            </div>
        </div>
    </section>

<style>
    :root {
        --primary-color: #1977cc;
        --primary-light: #e8f2fc;
        --secondary-color: #f8f9fa;
        --text-color: #333;
        --light-text: #6c757d;
        --white: #ffffff;
        --success-color: #28a745;
        --info-color: #17a2b8;
        --border-radius: 12px;
        --box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }

    .extrait-page {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--white);
        color: var(--text-color);
        line-height: 1.6;
    }

    .extrait-container {
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Hero Section */
    .extrait-hero {
        background: linear-gradient(rgba(25, 119, 204, 0.5), rgba(25, 119, 204, 0.9)), url('{{asset('assets/assets/img/logo plateau.png')}}');
        background-size: cover;
        background-position: center;
        color: var(--white);
        padding: 80px 0;
        text-align: center;
    }

    .extrait-hero h1 {
        font-size: 2.8rem;
        margin-bottom: 20px;
        font-weight: 700;
        color: white;
    }

    .extrait-hero p {
        font-size: 1.3rem;
        max-width: 700px;
        margin: 0 auto;
        opacity: 0.9;
    }

    /* Tabs Navigation */
    .extrait-tabs {
        background-color: var(--secondary-color);
        padding: 30px 0;
    }

    .tabs {
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    .tab-btn {
        background: var(--white);
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        padding: 15px 30px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        font-size: 1.1rem;
        transition: var(--transition);
    }

    .tab-btn:hover, .tab-btn.active {
        background: var(--primary-color);
        color: var(--white);
        transform: translateY(-2px);
    }

    /* Tab Content */
    .tab-content {
        display: none;
        padding: 50px 0;
    }

    .tab-content.active {
        display: block;
    }

    /* Card Styles */
    .extrait-card {
        background: var(--white);
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
    }

    .extrait-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary-color), #125daa);
        color: var(--white);
        padding: 30px;
        text-align: center;
    }

    .card-header h2 {
        font-size: 2rem;
        color: white;
        margin-bottom: 10px;
    }

    .card-header p {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .card-body {
        padding: 30px;
    }

    /* Info Alert */
    .info-alert {
        display: flex;
        align-items: flex-start;
        background-color: var(--primary-light);
        border-left: 4px solid var(--info-color);
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .info-alert i {
        color: var(--info-color);
        font-size: 1.5rem;
        margin-right: 15px;
        margin-top: 2px;
    }

    .info-alert strong {
        color: var(--info-color);
    }

    /* Description */
    .description {
        margin-bottom: 30px;
    }

    .description p {
        font-size: 1.1rem;
        line-height: 1.7;
        color: var(--text-color);
    }

    /* Requirements */
    .requirements {
        margin-bottom: 40px;
    }

    .requirements h3 {
        color: var(--primary-color);
        font-size: 1.5rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .requirements h3 i {
        margin-right: 10px;
    }

    .requirements-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
    }

    .requirement-item {
        display: flex;
        align-items: center;
        padding: 15px;
        background-color: var(--secondary-color);
        border-radius: 8px;
        transition: var(--transition);
    }

    .requirement-item:hover {
        background-color: var(--primary-light);
        transform: translateX(5px);
    }

    .requirement-icon {
        width: 30px;
        height: 30px;
        background-color: var(--success-color);
        color: var(--white);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }

    /* Action Button */
    .action-btn {
        text-align: center;
    }

    .btn-primary {
        display: inline-block;
        background-color: var(--primary-color);
        color: var(--white);
        padding: 16px 40px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.1rem;
        transition: var(--transition);
    }

    .btn-primary:hover {
        background-color: #125daa;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(25, 119, 204, 0.3);
    }

    .btn-primary i {
        margin-right: 10px;
    }

    /* FAQ Section */
    .faq-section {
        background-color: var(--secondary-color);
        padding: 80px 0;
    }

    .faq-section h2 {
        text-align: center;
        margin-bottom: 50px;
        color: var(--primary-color);
        font-size: 2.2rem;
    }

    .faq-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }

    .faq-item {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 25px;
        box-shadow: var(--box-shadow);
    }

    .faq-item h3 {
        color: var(--primary-color);
        margin-bottom: 15px;
        font-size: 1.2rem;
    }

    /* Contact Section */
    .contact-section {
        padding: 80px 0;
    }

    .contact-card {
        background: linear-gradient(135deg, var(--primary-color), #125daa);
        color: var(--white);
        padding: 60px 50px;
        border-radius: var(--border-radius);
        text-align: center;
    }

    .contact-card h2 {
        margin-bottom: 15px;
        font-size: 2rem;
    }

    .contact-card > p {
        margin-bottom: 50px;
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .contact-methods {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
    }

    .contact-method {
        padding: 20px;
    }

    .contact-method i {
        font-size: 2.5rem;
        margin-bottom: 20px;
        color: var(--white);
        opacity: 0.9;
    }

    .contact-method h3 {
        margin-bottom: 15px;
        font-size: 1.3rem;
    }

    .contact-method p {
        opacity: 0.9;
        line-height: 1.6;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .tabs {
            flex-direction: column;
            align-items: center;
        }
        
        .tab-btn {
            width: 100%;
            max-width: 300px;
            text-align: center;
        }
        
        .requirements-grid {
            grid-template-columns: 1fr;
        }
        
        .faq-grid {
            grid-template-columns: 1fr;
        }
        
        .contact-methods {
            grid-template-columns: 1fr;
        }
        
        .contact-card {
            padding: 40px 20px;
        }
        
        .extrait-hero h1 {
            font-size: 2.2rem;
        }
        
        .extrait-hero p {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 576px) {
        .card-header {
            padding: 20px;
        }
        
        .card-header h2 {
            font-size: 1.6rem;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .info-alert {
            flex-direction: column;
            text-align: center;
        }
        
        .info-alert i {
            margin-right: 0;
            margin-bottom: 10px;
        }
        
        .btn-primary {
            width: 100%;
            text-align: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class from all buttons and contents
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked button
                btn.classList.add('active');
                
                // Show corresponding content
                const tabId = btn.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
    });
</script>
@endsection