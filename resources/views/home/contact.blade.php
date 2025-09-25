@extends('home.layouts.tamplate')
@section('content')
<div class="contact-page">
    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="contact-container">
            <h1>Contactez-Nous</h1>
            <p>Nous sommes à votre écoute - Service officiel de la Mairie du Plateau</p>
        </div>
    </section>

    <!-- Contact Content -->
    <div class="contact-container">
        <div class="contact-grid">
            <!-- Contact Information -->
            <div class="contact-info">
                <div class="contact-card">
                    <div class="card-header">
                        <h2>Informations de Contact</h2>
                        <p>Comment nous joindre</p>
                    </div>

                    <div class="card-body">
                        <div class="contact-method">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-details">
                                <h3>Adresse</h3>
                                <p>Hôtel de Ville, Place de la Mairie<br>Le Plateau, Abidjan, Côte d'Ivoire</p>
                            </div>
                        </div>

                        <div class="contact-method">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-details">
                                <h3>Téléphone</h3>
                                <p>+225 20 21 22 23<br>+225 20 21 22 24</p>
                            </div>
                        </div>

                        <div class="contact-method">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details">
                                <h3>Email</h3>
                                <p>contact@mairieplateau.ci<br>info@mairieplateau.ci</p>
                            </div>
                        </div>

                        <div class="contact-method">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-details">
                                <h3>Horaires d'ouverture</h3>
                                <p>Lundi - Vendredi: 8h - 17h<br>Samedi: 9h - 13h</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-section">
                <div class="contact-card">
                    <div class="card-header">
                        <h2>Envoyez-nous un message</h2>
                        <p>Nous vous répondrons dans les plus brefs délais</p>
                    </div>

                    <div class="card-body">
                        <form class="contact-form">
                            <div class="form-group">
                                <label for="name">Nom complet</label>
                                <input type="text" id="name" name="name" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Adresse email</label>
                                <input type="email" id="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="phone">Numéro de téléphone</label>
                                <input type="tel" id="phone" name="phone">
                            </div>

                            <div class="form-group">
                                <label for="subject">Sujet</label>
                                <select id="subject" name="subject" required>
                                    <option value="">Sélectionnez un sujet</option>
                                    <option value="extrait-naissance">Extrait de naissance</option>
                                    <option value="extrait-deces">Extrait de décès</option>
                                    <option value="carte-identite">Carte d'identité</option>
                                    <option value="passeport">Passeport</option>
                                    <option value="autre">Autre demande</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" rows="5" required></textarea>
                            </div>

                            <div class="form-submit">
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-paper-plane"></i> Envoyer le message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <section class="map-section">
        <div class="contact-container">
            <h2>Notre Localisation</h2>
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3972.6066238994395!2d-4.026760124185894!3d5.323891035938389!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfc1ebb6ac21af73%3A0x403e9fb7abcc6991!2sMairie%20du%20Plateau!5e0!3m2!1sfr!2sci!4v1755699918271!5m2!1sfr!2sci" width="100%" height="600" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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

    .contact-page {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--white);
        color: var(--text-color);
        line-height: 1.6;
    }

    .contact-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Hero Section */
    .contact-hero {
        background: linear-gradient(rgba(25, 119, 204, 0.5), rgba(25, 119, 204, 0.9)), url('{{asset('assets/assets/img/logo plateau.png')}}');
        background-size: cover;
        background-position: center;
        color: var(--white);
        padding: 80px 0;
        text-align: center;
    }

    .contact-hero h1 {
        font-size: 2.8rem;
        margin-bottom: 20px;
        font-weight: 700;
        color: white;
    }

    .contact-hero p {
        font-size: 1.3rem;
        max-width: 700px;
        margin: 0 auto;
        opacity: 0.9;
    }

    /* Contact Grid */
    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        padding: 50px 0;
    }

    /* Card Styles */
    .contact-card {
        background: var(--white);
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        height: 100%;
    }

    .contact-card:hover {
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

    /* Contact Methods */
    .contact-method {
        display: flex;
        align-items: flex-start;
        margin-bottom: 30px;
    }

    .contact-icon {
        width: 50px;
        height: 50px;
        background-color: var(--primary-light);
        color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        flex-shrink: 0;
        font-size: 1.2rem;
    }

    .contact-details h3 {
        color: var(--primary-color);
        margin-bottom: 5px;
        font-size: 1.2rem;
    }

    .contact-details p {
        color: var(--text-color);
        line-height: 1.6;
    }

    /* Contact Form */
    .contact-form .form-group {
        margin-bottom: 20px;
    }

    .contact-form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text-color);
    }

    .contact-form input,
    .contact-form select,
    .contact-form textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: var(--transition);
    }

    .contact-form input:focus,
    .contact-form select:focus,
    .contact-form textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(25, 119, 204, 0.2);
    }

    .form-submit {
        text-align: center;
        margin-top: 30px;
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

    /* Map Section */
    .map-section {
        padding: 80px 0;
    }

    .map-section h2 {
        text-align: center;
        margin-bottom: 40px;
        color: var(--primary-color);
        font-size: 2.2rem;
    }

    .map-container {
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .contact-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }

    @media (max-width: 768px) {
        .contact-hero h1 {
            font-size: 2.2rem;
        }
        
        .contact-hero p {
            font-size: 1.1rem;
        }
        
        .faq-grid {
            grid-template-columns: 1fr;
        }
        
        .contact-method {
            flex-direction: column;
            text-align: center;
        }
        
        .contact-icon {
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
        
        .btn-primary {
            width: 100%;
            text-align: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const contactForm = document.querySelector('.contact-form');
        
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Ici vous pouvez ajouter le code pour envoyer le formulaire
                alert('Votre message a été envoyé avec succès! Nous vous répondrons dans les plus brefs délais.');
                contactForm.reset();
            });
        }
    });
</script>
@endsection