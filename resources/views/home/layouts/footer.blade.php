<!-- FOOTER -->
<footer class="footer">
    <div class="container-fluid px-10percent">
        <div class="row g-5">
            <!-- Colonne 1: Présentation & Contacts -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <img src="{{ asset('assets/assets/img/plateau-mart.png') }}" alt="Logo Mairie du Plateau" class="footer-logo mb-0" style="height: 60px; filter: drop-shadow(0 0 8px rgba(255,255,255,0.1)); background-color: white;">
                    <div>
                        <span class="d-block fw-900 fs-5 text-white text-uppercase" style="letter-spacing: 0.5px;">PLATEAU-APPS</span>
                        <span class="d-block text-white-50 small font-weight-600">Portail Officiel des Démarches</span>
                    </div>
                </div>
                <p class="text-white-50 small mb-4 lh-lg">
                    La Mairie du Plateau modernise ses services administratifs pour offrir aux citoyens une plateforme sécurisée, rapide et accessible 24h/7j pour toutes leurs demandes d'actes d'état civil.
                </p>
                <div class="d-flex flex-column gap-3 text-white-50 small">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt text-primary fs-5"></i>
                        <span>Hôtel de Ville du Plateau, Avenue Chardy, Abidjan</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-envelope text-primary fs-5"></i>
                        <a href="mailto:contact@mairie-plateau.ci" class="text-white-50 text-decoration-none transition-color hover:text-white">contact@mairie-plateau.ci</a>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-telephone text-primary fs-5"></i>
                        <a href="tel:+2250709500501" class="text-white-50 text-decoration-none transition-color hover:text-white">+225 07 095 005 01</a>
                    </div>
                </div>
            </div>

            <!-- Colonne 2: Services rapides -->
            <div class="col-lg-3 col-md-6">
                <h5 class="fw-800 text-white mb-4 text-uppercase small tracking-wider" style="border-left: 3px solid #1f4083; padding-left: 10px;">Services en ligne</h5>
                <ul class="list-unstyled d-flex flex-column gap-3 small">
                    <li>
                        <a href="{{ route('home.birth') }}" class="text-white-50 text-decoration-none hover-link d-flex align-items-center gap-2 transition">
                            <i class="bi bi-chevron-right text-primary small"></i> Acte de Naissance
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home.wedding') }}" class="text-white-50 text-decoration-none hover-link d-flex align-items-center gap-2 transition">
                            <i class="bi bi-chevron-right text-primary small"></i> Acte de Mariage
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home.death') }}" class="text-white-50 text-decoration-none hover-link d-flex align-items-center gap-2 transition">
                            <i class="bi bi-chevron-right text-primary small"></i> Acte de Décès
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('recherche.demande') }}" class="text-white-50 text-decoration-none hover-link d-flex align-items-center gap-2 transition">
                            <i class="bi bi-chevron-right text-primary small"></i> Suivre ma demande
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Colonne 3: Municipalité & Utile -->
            <div class="col-lg-2 col-md-6">
                <h5 class="fw-800 text-white mb-4 text-uppercase small tracking-wider" style="border-left: 3px solid #1f4083; padding-left: 10px;">Utile</h5>
                <ul class="list-unstyled d-flex flex-column gap-3 small">
                    <li>
                        <a href="#" class="text-white-50 text-decoration-none hover-link d-flex align-items-center gap-2 transition">
                            <i class="bi bi-chevron-right text-primary small"></i> La Municipalité
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-white-50 text-decoration-none hover-link d-flex align-items-center gap-2 transition">
                            <i class="bi bi-chevron-right text-primary small"></i> Actualités
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-white-50 text-decoration-none hover-link d-flex align-items-center gap-2 transition">
                            <i class="bi bi-chevron-right text-primary small"></i> Guide des démarches
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('plateau.privacy') }}" class="text-white-50 text-decoration-none hover-link d-flex align-items-center gap-2 transition">
                            <i class="bi bi-chevron-right text-primary small"></i> Confidentialité
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Colonne 4: Horaires & Réseaux -->
            <div class="col-lg-3 col-md-6">
                <h5 class="fw-800 text-white mb-4 text-uppercase small tracking-wider" style="border-left: 3px solid #1f4083; padding-left: 10px;">Horaires & Réseaux</h5>
                <div class="text-white-50 small mb-4 lh-lg">
                    <div class="d-flex justify-content-between border-bottom border-secondary border-opacity-10 pb-2 mb-2">
                        <span>Lundi - Vendredi</span>
                        <span class="text-white">07h30 - 16h30</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom border-secondary border-opacity-10 pb-2 mb-2">
                        <span>Samedi - Dimanche</span>
                        <span class="text-danger fw-600">Fermé</span>
                    </div>
                    <p class="xsmall text-white-50 mt-2 italic"><i class="bi bi-info-circle me-1"></i>Déclarations de décès assurées d'urgence le week-end.</p>
                </div>
                <div class="d-flex gap-3">
                    <a href="#" class="social-link facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-link twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="social-link linkedin"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="social-link youtube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>

        <hr class="my-5 border-white opacity-10">
        
        <div class="row align-items-center text-center text-md-start small text-white-50">
            <div class="col-md-6 mb-3 mb-md-0">
                <p class="mb-0">&copy; 2024 MAIRIE DU PLATEAU. TOUS DROITS RÉSERVÉS.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">Développé pour la modernisation administrative de la commune du Plateau.</p>
            </div>
        </div>
    </div>
</footer>

<style>
    .hover-link {
        transition: all 0.3s ease;
    }
    .hover-link:hover {
        color: #ffffff !important;
        transform: translateX(5px);
    }
    .social-link {
        background: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 50% !important;
        width: 40px !important;
        height: 40px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.7) !important;
        transition: all 0.3s ease;
        text-decoration: none !important;
    }
    .social-link:hover {
        background: #1f4083 !important;
        color: #ffffff !important;
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(31, 64, 131, 0.3);
    }
    .xsmall {
        font-size: 0.75rem;
    }
    .italic {
        font-style: italic;
    }
</style>

