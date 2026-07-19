<!-- FOOTER -->
<footer class="footer">
    <div class="container-fluid px-10percent">
        <!-- TOP INFO BAR -->
        <div class="footer-top-bar mb-5">
            <div class="row g-4">
                <!-- Item 1 -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="footer-info-item">
                        <div class="footer-info-line-wrapper d-flex align-items-center mb-3">
                            <div class="footer-info-line flex-grow-1"></div>
                            <div class="footer-info-circle ms-2">
                                <i class="fa-solid fa-paper-plane"></i>
                            </div>
                        </div>
                        <a href="mailto:contact@mairieplateau.ci" class="footer-info-text d-block text-decoration-none text-white">contact@mairieplateau.ci</a>
                        <span class="footer-info-subtext">Send a Email</span>
                    </div>
                </div>
                <!-- Item 2 -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="footer-info-item">
                        <div class="footer-info-line-wrapper d-flex align-items-center mb-3">
                            <div class="footer-info-line flex-grow-1"></div>
                            <div class="footer-info-circle ms-2">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                        </div>
                        <a href="tel:+22520212223" class="footer-info-text d-block text-decoration-none text-white">+225 20 21 22 23</a>
                        <span class="footer-info-subtext">Appelez nous à tout moment</span>
                    </div>
                </div>
                <!-- Item 3 -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="footer-info-item">
                        <div class="footer-info-line-wrapper d-flex align-items-center mb-3">
                            <div class="footer-info-line flex-grow-1"></div>
                            <div class="footer-info-circle ms-2">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                        </div>
                        <span class="footer-info-text d-block text-white">Hotel de la ville,Abidjan</span>
                        <span class="footer-info-subtext">Notre Adresse</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN COLUMNS -->
        <div class="row g-5 pt-4">
            <!-- Colonne 1: Logo & Contacts -->
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="mb-4">
                    <img src="{{ asset('assets/assets/img/plateau-mart.png') }}" alt="Logo Mairie du Plateau" style="height: 48px;">
                </div>
                <div class="d-flex flex-column gap-3 small text-white-50">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-location-dot mt-1 text-white-50"></i>
                        <span>Avenue Chardy, Plateau<br>Abidjan, Côte d'Ivoire</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-envelope text-white-50"></i>
                        <a href="mailto:contact@mairieplateau.ci" class="text-white-50 text-decoration-none hover-link">contact@mairieplateau.ci</a>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-phone text-white-50"></i>
                        <a href="tel:+22520212223" class="text-white-50 text-decoration-none hover-link">+225 20 21 22 23</a>
                    </div>
                </div>
            </div>

            <!-- Colonne 2: Liens rapides -->
            <div class="col-lg-2 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <h5 class="fw-800 text-white mb-4 text-uppercase small tracking-wider">Liens Rapides</h5>
                <ul class="list-unstyled d-flex flex-column gap-3 small">
                    <li><a href="{{ route('home') }}" class="text-white-50 text-decoration-none hover-link">A propos</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none hover-link">Services</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none hover-link">Départements</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none hover-link">Contact</a></li>
                </ul>
                <div class="mt-4 pt-2">
                    <a href="{{ route('login') }}" class="text-white-50 text-decoration-none hover-link fw-700" style="color: #60a5fa !important;">Mon espace citoyen</a>
                </div>
            </div>

            <!-- Colonne 3: Nos Services -->
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <h5 class="fw-800 text-white mb-4 text-uppercase small tracking-wider">Nos Services</h5>
                <ul class="list-unstyled d-flex flex-column gap-3 small">
                    <li><a href="#" class="text-white-50 text-decoration-none hover-link">Acte de naissance</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none hover-link">Acte de mariage</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none hover-link">Acte de décès</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none hover-link">CMU</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none hover-link">Légalisation</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none hover-link">Urbanisme</a></li>
                </ul>
            </div>

            <!-- Colonne 4: Suivez-nous & Message -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <h5 class="fw-800 text-white mb-4 text-uppercase small tracking-wider">Suivez-nous</h5>
                <div class="d-flex gap-3 mb-4">
                    <a href="#" class="footer-social-link"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="footer-social-link"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" class="footer-social-link"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                
                <!-- Card box official portal -->
                <div class="p-3 rounded-4" style="background-color: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255, 255, 255, 0.05);">
                    <p class="text-white mb-2 fw-700 small">Portail officiel de la commune du Plateau.</p>
                    <p class="text-white-50 mb-0 small">Moderniser pour mieux vous servir.</p>
                </div>
            </div>
        </div>

        <hr class="my-5 border-white opacity-10">

        <!-- BOTTOM BAR -->
        <div class="row align-items-center text-center text-md-start small text-white-50 pb-3">
            <div class="col-md-6 mb-3 mb-md-0">
                <p class="mb-0">&copy; {{ date('Y') }} Mairie du Plateau &mdash; Tous droits réservés.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="{{ route('plateau.privacy') }}" class="text-white-50 text-decoration-none hover-link me-3">Politique de confidentialité</a>
                <span class="text-white-50">|</span>
                <a href="#" class="text-white-50 text-decoration-none hover-link ms-3">Mentions légales</a>
            </div>
        </div>
    </div>
