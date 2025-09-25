@extends('home.layouts.tamplate')
@section('content')
<div class="rendezvous-page">
    <!-- Hero Section -->
    <section class="rendezvous-hero">
        <div class="rendezvous-container">
            <h1>Rendez-vous pour Mariage</h1>
            <p>Planifiez votre union matrimoniale à la Mairie du Plateau - Service officiel</p>
        </div>
    </section>

    <!-- Process Steps -->
    <section class="process-steps">
        <div class="rendezvous-container">
            <h2>Procédure de prise de rendez-vous</h2>
            <div class="steps-container">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Préparation des documents</h3>
                        <p>Rassemblez tous les documents nécessaires avant de prendre rendez-vous</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Choix de la date</h3>
                        <p>Sélectionnez une date disponible qui convient aux deux futurs époux</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Confirmation</h3>
                        <p>Recevez une confirmation par email avec les détails du rendez-vous</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>Présentation en mairie</h3>
                        <p>Présentez-vous à la mairie avec tous les documents requis</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Documents Required -->
    <section class="documents-section">
        <div class="rendezvous-container">
            <div class="rendezvous-card">
                <div class="card-header">
                    <h2>Documents requis pour le mariage</h2>
                    <p>Liste des pièces à fournir pour la célébration du mariage</p>
                </div>

                <div class="card-body">
                    <div class="info-alert">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Important :</strong> Tous les documents doivent être originaux et datés de moins de 3 mois.
                        </div>
                    </div>

                    <div class="documents-grid">
                        <div class="document-category">
                            <h3><i class="fas fa-user"></i> Pièces d'identité</h3>
                            <ul class="requirements-list">
                                <li>Carte nationale d'identité ou passeport en cours de validité</li>
                                <li>Justificatif de domicile (facture d'eau, d'électricité ou de téléphone)</li>
                                <li>Photographies d'identité récentes (4 pour chaque futur époux)</li>
                            </ul>
                        </div>

                        <div class="document-category">
                            <h3><i class="fas fa-file-contract"></i> Documents d'état civil</h3>
                            <ul class="requirements-list">
                                <li>Acte de naissance (copie intégrale) de moins de 3 mois</li>
                                <li>Certificat de coutume pour les ressortissants étrangers</li>
                                <li>Document de loi applicable pour les binationaux</li>
                            </ul>
                        </div>

                        <div class="document-category">
                            <h3><i class="fas fa-users"></i> Documents concernant les témoins</h3>
                            <ul class="requirements-list">
                                <li>Pièce d'identité des témoins (2 à 4 témoins requis)</li>
                                <li>Justificatif de domicile des témoins</li>
                                <li>Photocopie des documents d'identité des témoins</li>
                            </ul>
                        </div>

                        <div class="document-category">
                            <h3><i class="fas fa-file-medical"></i> Documents médicaux</h3>
                            <ul class="requirements-list">
                                <li>Certificat prénuptial délivré par un médecin</li>
                                <li>Attestation de consultation prémaritale</li>
                            </ul>
                        </div>
                    </div>

                    <div class="action-btn">
                        <a href="{{ route('user.dashboard') }}" class="btn-primary">
                            <i class="fas fa-calendar-check"></i> Prendre rendez-vous
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Calendar & Availability -->
    <section class="availability-section">
        <div class="rendezvous-container">
            <div class="rendezvous-card">
                <div class="card-header">
                    <h2>Disponibilités et créneaux horaires</h2>
                    <p>Planification des cérémonies de mariage à la Mairie du Plateau</p>
                </div>

                <div class="card-body">
                    <div class="availability-info">
                        <div class="availability-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <h4>Jours de célébration</h4>
                                <p>Du mardi au samedi, de 9h à 16h</p>
                            </div>
                        </div>
                        <div class="availability-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <h4>Délai de réservation</h4>
                                <p>Réservation minimum 1 mois à l'avance</p>
                            </div>
                        </div>
                        <div class="availability-item">
                            <i class="fas fa-hourglass-half"></i>
                            <div>
                                <h4>Durée de la cérémonie</h4>
                                <p>30 minutes par cérémonie</p>
                            </div>
                        </div>
                    </div>

                    <div class="calendar-notes">
                        <h3><i class="fas fa-exclamation-circle"></i> Notes importantes</h3>
                        <ul>
                            <li>Les mariages ne sont pas célébrés les dimanches, lundis et jours fériés</li>
                            <li>La réservation n'est confirmée qu'après vérification complète des documents</li>
                            <li>En cas d'empêchement, merci de prévenir au moins 72h à l'avance</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="rendezvous-container">
            <h2>Questions Fréquentes</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h3>Quel est le délai pour obtenir un rendez-vous ?</h3>
                    <p>Le délai moyen est de 4 à 6 semaines selon la période de l'année. Nous recommandons de prendre rendez-vous au moins 2 mois à l'avance.</p>
                </div>
                <div class="faq-item">
                    <h3>Peut-on annuler ou reporter un rendez-vous ?</h3>
                    <p>Oui, vous pouvez annuler ou reporter votre rendez-vous jusqu'à 72 heures à l'avance sans frais. Passé ce délai, des frais d'annulation peuvent s'appliquer.</p>
                </div>
                <div class="faq-item">
                    <h3>Faut-il que les deux futurs époux soient présents ?</h3>
                    <p>Pour la prise de rendez-vous, une seule personne peut effectuer la démarche. Cependant, les deux futurs époux doivent être présents le jour de la cérémonie.</p>
                </div>
            </div>
        </div>
    </section>
