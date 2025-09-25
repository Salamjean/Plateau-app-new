@extends('home.layouts.tamplate')
@section('content')
<div class="services-page">
    <!-- Hero Section -->
    <section class="services-hero">
        <div class="services-container">
            <h1>Services de la Mairie du Plateau</h1>
            <p>Découvrez tous les services administratifs offerts aux citoyens et aux entreprises de la commune du Plateau</p>
        </div>
    </section>

    <!-- Services Navigation -->
    <section class="services-nav">
        <div class="services-container">
            <div class="nav-tabs">
                <button class="nav-tab active" data-category="all">Tous les services</button>
                <button class="nav-tab" data-category="citoyens">Services aux citoyens</button>
                <button class="nav-tab" data-category="entreprises">Services aux entreprises</button>
                <button class="nav-tab" data-category="urbanisme">Urbanisme</button>
                <button class="nav-tab" data-category="etat-civil">État civil</button>
            </div>
        </div>
    </section>

    <!-- Services Grid -->
    <section class="services-grid-section">
        <div class="services-container">
            <h2 class="section-title">Nos Services</h2>
            
            <div class="services-grid">
                <!-- Service Card 1 -->
                <div class="service-card" data-category="etat-civil">
                    <div class="service-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3>État Civil</h3>
                    <p>Délivrance d'actes de naissance, de mariage, de décès et autres documents d'état civil.</p>
                    <ul class="service-features">
                        <li>Acte de naissance</li>
                        <li>Acte de mariage</li>
                        <li>Acte de décès</li>
                        <li>Livret de famille</li>
                    </ul>
                    <a href="#" class="service-btn">En savoir plus</a>
                </div>

                <!-- Service Card 2 -->
                <div class="service-card" data-category="urbanisme">
                    <div class="service-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Urbanisme et Permis de Construire</h3>
                    <p>Demandes de permis de construire, certificats d'urbanisme et autorisations diverses.</p>
                    <ul class="service-features">
                        <li>Permis de construire</li>
                        <li>Certificat d'urbanisme</li>
                        <li>Autorisation de travaux</li>
                        <li>Lotissement</li>
                    </ul>
                    <a href="#" class="service-btn">En savoir plus</a>
                </div>

                <!-- Service Card 3 -->
                <div class="service-card" data-category="citoyens">
                    <div class="service-icon">
                        <i class="fas fa-passport"></i>
                    </div>
                    <h3>Pièces d'Identité</h3>
                    <p>Demande et renouvellement de cartes nationales d'identité et autres documents officiels.</p>
                    <ul class="service-features">
                        <li>Carte nationale d'identité</li>
                        <li>Passeport</li>
                        <li>Attestation d'identité</li>
                        <li>Certificat de nationalité</li>
                    </ul>
                    <a href="#" class="service-btn">En savoir plus</a>
                </div>

                <!-- Service Card 4 -->
                <div class="service-card" data-category="entreprises">
                    <div class="service-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3>Activités Commerciales</h3>
                    <p>Immatriculation des entreprises, licences commerciales et autorisations d'exploitation.</p>
                    <ul class="service-features">
                        <li>Immatriculation au registre du commerce</li>
                        <li>Licence commerciale</li>
                        <li>Autorisation d'exploitation</li>
                        <li>Patente</li>
                    </ul>
                    <a href="#" class="service-btn">En savoir plus</a>
                </div>

                <!-- Service Card 5 -->
                <div class="service-card" data-category="citoyens">
                    <div class="service-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3>Logement Social</h3>
                    <p>Programmes d'accès au logement social et accompagnement des demandeurs.</p>
                    <ul class="service-features">
                        <li>Demande de logement social</li>
                        <li>Programmes d'accession</li>
                        <li>Aides au logement</li>
                        <li>Rénovation urbaine</li>
                    </ul>
                    <a href="#" class="service-btn">En savoir plus</a>
                </div>

                <!-- Service Card 6 -->
                <div class="service-card" data-category="citoyens">
                    <div class="service-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3>Action Sociale</h3>
                    <p>Aides sociales, accompagnement des personnes vulnérables et programmes solidaires.</p>
                    <ul class="service-features">
                        <li>Aides sociales</li>
                        <li>Accompagnement des personnes âgées</li>
                        <li>Soutien aux familles</li>
                        <li>Insertion professionnelle</li>
                    </ul>
                    <a href="#" class="service-btn">En savoir plus</a>
                </div>

                <!-- Service Card 7 -->
                <div class="service-card" data-category="urbanisme">
                    <div class="service-icon">
                        <i class="fas fa-tree"></i>
                    </div>
                    <h3>Environnement et Propreté</h3>
                    <p>Services de collecte des déchets, entretien des espaces verts et protection de l'environnement.</p>
                    <ul class="service-features">
                        <li>Collecte des déchets</li>
                        <li>Entretien des espaces verts</li>
                        <li>Protection de l'environnement</li>
                        <li>Sensibilisation écologique</li>
                    </ul>
                    <a href="#" class="service-btn">En savoir plus</a>
                </div>

                <!-- Service Card 8 -->
                <div class="service-card" data-category="citoyens">
                    <div class="service-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Éducation et Jeunesse</h3>
                    <p>Services dédiés à l'éducation, aux activités périscolaires et à l'épanouissement de la jeunesse.</p>
                    <ul class="service-features">
                        <li>Inscription scolaire</li>
                        <li>Activités périscolaires</li>
                        <li>Accueil de loisirs</li>
                        <li>Bourses et aides</li>
                    </ul>
                    <a href="#" class="service-btn">En savoir plus</a>
                </div>
            </div>
        </div>
    </section>

    <!-- How-to Section -->
    <section class="how-to-section">
        <div class="services-container">
            <h2 class="section-title">Comment accéder à nos services</h2>
            <div class="how-to-steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Préparez vos documents</h3>
                    <p>Rassemblez les pièces justificatives nécessaires selon le service demandé.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Prenez rendez-vous</h3>
                    <p>Utilisez notre plateforme en ligne ou contactez-nous par téléphone.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Rendez-vous en mairie</h3>
                    <p>Présentez-vous avec vos documents aux guichets dédiés.</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Recevez votre dossier</h3>
                    <p>Votre demande est traitée dans les délais impartis.</p>
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
        --border-radius: 8px;
        --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .services-page {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--white);
        color: var(--text-color);
        line-height: 1.6;
    }
    
    .services-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    /* Hero Section */
    .services-hero {
        background: linear-gradient(rgba(25, 119, 204, 0.5), rgba(25, 119, 204, 0.9)), url('{{asset('assets/assets/img/A.jpg')}}');
        background-size: cover;
        background-position: center;
        color: var(--white);
        padding: 80px 0;
        text-align: center;
    }
    
    .services-hero h1 {
        color: white;
        font-size: 2.5rem;
        margin-bottom: 20px;
    }
    
    .services-hero p {
        font-size: 1.2rem;
        max-width: 700px;
        margin: 0 auto;
    }
    
    /* Services Navigation */
    .services-nav {
        background-color: var(--secondary-color);
        padding: 30px 0;
    }
    
    .nav-tabs {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
    }
    
    .nav-tab {
        background: var(--white);
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        padding: 10px 20px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .nav-tab:hover, .nav-tab.active {
        background: var(--primary-color);
        color: var(--white);
    }
    
    /* Section Title */
    .section-title {
        text-align: center;
        margin-bottom: 50px;
        color: var(--primary-color);
        position: relative;
    }
    
    .section-title:after {
        content: '';
        display: block;
        width: 70px;
        height: 3px;
        background: var(--primary-color);
        margin: 15px auto;
    }
    
    /* Services Grid */
    .services-grid-section {
        padding: 80px 0;
    }
    
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
    }
    
    .service-card {
        background: var(--white);
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        transition: transform 0.3s;
        padding: 30px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .service-card:hover {
        transform: translateY(-10px);
    }
    
    .service-icon {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 20px;
        text-align: center;
    }
    
    .service-card h3 {
        margin-bottom: 15px;
        color: var(--primary-color);
        text-align: center;
    }
    
    .service-card p {
        margin-bottom: 20px;
        flex-grow: 1;
    }
    
    .service-features {
        margin-bottom: 25px;
        flex-grow: 1;
    }
    
    .service-features li {
        margin-bottom: 8px;
        position: relative;
        padding-left: 20px;
    }
    
    .service-features li:before {
        content: "•";
        color: var(--primary-color);
        font-weight: bold;
        position: absolute;
        left: 0;
    }
    
    .service-btn {
        display: block;
        text-align: center;
        background: var(--primary-color);
        color: var(--white);
        padding: 12px 20px;
        border-radius: var(--border-radius);
        text-decoration: none;
        font-weight: 600;
        transition: background 0.3s;
    }
    
    .service-btn:hover {
        background: #125daa;
    }
    
    /* How-to Section */
    .how-to-section {
        background-color: var(--secondary-color);
        padding: 80px 0;
    }
    
    .how-to-steps {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }
    
    .step {
        text-align: center;
        padding: 30px;
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
    }
    
    .step-number {
        width: 50px;
        height: 50px;
        background: var(--primary-color);
        color: var(--white);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0 auto 20px;
    }
    
    .step h3 {
        margin-bottom: 15px;
        color: var(--primary-color);
    }
    
    /* Contact Section */
    .services-contact {
        padding: 80px 0;
    }
    
    .contact-card {
        background: linear-gradient(to right, var(--primary-color), #125daa);
        color: var(--white);
        padding: 50px;
        border-radius: var(--border-radius);
        text-align: center;
    }
    
    .contact-card h2 {
        margin-bottom: 15px;
    }
    
    .contact-card > p {
        margin-bottom: 40px;
        font-size: 1.1rem;
    }
    
    .contact-methods {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }
    
    .contact-method {
        padding: 20px;
    }
    
    .contact-method i {
        font-size: 2.5rem;
        margin-bottom: 15px;
        color: var(--white);
    }
    
    .contact-method h3 {
        margin-bottom: 10px;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .nav-tabs {
            flex-direction: column;
            align-items: center;
        }
        
        .nav-tab {
            width: 100%;
            max-width: 250px;
            text-align: center;
        }
        
        .services-grid {
            grid-template-columns: 1fr;
        }
        
        .how-to-steps {
            grid-template-columns: 1fr;
        }
        
        .contact-methods {
            grid-template-columns: 1fr;
        }
        
        .contact-card {
            padding: 30px 20px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.nav-tab');
        const services = document.querySelectorAll('.service-card');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                tab.classList.add('active');
                
                const category = tab.getAttribute('data-category');
                
                // Show/hide services based on category
                services.forEach(service => {
                    if (category === 'all' || service.getAttribute('data-category') === category) {
                        service.style.display = 'flex';
                    } else {
                        service.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endsection