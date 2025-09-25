@extends('home.layouts.tamplate')

@section('content')
<div class="about-page">
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="about-container">
            <h1>À Propos de la Mairie du Plateau</h1>
            <p>Découvrez l'institution au service des citoyens de la commune du Plateau, cœur administratif et économique d'Abidjan</p>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="about-container">
            <h2 class="section-title">Notre Histoire</h2>
            <div class="about-content">
                <div class="about-text">
                    <p>La Mairie du Plateau représente l'autorité administrative de la commune centrale d'Abidjan, cœur économique de la Côte d'Ivoire. Depuis sa création, notre institution n'a cessé d'évoluer pour répondre aux besoins croissants de la population et des entreprises qui font du Plateau un centre d'activité dynamique.</p>
                    <p>Notre mission quotidienne est de garantir le bon fonctionnement des services publics, d'assurer le développement urbain harmonieux de la commune et de faciliter la vie des administrés à travers des services accessibles et efficaces.</p>
                    <p>Avec une équipe dévouée et professionnelle, la Mairie du Plateau s'engage à moderniser continuellement ses services tout en préservant le patrimoine historique et culturel de cette commune emblématique.</p>
                </div>
                <div class="about-image">
                    <img src="{{asset('assets/assets/img/equipemairie.jpg')}}" alt="Bâtiment de la Mairie du Plateau">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="mission-vision">
        <div class="about-container">
            <h2 class="section-title">Notre Mission et Vision</h2>
            <div class="mv-container">
                <div class="mv-card">
                    <div class="mv-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Notre Mission</h3>
                    <p>Offrir des services administratifs de qualité aux citoyens et aux entreprises, assurer un développement urbain durable, et maintenir un environnement favorable aux activités économiques et sociales dans la commune du Plateau.</p>
                </div>
                <div class="mv-card">
                    <div class="mv-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Notre Vision</h3>
                    <p>Faire du Plateau une commune moderne, inclusive et durable, qui préserve son patrimoine tout en s'adaptant aux défis contemporains, et qui sert de modèle de gouvernance locale en Côte d'Ivoire.</p>
                </div>
                <div class="mv-card">
                    <div class="mv-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Nos Valeurs</h3>
                    <p>Intégrité, transparence, innovation et service public. Ces valeurs guident chacune de nos actions et nous aident à construire une administration proche des citoyens et efficace dans ses missions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="about-container">
            <h2 class="section-title">Notre Équipe</h2>
            <div class="team-container">
                <div class="team-member">
                    <div class="team-img">
                        <img src="{{asset('assets/assets/img/maire.jpeg')}}" alt="Maire">
                    </div>
                    <h3>M. Jacques Gabriel Ehouo</h3>
                    <p>Maire du Plateau</p>
                </div>
                <div class="team-member">
                    <div class="team-img">
                        <img src="{{asset('assets/assets/img/Avato.png')}}" alt="Premier adjoint">
                    </div>
                    <h3>M. Prénom Nom</h3>
                    <p>Premier Adjoint</p>
                </div>
                <div class="team-member">
                    <div class="team-img">
                        <img src="{{asset('assets/assets/img/Avato.png')}}" alt="Directeur administratif">
                    </div>
                    <h3>M. Prénom Nom</h3>
                    <p>Directeur Administratif</p>
                </div>
                <div class="team-member">
                    <div class="team-img">
                        <img src="{{asset('assets/assets/img/Avato.png')}}" alt="Responsable services urbains">
                    </div>
                    <h3>M. Prénom Nom</h3>
                    <p>Directeur Etat civil</p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    :root {
        --primary-color: #1977cc;
        --secondary-color: #f8f9fa;
        --text-color: #333;
        --light-text: #6c757d;
        --white: #ffffff;
    }
    
    .about-page {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--white);
        color: var(--text-color);
        line-height: 1.6;
    }
    
    .about-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    /* Hero Section */
    .about-hero {
        background: linear-gradient(rgba(25, 119, 204, 0.5), rgba(25, 119, 204, 0.9)), url('{{asset('assets/assets/img/mairiee.jpg')}}');
        background-size: cover;
        background-position: center;
        color: var(--white);
        padding: 80px 0;
        text-align: center;
    }
    
    .about-hero h1 {
        font-size: 2.5rem;
        color: white;
        margin-bottom: 20px;
    }
    
    .about-hero p {
        font-size: 1.2rem;
        max-width: 700px;
        margin: 0 auto;
    }
    
    /* About Section */
    .about-section {
        padding: 80px 0;
    }
    
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
    
    .about-content {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 50px;
    }
    
    .about-text {
        flex: 1;
        min-width: 300px;
        padding-right: 30px;
    }
    
    .about-image {
        flex: 1;
        min-width: 300px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .about-image img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.5s;
    }
    
    .about-image img:hover {
        transform: scale(1.03);
    }
    
    /* Mission & Vision */
    .mission-vision {
        background-color: var(--secondary-color);
        padding: 80px 0;
    }
    
    .mv-container {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
    }
    
    .mv-card {
        flex: 1;
        min-width: 300px;
        background: var(--white);
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s;
    }
    
    .mv-card:hover {
        transform: translateY(-10px);
    }
    
    .mv-icon {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 20px;
    }
    
    .mv-card h3 {
        margin-bottom: 15px;
        color: var(--primary-color);
    }
    
    /* Team Section */
    .team-section {
        padding: 80px 0;
    }
    
    .team-container {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        justify-content: center;
    }
    
    .team-member {
        flex: 0 0 calc(25% - 30px);
        min-width: 250px;
        text-align: center;
        margin-bottom: 30px;
    }
    
    .team-img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 20px;
        border: 5px solid var(--secondary-color);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .team-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .team-member h3 {
        margin-bottom: 5px;
        color: var(--primary-color);
    }
    
    .team-member p {
        color: var(--light-text);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .about-content {
            flex-direction: column;
        }
        
        .about-text {
            padding-right: 0;
            margin-bottom: 30px;
        }
        
        .mv-container, .team-container {
            flex-direction: column;
        }
        
        .team-member {
            flex: 0 0 100%;
        }
    }
</style>
@endsection