</div>

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

    .rendezvous-page {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--white);
        color: var(--text-color);
        line-height: 1.6;
    }

    .rendezvous-container {
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Hero Section */
    .rendezvous-hero {
        background: linear-gradient(rgba(25, 119, 204, 0.5), rgba(25, 119, 204, 0.9)), url('{{asset('assets/assets/img/logo plateau.png')}}');
        background-size: cover;
        background-position: center;
        color: var(--white);
        padding: 80px 0;
        text-align: center;
    }

    .rendezvous-hero h1 {
        font-size: 2.8rem;
        margin-bottom: 20px;
        font-weight: 700;
        color: white;
    }

    .rendezvous-hero p {
        font-size: 1.3rem;
        max-width: 700px;
        margin: 0 auto;
        opacity: 0.9;
    }

    /* Process Steps */
    .process-steps {
        background-color: var(--secondary-color);
        padding: 60px 0;
    }

    .process-steps h2 {
        text-align: center;
        margin-bottom: 50px;
        color: var(--primary-color);
        font-size: 2.2rem;
    }

    .steps-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 30px;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .step-number {
        width: 60px;
        height: 60px;
        background-color: var(--primary-color);
        color: var(--white);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .step-content h3 {
        color: var(--primary-color);
        margin-bottom: 15px;
        font-size: 1.4rem;
    }

    /* Documents Section */
    .documents-section {
        padding: 60px 0;
    }

    .rendezvous-card {
        background: var(--white);
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
    }

    .rendezvous-card:hover {
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

    /* Documents Grid */
    .documents-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
    }

    .document-category {
        background-color: var(--secondary-color);
        padding: 25px;
        border-radius: var(--border-radius);
        transition: var(--transition);
    }

    .document-category:hover {
        background-color: var(--primary-light);
        transform: translateY(-3px);
    }

    .document-category h3 {
        color: var(--primary-color);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        font-size: 1.3rem;
    }

    .document-category h3 i {
        margin-right: 10px;
    }

    .requirements-list {
        list-style: none;
        padding: 0;
    }

    .requirements-list li {
        padding: 8px 0;
        border-bottom: 1px solid #eee;
        position: relative;
        padding-left: 25px;
    }

    .requirements-list li:before {
        content: "•";
        color: var(--primary-color);
        font-weight: bold;
        position: absolute;
        left: 0;
    }

    .requirements-list li:last-child {
        border-bottom: none;
    }

    /* Availability Section */
    .availability-section {
        padding: 60px 0;
        background-color: var(--secondary-color);
    }

    .availability-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
    }

    .availability-item {
        display: flex;
        align-items: center;
        background-color: var(--white);
        padding: 20px;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
    }

    .availability-item i {
        font-size: 2rem;
        color: var(--primary-color);
        margin-right: 20px;
    }

    .availability-item h4 {
        margin-bottom: 5px;
        color: var(--primary-color);
    }

    .calendar-notes {
        background-color: var(--primary-light);
        padding: 25px;
        border-radius: var(--border-radius);
        border-left: 4px solid var(--primary-color);
    }

    .calendar-notes h3 {
        color: var(--primary-color);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }

    .calendar-notes h3 i {
        margin-right: 10px;
    }

    .calendar-notes ul {
        list-style: none;
        padding: 0;
    }

    .calendar-notes li {
        padding: 8px 0;
        padding-left: 25px;
        position: relative;
    }

    .calendar-notes li:before {
        content: "•";
        color: var(--primary-color);
        font-weight: bold;
        position: absolute;
        left: 0;
    }

    /* Action Button */
    .action-btn {
        text-align: center;
        margin-top: 30px;
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
        background-color: var(--secondary-color);
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
        .steps-container {
            grid-template-columns: 1fr;
        }
        
        .documents-grid {
            grid-template-columns: 1fr;
        }
        
        .availability-info {
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
        
        .rendezvous-hero h1 {
            font-size: 2.2rem;
        }
        
        .rendezvous-hero p {
            font-size: 1.1rem;
        }
        
        .availability-item {
            flex-direction: column;
            text-align: center;
        }
        
        .availability-item i {
            margin-right: 0;
            margin-bottom: 15px;
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
        
        .document-category {
            padding: 15px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation pour les étapes du processus
        const steps = document.querySelectorAll('.step');
        steps.forEach((step, index) => {
            setTimeout(() => {
                step.style.opacity = 1;
                step.style.transform = 'translateY(0)';
            }, index * 200);
        });

        // Animation pour les catégories de documents
        const documentCategories = document.querySelectorAll('.document-category');
        documentCategories.forEach((category, index) => {
            setTimeout(() => {
                category.style.opacity = 1;
                category.style.transform = 'translateY(0)';
            }, index * 150);
        });

        // Initial styles for animation
        steps.forEach(step => {
            step.style.opacity = 0;
            step.style.transform = 'translateY(20px)';
            step.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        });

        documentCategories.forEach(category => {
            category.style.opacity = 0;
            category.style.transform = 'translateY(20px)';
            category.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        });
    });
</script>
@endsection