</footer>

<style>
    .footer {
        background: linear-gradient(rgba(100, 100, 113, 0.97), rgba(100, 100, 113, 0.97)), url('{{ asset("assets/assets/img/footerback.png") }}') no-repeat !important;
        background-size: cover !important;
        background-position: center !important;
        color: #e2e8f0;
        padding: 80px 0 30px;
        border-radius: 60px 60px 0 0;
        position: relative;
        margin-top: 140px;
    }

    .hover-link {
        transition: all 0.3s ease;
        display: inline-block;
    }
    
    .hover-link:hover {
        color: #ffffff !important;
        transform: translateX(4px);
    }
    
    /* --- FOOTER TOP BAR --- */
    .footer-top-bar {
        background-color: #103a83;
        border-radius: 24px;
        padding: 25px 30px; /* slightly optimized padding to account for item margins */
        margin-top: -130px;
        position: relative;
        z-index: 20;
        box-shadow: 0 20px 40px rgba(16, 58, 131, 0.15);
    }
    
    .footer-top-bar .text-white {
        color: #ffffff !important;
    }

    .footer-info-item {
        position: relative;
        padding: 20px;
        border-radius: 20px;
        border: 2px solid transparent;
        background-color: transparent;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        cursor: pointer;
    }

    .footer-info-item:hover {
        background-color: #ffffff !important;
        border-color: #ffffff !important;
        transform: translateY(-45px);
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
    }

    /* Smooth color transitions for items inside the contact card */
    .footer-info-text, 
    .footer-info-subtext, 
    .footer-info-line, 
    .footer-info-circle,
    .footer-info-item .text-white {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    /* Change colors to dark when hovered (white background card active) */
    .footer-info-item:hover .footer-info-text,
    .footer-info-item:hover .text-white {
        color: #0f2c59 !important;
    }

    .footer-info-item:hover .footer-info-subtext {
        color: #64748b !important;
    }

    .footer-info-item:hover .footer-info-line {
        background: linear-gradient(to right, rgba(15, 44, 89, 0.1), #0f2c59) !important;
    }

    .footer-info-item:hover .footer-info-circle {
        background-color: #103a83 !important;
        box-shadow: 0 4px 12px rgba(16, 58, 131, 0.2);
    }

    .footer-info-line-wrapper {
        width: 100%;
        display: flex;
        align-items: center;
    }

    .footer-info-line {
        height: 6px;
        background: linear-gradient(to right, rgba(255, 255, 255, 0.3), #ffffff);
        clip-path: polygon(0 48%, 100% 0, 100% 100%, 0 52%);
    }

    .footer-info-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #0c2b62;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 0.95rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        flex-shrink: 0;
    }

    .footer-info-text {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .footer-info-subtext {
        color: rgba(255, 255, 255, 0.55);
        font-size: 0.85rem;
        font-weight: 500;
    }

    .footer-social-link {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-decoration: none !important;
    }

    .footer-social-link:hover {
        background-color: #103a83;
        color: #ffffff;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(16, 58, 131, 0.3);
    }

    @media (max-width: 991px) {
        .footer-top-bar {
            margin-top: -100px;
            padding: 30px 20px;
        }
    }

    @media (max-width: 768px) {
        .footer-top-bar {
            margin-top: -85px;
            padding: 25px 20px;
        }
        
        .footer-info-line {
            margin-bottom: 10px;
        }
        
        .footer-info-text {
            font-size: 1.05rem;
        }
    }
</style>
