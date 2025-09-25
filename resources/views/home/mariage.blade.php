@extends('home.layouts.tamplate')
@section('content')
<div class="extrait-page">
    <!-- Hero Section -->
    <section class="extrait-hero">
        <div class="extrait-container">
            <h1>Extraits de Mariage</h1>
            <p>Obtenez votre extrait de mariage en ligne - Service officiel de la Mairie du Plateau</p>
        </div>
    </section>

    <!-- Navigation Tabs -->
    <section class="extrait-tabs">
        <div class="extrait-container">
            <div class="tabs">
                <button class="tab-btn active" data-tab="copie-integrale">Copie intégrale</button>
                <button class="tab-btn" data-tab="extrait-simple">Extrait simple</button>
            </div>
        </div>
    </section>

    <!-- Tab Content -->
    <div class="extrait-container">
        <!-- Copie Intégrale -->
        <div class="tab-content active" id="copie-integrale">
            <div class="extrait-card">
                <div class="card-header">
                    <h2>Copie intégrale d'acte de mariage</h2>
                    <p>Document complet avec toutes les informations sur l'union matrimoniale</p>
                </div>

                <div class="card-body">
                    <div class="info-alert">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>À savoir :</strong> La copie intégrale contient toutes les informations sur le mariage, y compris les détails sur les époux et leurs parents.
                        </div>
                    </div>

                    <div class="description">
                        <p>La copie intégrale de l'acte de mariage est la reproduction intégrale de l'acte de mariage inscrit sur le registre d'état civil. Elle comporte des informations sur les époux (noms, prénoms, dates et lieu de naissance), des informations sur leurs parents et les mentions marginales lorsqu'elles existent.</p>
                    </div>

                    <div class="requirements">
                        <h3><i class="fas fa-file-alt"></i> Documents requis</h3>
                        <div class="requirements-grid">
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Nom et prénoms complets des deux époux</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Date de naissance des époux</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Lieu de naissance des époux</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Date du mariage</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Lieu du mariage (commune)</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Copie des pièces d'identité des requérants</span>
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

        <!-- Extrait Simple -->
        <div class="tab-content" id="extrait-simple">
            <div class="extrait-card">
                <div class="card-header">
                    <h2>Extrait simple d'acte de mariage</h2>
                    <p>Document simplifié avec les informations essentielles sur le mariage</p>
                </div>

                <div class="card-body">
                    <div class="info-alert">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>À savoir :</strong> L'extrait simple contient uniquement les informations essentielles sur le mariage.
                        </div>
                    </div>

                    <div class="description">
                        <p>L'extrait simple de l'acte de mariage est un document qui reprend une partie des informations de l'acte de mariage original. Il comporte uniquement les informations sur les époux (noms, prénoms, dates et lieu de naissance) ainsi que la date et le lieu du mariage, sans les informations sur les parents ni les mentions marginales.</p>
                    </div>

                    <div class="requirements">
                        <h3><i class="fas fa-list-check"></i> Documents requis</h3>
                        <div class="requirements-grid">
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Nom et prénoms complets des deux époux</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Date de naissance des époux</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Date du mariage</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Lieu du mariage (commune)</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Copie des pièces d'identité des requérants</span>
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
                    <p>L'extrait simple contient les informations de base sur les époux et le mariage tandis que la copie intégrale inclut également des informations sur les parents et les mentions marginales.</p>
                </div>
                <div class="faq-item">
                    <h3>Combien de temps faut-il pour obtenir un extrait de mariage ?</h3>
                    <p>Le traitement d'une demande en ligne prend généralement 24 à 48 heures. Vous recevrez une notification dès que votre document sera prêt.</p>
                </div>
                <div class="faq-item">
                    <h3>Puis-je faire une demande pour un mariage célébré dans une autre commune ?</h3>
                    <p>Non, vous devez vous adresser à la mairie où le mariage a été célébré pour obtenir une copie de l'acte.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="extrait-container">
            <div class="contact-card">
                <h2>Besoin d'aide ?</h2>
                <p>Notre équipe est à votre disposition pour vous accompagner dans vos démarches</p>
                
                <div class="contact-methods">
                    <div class="contact-method">
                        <i class="fas fa-phone"></i>
                        <h3>Par téléphone</h3>
                        <p>+225 XX XX XX XX<br>Du lundi au vendredi, 8h-17h</p>
                    </div>
                    
                    <div class="contact-method">
                        <i class="fas fa-envelope"></i>
                        <h3>Par email</h3>
                        <p>etatcivil@mairieplateau.ci<br>Réponse sous 24h</p>
                    </div>
                    
                    <div class="contact-method">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>En personne</h3>
                        <p>Mairie du Plateau<br>Service État Civil</p>
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