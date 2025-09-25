@extends('home.layouts.tamplate')
@section('content')
<div class="departments-page">
    <!-- Hero Section -->
    <section class="departments-hero">
        <div class="departments-container">
            <h1>Départements de la Mairie du Plateau</h1>
            <p>Découvrez l'organisation administrative et les différents services de votre mairie</p>
        </div>
    </section>

    <!-- Department Navigation -->
    <section class="departments-nav">
        <div class="departments-container">
            <div class="nav-tabs">
                <button class="nav-tab active" data-category="all">Tous les départements</button>
                <button class="nav-tab" data-category="administratif">Services administratifs</button>
                <button class="nav-tab" data-category="technique">Services techniques</button>
                <button class="nav-tab" data-category="social">Services sociaux</button>
                <button class="nav-tab" data-category="culturel">Services culturels</button>
            </div>
        </div>
    </section>

    <!-- Departments Grid -->
    <section class="departments-grid-section">
        <div class="departments-container">
            <h2 class="section-title">Nos Départements</h2>
            
            <div class="departments-grid">
                <!-- Department Card 1 -->
                <div class="department-card" data-category="administratif">
                    <div class="department-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3>État Civil</h3>
                    <p>Service chargé de l'enregistrement des naissances, mariages, décès et de la délivrance des documents officiels.</p>
                    <div class="department-info">
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <span>Responsable: Mme. Koné Aminata</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span>+225 20 30 40 50</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>Lun-Ven: 7h30-16h30</span>
                        </div>
                    </div>
                    <a href="#" class="department-btn">Contacter ce service</a>
                </div>

                <!-- Department Card 2 -->
                <div class="department-card" data-category="administratif">
                    <div class="department-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3>Affaires Juridiques</h3>
                    <p>Service conseil en droit administratif, contentieux et gestion des marchés publics de la mairie.</p>
                    <div class="department-info">
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <span>Responsable: M. Kouassi Jean</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span>+225 20 30 40 51</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>Lun-Ven: 8h-17h</span>
                        </div>
                    </div>
                    <a href="#" class="department-btn">Contacter ce service</a>
                </div>

                <!-- Department Card 3 -->
                <div class="department-card" data-category="technique">
                    <div class="department-icon">
                        <i class="fas fa-hard-hat"></i>
                    </div>
                    <h3>Services Techniques</h3>
                    <p>Entretien de la voirie, gestion des bâtiments municipaux et infrastructures urbaines.</p>
                    <div class="department-info">
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <span>Responsable: M. Diallo Ibrahim</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span>+225 20 30 40 52</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>Lun-Sam: 7h-15h</span>
                        </div>
                    </div>
                    <a href="#" class="department-btn">Contacter ce service</a>
                </div>

                <!-- Department Card 4 -->
                <div class="department-card" data-category="social">
                    <div class="department-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3>Action Sociale</h3>
                    <p>Aide aux personnes vulnérables, programmes sociaux et soutien aux familles en difficulté.</p>
                    <div class="department-info">
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <span>Responsable: Mme. Bamba Aïcha</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span>+225 20 30 40 53</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>Lun-Ven: 8h-16h</span>
                        </div>
                    </div>
                    <a href="#" class="department-btn">Contacter ce service</a>
                </div>

                <!-- Department Card 5 -->
                <div class="department-card" data-category="culturel">
                    <div class="department-icon">
                        <i class="fas fa-theater-masks"></i>
                    </div>
                    <h3>Affaires Culturelles</h3>
                    <p>Organisation d'événements culturels, préservation du patrimoine et soutien aux artistes locaux.</p>
                    <div class="department-info">
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <span>Responsable: M. Soro Moussa</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span>+225 20 30 40 54</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>Lun-Ven: 9h-17h</span>
                        </div>
                    </div>
                    <a href="#" class="department-btn">Contacter ce service</a>
                </div>

                <!-- Department Card 6 -->
                <div class="department-card" data-category="technique">
                    <div class="department-icon">
                        <i class="fas fa-tree"></i>
                    </div>
                    <h3>Environnement</h3>
                    <p>Gestion des déchets, entretien des espaces verts et politiques environnementales.</p>
                    <div class="department-info">
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <span>Responsable: Mme. Kamara Fatou</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span>+225 20 30 40 55</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>Lun-Ven: 7h30-15h30</span>
                        </div>
                    </div>
                    <a href="#" class="department-btn">Contacter ce service</a>
                </div>

                <!-- Department Card 7 -->
                <div class="department-card" data-category="administratif">
                    <div class="department-icon">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <h3>Finances</h3>
                    <p>Gestion budgétaire, recouvrement des impôts locaux et finances de la commune.</p>
                    <div class="department-info">
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <span>Responsable: M. Aké Paul</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span>+225 20 30 40 56</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>Lun-Ven: 8h-15h</span>
                        </div>
                    </div>
                    <a href="#" class="department-btn">Contacter ce service</a>
                </div>

                <!-- Department Card 8 -->
                <div class="department-card" data-category="social">
                    <div class="department-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Éducation</h3>
                    <p>Gestion des écoles municipales, programmes éducatifs et soutien scolaire.</p>
                    <div class="department-info">
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <span>Responsable: Mme. Diomandé Nadège</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span>+225 20 30 40 57</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>Lun-Ven: 8h-16h</span>
                        </div>
                    </div>
                    <a href="#" class="department-btn">Contacter ce service</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Organization Chart -->
    <section class="org-chart-section">
        <div class="departments-container">
            <h2 class="section-title">Organigramme Municipal</h2>
            <div class="org-chart">
                <div class="org-level">
                    <div class="org-node main">
                        <div class="org-content">
                            <h4>Maire du Plateau</h4>
                            <p>M. Jacques Gabriel Ehouo</p>
                        </div>
                    </div>
                </div>
                
                <div class="org-connector"></div>
                
                <div class="org-level">
                    <div class="org-node">
                        <div class="org-content">
                            <h4>Secrétariat Général</h4>
                            <p>M. Prénom Nom</p>
                        </div>
                    </div>
                    <div class="org-node">
                        <div class="org-content">
                            <h4>Direction des Finances</h4>
                            <p>M. Prénom Nom</p>
                        </div>
                    </div>
                    <div class="org-node">
                        <div class="org-content">
                            <h4>Direction des Services Techniques</h4>
                            <p>M. Prénom Nom</p>
                        </div>
                    </div>
                </div>
                
                <div class="org-connector"></div>
                
                <div class="org-level">
                    <div class="org-node">
                        <div class="org-content">
                            <h4>Service État Civil</h4>
                            <p>Mme. Prénom Nom</p>
                        </div>
                    </div>
                    <div class="org-node">
                        <div class="org-content">
                            <h4>Service Action Sociale</h4>
                            <p>M. Prénom Nom</p>
                        </div>
                    </div>
                    <div class="org-node">
                        <div class="org-content">
                            <h4>Service Environnement</h4>
                            <p>M. Prénom Nom</p>
                        </div>
                    </div>
                    <div class="org-node">
                        <div class="org-content">
                            <h4>Service Culture</h4>
                            <p>M. Prénom Nom</p>
                        </div>
                    </div>
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
        --border-radius: 12px;
        --box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }
    
    .departments-page {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--white);
        color: var(--text-color);
        line-height: 1.6;
    }
    
    .departments-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    /* Hero Section */
    .departments-hero {
        background: linear-gradient(rgba(25, 119, 204, 0.05), rgba(25, 119, 204, 0.9)), url('{{asset('assets/assets/img/logo pla.jpeg')}}');
        background-size: cover;
        background-position: center;
        color: var(--white);
        padding: 100px 0;
        text-align: center;
    }
    
    .departments-hero h1 {
        font-size: 2.8rem;
        margin-bottom: 20px;
        font-weight: 700;
        color: white;
    }
    
    .departments-hero p {
        font-size: 1.3rem;
        max-width: 700px;
        margin: 0 auto;
        opacity: 0.9;
    }
    
    /* Navigation */
    .departments-nav {
        background-color: var(--secondary-color);
        padding: 30px 0;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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
        padding: 12px 24px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        transition: var(--transition);
    }
    
    .nav-tab:hover, .nav-tab.active {
        background: var(--primary-color);
        color: var(--white);
        transform: translateY(-2px);
    }
    
    /* Section Title */
    .section-title {
        text-align: center;
        margin-bottom: 50px;
        color: var(--primary-color);
        position: relative;
        font-size: 2.2rem;
    }
    
    .section-title:after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: var(--primary-color);
        margin: 15px auto;
        border-radius: 2px;
    }
    
    /* Departments Grid */
    .departments-grid-section {
        padding: 80px 0;
    }
    
    .departments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
    }
    
    .department-card {
        background: var(--white);
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        padding: 30px;
        display: flex;
        flex-direction: column;
        height: 100%;
        border-top: 5px solid var(--primary-color);
    }
    
    .department-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }
    
    .department-icon {
        font-size: 2.8rem;
        color: var(--primary-color);
        margin-bottom: 20px;
        text-align: center;
    }
    
    .department-card h3 {
        margin-bottom: 15px;
        color: var(--primary-color);
        text-align: center;
        font-size: 1.5rem;
    }
    
    .department-card p {
        margin-bottom: 25px;
        flex-grow: 1;
        text-align: center;
        color: var(--light-text);
    }
    
    .department-info {
        margin-bottom: 25px;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        font-size: 0.95rem;
    }
    
    .info-item i {
        color: var(--primary-color);
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    
    .department-btn {
        display: block;
        text-align: center;
        background: var(--primary-color);
        color: var(--white);
        padding: 14px 20px;
        border-radius: var(--border-radius);
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
        margin-top: auto;
    }
    
    .department-btn:hover {
        background: #125daa;
        transform: translateY(-2px);
    }
    
    /* Organization Chart */
    .org-chart-section {
        background-color: var(--secondary-color);
        padding: 80px 0;
    }
    
    .org-chart {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .org-level {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 30px;
        margin-bottom: 40px;
        width: 100%;
    }
    
    .org-node {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 25px;
        box-shadow: var(--box-shadow);
        text-align: center;
        min-width: 220px;
        position: relative;
        border-top: 4px solid var(--primary-color);
    }
    
    .org-node.main {
        background: var(--primary-color);
        color: var(--white);
        padding: 30px;
    }
    
    .org-node.main h4 {
        color: var(--white);
    }
    
    .org-content h4 {
        margin-bottom: 10px;
        color: var(--primary-color);
        font-size: 1.2rem;
    }
    
    .org-content p {
        color: var(--light-text);
        font-size: 0.95rem;
    }
    
    .org-node.main .org-content p {
        color: rgba(255, 255, 255, 0.9);
    }
    
    .org-connector {
        width: 3px;
        height: 40px;
        background: var(--primary-color);
        margin: 10px 0 20px;
        position: relative;
    }
    
    .org-connector:before {
        content: '';
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 15px;
        height: 15px;
        background: var(--primary-color);
        border-radius: 50%;
    }
    
    /* Contact Section */
    .departments-contact {
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
        font-size: 2.8rem;
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
    @media (max-width: 992px) {
        .departments-hero h1 {
            font-size: 2.2rem;
        }
        
        .departments-hero p {
            font-size: 1.1rem;
        }
        
        .section-title {
            font-size: 1.8rem;
        }
        
        .org-level {
            flex-direction: column;
            align-items: center;
        }
    }
    
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
        
        .departments-grid {
            grid-template-columns: 1fr;
        }
        
        .contact-methods {
            grid-template-columns: 1fr;
        }
        
        .contact-card {
            padding: 40px 20px;
        }
        
        .org-node {
            min-width: auto;
            width: 100%;
            max-width: 300px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.nav-tab');
        const departments = document.querySelectorAll('.department-card');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                tab.classList.add('active');
                
                const category = tab.getAttribute('data-category');
                
                // Show/hide departments based on category
                departments.forEach(dept => {
                    if (category === 'all' || dept.getAttribute('data-category') === category) {
                        dept.style.display = 'flex';
                    } else {
                        dept.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endsection