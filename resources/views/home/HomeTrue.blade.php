<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateau-apps</title>

    <link rel="shortcut icon" href="{{ asset('assets/assets/img/logo plateau.png') }}" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/assets/css/accueil.css') }}">
</head>

<body class="play-init">
    <!-- SCRIPT ANTI-CLIGNOTEMENT (À l'intérieur du body) -->
    <script>
        // 1) Aucune animation/transition au premier rendu (refresh) — retiré après le 1er paint
        document.documentElement.classList.add('fp-preload');
        // 2) Active le mode fullpage immédiatement (évite l'empilement des sections sur desktop uniquement)
        if (window.innerWidth >= 992 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.body.classList.add('fullpage-mode');
        }
    </script>

    <!-- TÉLÉPHONE VOLANT PRÉCHARGÉ -->
    <img src="{{ asset('assets/landing/iphone-mockup.png') }}" id="flying-phone" class="persistent-phone" alt="">

    <!-- BOUTON RETOUR EN HAUT (Caché par défaut) -->
    <a href="#" class="back-to-top" id="backToTop" aria-label="Retour en haut">
        <i class="fas fa-arrow-up"></i>
    </a>

    <section id="hero" class="hero fp-active">
        <!-- Cercles décoratifs -->
        <div class="hero-circle-bg"></div>
        <div class="deco-circle c1"></div>
        <div class="deco-circle c2"></div>
        <div class="deco-circle c3"></div>

        <!-- HEADER : Logo + Boutons -->
        <header class="top-bar">
            <div class="logo">
                <img src="{{ asset('assets/assets/img/plateau-mart.png') }}" alt="Plateau Smart City"
                    style="height: 52px; width: auto; display: block;" class="logo-img">
            </div>

            <nav class="nav-buttons">
                <a href="{{ route('recherche.demande') }}" class="btn-pill outline">
                    <i class="fas fa-search"></i> Suivre ma demande
                </a>
                <a href="{{ route('login') }}" class="btn-pill solid">
                    <i class="fas fa-user"></i> Mon espace
                </a>
            </nav>
        </header>

        <!-- HERO CONTENT -->
        <div class="hero-container">

            <!-- COL GAUCHE : Texte + CTA -->
            <div class="hero-left">
                <h1 class="hero-title">
                    <span class="word-1">Etat civil</span>
                    <span class="word-2">SIMPLIFIE</span>
                </h1>

                <p class="hero-description">
                    Ne perdez plus des heures en déplacements inutiles.
                    Obtenez vos actes d'état civil, prenez rendez-vous et
                    suivez vos demandes en quelques clics.
                </p>

                <!-- Features -->
                <div class="features">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-users"></i></div>
                        <div class="feature-text">
                            <strong>Sans</strong>
                            file d'attente
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="far fa-clock"></i></div>
                        <div class="feature-text">
                            <strong>Disponible</strong>
                            24/24
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                        <div class="feature-text">
                            <strong>En quelques</strong>
                            minutes
                        </div>
                    </div>
                </div>

                <!-- Boutons download -->
                <div class="download-buttons">
                    <a href="#" class="btn-store">
                        <i class="fab fa-apple"></i>
                        <div class="store-text">
                            <small>Télécharger</small>
                            <span>l'App Store</span>
                        </div>
                    </a>
                  <a href="#" class="btn-store">
    <img src="{{ asset('assets/assets/img/icons8-google-play-96.png') }}" alt="Google Play" style="width: 32px; height: 32px; object-fit: contain;">
    <div class="store-text">
        <small>Disponible sur</small>
        <span>Google Play</span>
    </div>
</a>
                </div>

                <!-- Trust / Témoignage -->
                <div class="trust-section">
                    <div class="avatars">
                        <img class="avatar"
                            src="{{ asset('assets/assets/img/f25c4eb80c53c7b4676b4cd35692b492096ca587.png') }}" alt="A">
                        <img class="avatar"
                            src="{{ asset('assets/assets/img/6e217eb5ce3a756b4a782e9e0063eae0ae5feff0.jpg') }}" alt="B">
                        <img class="avatar"
                            src="{{ asset('assets/assets/img/15e0fef8b16d59d0883862bed53bfe190399f9c4.png') }}" alt="C">
                        <img class="avatar"
                            src="{{ asset('assets/assets/img/a78c1465140380dd2d7eee44ca7318e16b549742.png') }}" alt="D">
                    </div>
                    <div class="trust-text">
                        <span class="count">+12 000 habitants</span>
                        <span class="subtitle">utilisent déjà Plateau Apps</span>
                        <div class="stars">
                            <span class="stars-icons">★★★★★</span>
                            <span class="rating">4.8/5</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COL DROITE : iPhone mockup -->
            <div class="hero-right">
                <div class="hero-phone-scroll-wrapper">
                    <img src="{{ asset('assets/landing/iphone-mockup.png') }}" alt="Aperçu de l'application Plateau"
                        class="iphone-mockup"
                        onerror="this.style.display='none';">
                </div>
            </div>

        </div>

        <!-- Scroll down -->
        <a href="#next-section" class="scroll-down" aria-label="Défiler vers le bas">
            <i class="fas fa-chevron-down"></i>
        </a>
    </section>

    <!-- ════════════════════════════════════════════════════════
     SECTION "À PROPOS"
     ════════════════════════════════════════════════════════ -->
    <section id="next-section" class="about-section">
        <div class="about-bg"></div>
        <div class="about-container">

            <!-- COLONNE GAUCHE : Notre Vision -->
            <div class="about-left reveal-left">
                <div class="eyebrow">
                    <span class="line"></span>
                    <span class="label">NOTRE VISION</span>
                </div>

                <h2 class="about-title">
                    Une administration<br>
                    <span class="accent">humaine,</span> moderne<br>
                    et transparente.
                </h2>

                <p class="about-text">
                    Au cœur du centre des affaires d'Abidjan,
                    nous croyons que la technologie doit
                    simplifier la vie, pas la complexifier.
                </p>

                <p class="about-text">
                    Notre vision est de redéfinir la relation entre
                    la Mairie du Plateau et ses citoyens.
                </p>

                <p class="about-text">
                    Finies les tracasseries administratives : nous
                    bâtissons une administration transparente,
                    rapide et <span class="accent">accessible à tous</span>, à chaque instant
                    de votre vie.
                </p>

                <!-- 4 mini-cards -->
                <div class="mini-cards">
                    <div class="mini-card">
                        <div class="icon-wrapper"><i class="fas fa-users"></i></div>
                        <div class="label">Proche<br>de vous</div>
                    </div>
                    <div class="mini-card">
                        <div class="icon-wrapper"><i class="fas fa-shield-alt"></i></div>
                        <div class="label">Transparente<br>et sécurisée</div>
                    </div>
                    <div class="mini-card">
                        <div class="icon-wrapper"><i class="fas fa-bolt"></i></div>
                        <div class="label">Rapide<br>et efficace</div>
                    </div>
                    <div class="mini-card">
                        <div class="icon-wrapper"><i class="fas fa-heart"></i></div>
                        <div class="label">À l'écoute<br>de vos besoins</div>
                    </div>
                </div>
            </div>

            <!-- COLONNE CENTRE : iPhone mockup tourné -->
            <div class="about-phone-wrap reveal">
                <img src="{{ asset('assets/assets/img/telephone2HD.png') }}" alt="Plateau Apps mobile" class="about-phone"
                    onerror="this.style.display='none';">
            </div>

            <!-- COLONNE DROITE : À Propos -->
            <div class="about-right reveal-right">
                <div class="eyebrow">
                    <span class="label">A PROPOS</span>
                </div>

                <h2 class="about-title right">
                    Plateau Apps<br>
                    Votre mairie <span class="accent">dans</span><br>
                    <span class="accent">la poche.</span>
                </h2>

                <p class="about-text">
                    Plateau Apps est le portail officiel conçu pour
                    dématérialiser vos démarches citoyennes les
                    plus essentielles.
                </p>

                <p class="about-text">
                    Qu'il s'agisse de célébrer une nouvelle vie
                    (actes de naissance), de sceller une union
                    (actes de mariage) ou de planifier un rendez-
                    vous crucial en mairie, notre plateforme
                    sécurise et accélère vos demandes pour
                    vous faire gagner un temps précieux.
                </p>

                <!-- Carte sécurité -->
                <div class="security-card">
                    <div class="shield-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="security-text">
                        Vos données sont protégées<br>
                        à <span class="accent">100%</span> et traitées avec<br>
                        le plus haut niveau de sécurité.
                    </div>
                </div>

                <div style="margin-top: 24px;">
                    <a href="#services" class="btn-discover">
                        Découvrir Plateau Apps
                        <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- ════════════════════════════════════════════════════════
     SECTION 3 — "Des services pensés pour vous simplifier la vie"
     ════════════════════════════════════════════════════════ -->
    <section id="services" class="services-section">
        <div class="services-top">
            <div class="reveal-left">
                <div class="eyebrow">
                    <span class="line"></span>
                    <span class="label">PLATEAU APPS</span>
                </div>

                <h2 class="services-title">
                    Des services pensés<br>
                    Pour vous <span class="accent">simplifier la vie.</span>
                </h2>

                <p class="services-text">
                    Plateau Apps centralise tous vos services
                    essentiels pour vous offrir une expérience
                    rapide, sécurisée et accessible à tous.
                </p>
                <div class="swipe-hint">
                    <span>Glisser pour voir nos services</span>
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>

            <div class="services-phone-side reveal-right">
                <img src="{{ asset('assets/landing/iphone-mockup.png') }}" alt="Plateau Apps services"
                    class="services-phone" onerror="this.style.display='none';">
            </div>
        </div>

        <div class="services-cards-wrap">
            <div class="services-cards">
                <div class="service-card reveal-up stagger-1">
                    <div class="circle"><i class="fas fa-bolt"></i></div>
                    <div class="name">Traitement <span class="accent">Express</span></div>
                    <div class="underline"></div>
                    <div class="desc">Vos demandes traitées en un temps record.</div>
                </div>

                <div class="service-card reveal-up stagger-2">
                    <div class="circle"><i class="fas fa-home"></i></div>
                    <div class="name">Zéro <span class="accent">Déplacement</span></div>
                    <div class="underline"></div>
                    <div class="desc">Faites vos démarches depuis chez vous.</div>
                </div>

                <div class="service-card reveal-up stagger-3">
                    <div class="circle"><i class="fas fa-certificate"></i></div>
                    <div class="name">Actes <span class="accent">Certifiés</span></div>
                    <div class="underline"></div>
                    <div class="desc">Des documents officiels authentiques et sécurisés.</div>
                </div>

                <div class="service-card reveal-up stagger-4">
                    <div class="circle"><i class="fas fa-mobile-alt"></i></div>
                    <div class="name">Paiement <span class="accent">Mobile</span></div>
                    <div class="underline"></div>
                    <div class="desc">Payez facilement et en toute sécurité.</div>
                </div>

                <div class="service-card reveal-up stagger-5">
                    <div class="circle"><i class="fas fa-globe-africa"></i></div>
                    <div class="name">Éco-<span class="accent">Citoyen</span></div>
                    <div class="underline"></div>
                    <div class="desc">Agissons ensemble pour une ville plus durable.</div>
                </div>
            </div>
            <!-- Indicators / dots for mobile slider -->
            <div class="slider-dots services-dots-container" id="services-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
    </section>

    <!-- ════════════════════════════════════════════════════════
     SECTION 4 — "Comment ça marche ?"
     ════════════════════════════════════════════════════════ -->
    <section id="how" class="how-section">
        <h2 class="how-title reveal-up">
            Comment ça <span class="accent">marche ?</span>
        </h2>
        <div class="swipe-hint">
            <span  style="color: white;">Glisser pour suivre les étapes</span>
            <i class="fas fa-arrow-right"></i>
        </div>

        <div class="how-phones-row">
            <div class="how-phone-item reveal-up stagger-1">
                <div class="how-phone-img-wrap">
                    <img src="{{ asset('assets/landing/how-naissance.png') }}" alt="Acte de naissance" class="how-phone-img">
                </div>
                <div class="how-step-line"></div>
                <div class="how-step-info">
                    <div class="how-step-number">01</div>
                    <div class="how-step-text">
                        <div class="how-step-name">Acte de<br>Naissance</div>
                        <div class="how-step-desc">Demande et copie conforme en ligne</div>
                    </div>
                </div>
            </div>

            <div class="how-phone-item reveal-up stagger-2">
                <div class="how-phone-img-wrap">
                    <img src="{{ asset('assets/landing/how-mariage.png') }}" alt="Acte de mariage" class="how-phone-img">
                </div>
                <div class="how-step-line"></div>
                <div class="how-step-info">
                    <div class="how-step-number">02</div>
                    <div class="how-step-text">
                        <div class="how-step-name">Acte de<br>Mariage</div>
                        <div class="how-step-desc">Planification et documents d'union</div>
                    </div>
                </div>
            </div>

            <div class="how-phone-item reveal-up stagger-3">
                <div class="how-phone-img-wrap">
                    <img src="{{ asset('assets/landing/how-deces.png') }}" alt="Acte de décès" class="how-phone-img">
                </div>
                <div class="how-step-line"></div>
                <div class="how-step-info">
                    <div class="how-step-number">03</div>
                    <div class="how-step-text">
                        <div class="how-step-name">Acte de<br>Décès</div>
                        <div class="how-step-desc">Assistance administrative</div>
                    </div>
                </div>
            </div>

            <div class="how-phone-item reveal-up stagger-4">
                <div class="how-phone-img-wrap">
                    <img src="{{ asset('assets/landing/how-rdv.png') }}" alt="Rendez-vous" class="how-phone-img">
                </div>
                <div class="how-step-line"></div>
                <div class="how-step-info">
                    <div class="how-step-number">04</div>
                    <div class="how-step-text">
                        <div class="how-step-name">Rendez-<br>vous</div>
                        <div class="how-step-desc">Planification en mairie en 2 min</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Indicators / dots for mobile slider -->
        <div class="slider-dots how-dots-container" id="how-dots">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </section>

    <!-- ════════════════════════════════════════════════════════
     SECTION 5 — "Le Plateau plus proche que jamais" + Footer
     ════════════════════════════════════════════════════════ -->
    <section id="final" class="final-section">
        <div class="final-logo">
            
        </div>

        <div class="final-container">
            <div class="reveal-left">
                <h2 class="final-title">
                    <span class="line-1">Le Plateau</span>
                    <span class="line-2">plus proche</span>
                    <span class="line-3">que jamais</span>
                </h2>

                <p class="final-text">
                    Une nouvelle façon d'accéder à vos services municipaux.<br>
                    Plus simple, plus rapide, disponible à tout moment.
                </p>

                <div class="final-stats">
                    <div class="final-stat">
                        <div class="icon-circle"><i class="far fa-clock"></i></div>
                        <div class="stat-name">Gagnez du temps</div>
                        <div class="stat-desc">Évitez les files d'attente.</div>
                    </div>
                    <div class="final-stat">
                        <div class="icon-circle"><i class="fas fa-shield-alt"></i></div>
                        <div class="stat-name">Service officiel</div>
                        <div class="stat-desc">Données 100% protégées.</div>
                    </div>
                    <div class="final-stat">
                        <div class="icon-circle"><i class="fas fa-mobile-alt"></i></div>
                        <div class="stat-name">Simple à utiliser</div>
                        <div class="stat-desc">Expérience claire et intuitive.</div>
                    </div>
                    <div class="final-stat">
                        <div class="icon-circle"><i class="fas fa-leaf"></i></div>
                        <div class="stat-name">Eco-responsable</div>
                        <div class="stat-desc">Moins de papier utilisé.</div>
                    </div>
                </div>
            </div>

            <div class="final-phone-wrap reveal-right">
                <img src="{{ asset('assets/landing/how-deces.png') }}" alt="Plateau Apps RDV" class="final-phone"
                    onerror="this.style.display='none';">
            </div>
        </div>

        
        

        <div class="download-bar">
            <h3>Téléchargez Plateau Apps</h3>
            <p>et simplifiez votre quotidien dès aujourd'hui.</p>

            <div class="download-row">
                <div class="download-trust">
                    <div class="avatars">
                        <img class="avatar" src="{{ asset('assets/assets/img/f25c4eb80c53c7b4676b4cd35692b492096ca587.png') }}" alt="A">
                        <img class="avatar" src="{{ asset('assets/assets/img/6e217eb5ce3a756b4a782e9e0063eae0ae5feff0.jpg') }}" alt="B">
                        <img class="avatar" src="{{ asset('assets/assets/img/15e0fef8b16d59d0883862bed53bfe190399f9c4.png') }}" alt="C">
                    </div>
                    <div class="info">
                        <strong>+12000 habitants</strong>
                        utilisent déjà plateau Apps
                        <div><span class="stars-icons">★★★★★</span> <span class="rating">4.8/5</span></div>
                    </div>
                </div>

                <a href="#" class="btn-store-dark">
                    <i class="fab fa-apple"></i>
                    <div class="store-text">
                        <small>Télécharger</small>
                        <span>l'App Store</span>
                    </div>
                </a>

               <a href="#" class="btn-store-dark">
    <img src="{{ asset('assets/assets/img/icons8-google-play-96.png') }}" alt="Google Play" style="width: 26px; height: 26px; object-fit: contain;">
    <div class="store-text">
        <small>Disponible sur</small>
        <span>Google Play</span>
    </div>
</a>
            </div>
        </div>

        <div class="footer-grid-wrapper">
            <div class="footer-grid-bg"></div>
            
            <div class="download-separator">
                <a href="#" class="scroll-down-final"><i class="fas fa-chevron-down"></i></a>
            </div>

            <footer class="footer">
                <div class="footer-container">
                    <div class="footer-left">
                        <img src="{{ asset('assets/assets/img/plateau-mart1.png') }}" alt="Plateau Smart City" class="logo-img">
                        <div class="footer-tagline">Une administration moderne,<br>proche de vous, pour vous.</div>
                    </div>

                    <div class="footer-social">
                        <a href="#hero" aria-label="Retour en haut">
                            <i class="fas fa-chevron-up"></i>
                        </a>
                    </div>

                    <div class="footer-right">
                        Service officiel de la Mairie du Plateau<br>
                        © 2026 Tous droits réservés.
                    </div>
                </div>
            </footer>
        </div>

    </section>

    <!-- GSAP (Le script original PC, totalement inchangé) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

    <script>
        const FP_ENABLED = window.innerWidth >= 992 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const fpPages = Array.from(document.querySelectorAll('body > section, body > footer'));
        let fpIdx = 0;
        let fpTransitioning = false;
        const FP_DURATION = 800;

        if (FP_ENABLED) {
            document.body.classList.add('fullpage-mode');
            if (fpPages[0]) fpPages[0].classList.add('fp-active');
        }

        const HAS_GSAP = typeof gsap !== 'undefined';
        const PP_ENABLED = FP_ENABLED && HAS_GSAP && window.innerWidth >= 992;

        const PP_SLOT_CONFIG = [
            { sel: '.iphone-mockup', rotate: -3 },
            { sel: '.about-phone', rotate: 8 },
            { sel: '.services-phone', rotate: 12 },
            { sel: '.how-phone-item:nth-child(1) .how-phone-img', rotate: 0 },
            { sel: '.final-phone', rotate: -10 },
        ];

        let ppEl = null; let ppSlots = []; let ppVisible = false; let ppCurrentImg = ''; let ppCurrentIdx = -1;

        function ppMeasureSlots() {
            ppSlots = PP_SLOT_CONFIG.map((cfg, i) => {
                if (!cfg) return null;
                const sec = fpPages[i];
                const anchor = sec && sec.querySelector(cfg.sel);
                if (!anchor) return null;

                const sT = sec.style.transform, sTr = sec.style.transition;
                const aT = anchor.style.transform, aA = anchor.style.animation;
                sec.style.transition = 'none'; sec.style.transform = 'none';
                anchor.style.animation = 'none'; anchor.style.transform = 'none';

                const r = anchor.getBoundingClientRect();
                const slot = { x: r.left + r.width / 2, y: r.top + r.height / 2, width: r.width, rotate: cfg.rotate, img: anchor.getAttribute('src') };

                sec.style.transform = sT; sec.style.transition = sTr;
                anchor.style.transform = aT; anchor.style.animation = aA;
                return slot;
            });
        }

        function ppMoveTo(idx) {
            if (!PP_ENABLED || !ppEl) return;
            const slot = ppSlots[idx];
            if (!slot) {
                gsap.killTweensOf(ppEl);
                gsap.to(ppEl, { opacity: 0, duration: 0.4, ease: 'power2.out', onComplete: () => { ppVisible = false; } });
                ppCurrentIdx = idx;
                return;
            }

            const imgChanged = ppCurrentImg !== slot.img;
            gsap.killTweensOf(ppEl);

            if (!ppVisible) {
                if (imgChanged) { ppEl.src = slot.img; ppCurrentImg = slot.img; }
                gsap.set(ppEl, { x: slot.x, y: slot.y, scale: slot.width / 100, rotation: slot.rotate, opacity: 0 });
                gsap.to(ppEl, { opacity: 1, duration: 0.55, ease: 'power2.out' });
                ppVisible = true;
            } else if (imgChanged) {
                gsap.to(ppEl, { x: slot.x, y: slot.y, scale: slot.width / 100, rotation: slot.rotate, duration: 0.8, ease: 'power3.inOut' });
                gsap.to(ppEl, { opacity: 0.2, duration: 0.4, ease: 'power1.inOut', onComplete: () => { ppEl.src = slot.img; ppCurrentImg = slot.img; } });
                gsap.to(ppEl, { opacity: 1, duration: 0.4, delay: 0.4, ease: 'power1.inOut' });
            } else {
                gsap.to(ppEl, { x: slot.x, y: slot.y, scale: slot.width / 100, rotation: slot.rotate, opacity: 1, duration: 0.8, ease: 'power3.inOut' });
            }
            ppCurrentIdx = idx;
        }

        function ppInit() {
            if (!PP_ENABLED) return;
            ppEl = document.getElementById('flying-phone');
            PP_SLOT_CONFIG.forEach((cfg, i) => {
                if (!cfg) return;
                const anchor = fpPages[i] && fpPages[i].querySelector(cfg.sel);
                if (anchor && i !== 3) anchor.style.visibility = 'hidden';
            });
            fpPages.forEach((sec, i) => {
                sec.addEventListener('scroll', () => {
                    if (i !== fpIdx || !ppEl || !ppSlots[i] || fpTransitioning) return;
                    gsap.set(ppEl, { y: ppSlots[i].y - sec.scrollTop });
                }, { passive: true });
            });
            ppEl.style.width = '100px'; ppEl.style.height = 'auto';
            gsap.set(ppEl, { xPercent: -50, yPercent: -50, transformOrigin: 'center center' });
            ppMeasureSlots();
            const first = ppSlots[0];
            if (first) {
                ppEl.src = first.img; ppCurrentImg = first.img;
                gsap.set(ppEl, { x: first.x, y: first.y, scale: first.width / 100, rotation: first.rotate, opacity: 1 });
                ppVisible = true; ppCurrentIdx = 0;
            }
        }

        let ppResizeTimer = null;
        window.addEventListener('resize', () => {
            if (!PP_ENABLED || !ppEl) return;
            clearTimeout(ppResizeTimer);
            ppResizeTimer = setTimeout(() => {
                ppMeasureSlots();
                const slot = ppSlots[ppCurrentIdx];
                if (slot) gsap.set(ppEl, { x: slot.x, y: slot.y, scale: slot.width / 100, rotation: slot.rotate });
            }, 150);
        });

        window.addEventListener('load', () => {
            if (!PP_ENABLED || !ppEl) return;
            ppMeasureSlots();
            const slot = ppSlots[ppCurrentIdx];
            if (slot) gsap.set(ppEl, { x: slot.x, y: slot.y, scale: slot.width / 100, rotation: slot.rotate });
            setTimeout(() => { document.body.classList.remove('play-init'); }, 1500);
        });

        ppInit();

        function fpRevealIn(section) {
            section.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-up, .reveal-scale').forEach(el => el.classList.add('visible'));
        }
        function fpResetReveals(section) {
            section.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-up, .reveal-scale').forEach(el => el.classList.remove('visible'));
        }

        function fpAnimateSwap(leaving, entering, dir) {
            const leavingIdx = fpPages.indexOf(leaving);
            const enteringIdx = fpPages.indexOf(entering);
            if (leavingIdx === 0) {
                gsap.killTweensOf([leaving, entering, '.hero-circle-bg', '.top-bar', '.hero-container', '.scroll-down', '.deco-circle']);
                entering.classList.add('fp-active'); leaving.style.transition = 'none'; entering.style.transition = 'none';
                entering.style.zIndex = '6'; leaving.style.zIndex = '5';
                gsap.set(entering, { opacity: 0, scale: 1 });
                gsap.to('.hero-circle-bg', { scale: 8, duration: 0.7, ease: 'power2.inOut' });
                gsap.to(['.top-bar', '.hero-container', '.scroll-down', '.deco-circle'], { opacity: 0, duration: 0.3, ease: 'power2.out' });
                gsap.to(entering, { opacity: 1, duration: 0.55, delay: 0.15, ease: 'power2.out', onComplete: () => {
                    leaving.classList.remove('fp-active'); gsap.set('.hero-circle-bg', { clearProps: 'transform' });
                    gsap.set(['.top-bar', '.hero-container', '.scroll-down', '.deco-circle'], { clearProps: 'opacity' });
                    gsap.set([leaving, entering], { clearProps: 'opacity,transform,zIndex' }); leaving.style.transition = ''; entering.style.transition = '';
                }});
                return;
            }
            if (enteringIdx === 0) {
                gsap.killTweensOf([leaving, entering, '.hero-circle-bg', '.top-bar', '.hero-container', '.scroll-down', '.deco-circle']);
                entering.classList.add('fp-active'); leaving.style.transition = 'none'; entering.style.transition = 'none';
                entering.style.zIndex = '6'; leaving.style.zIndex = '5';
                gsap.set(entering, { opacity: 1, scale: 1 }); gsap.set('.hero-circle-bg', { scale: 8 }); gsap.set(['.top-bar', '.hero-container', '.scroll-down', '.deco-circle'], { opacity: 0 });
                gsap.set(leaving, { opacity: 1, scale: 1 });
                gsap.to('.hero-circle-bg', { scale: 1, duration: 0.7, ease: 'power2.inOut' });
                gsap.to(['.top-bar', '.hero-container', '.scroll-down', '.deco-circle'], { opacity: 1, duration: 0.7, delay: 0.1, ease: 'power2.out' });
                gsap.to(leaving, { opacity: 0, scale: 0.94, duration: 0.65, ease: 'power2.inOut', onComplete: () => {
                    leaving.classList.remove('fp-active'); gsap.set('.hero-circle-bg', { clearProps: 'transform' });
                    gsap.set(['.top-bar', '.hero-container', '.scroll-down', '.deco-circle'], { clearProps: 'opacity' });
                    gsap.set([leaving, entering], { clearProps: 'opacity,transform,zIndex' }); leaving.style.transition = ''; entering.style.transition = '';
                }});
                return;
            }
            gsap.killTweensOf([leaving, entering]);
            entering.classList.add('fp-active'); leaving.style.transition = 'none'; entering.style.transition = 'none';
            entering.style.zIndex = '6'; leaving.style.zIndex = '5';
            gsap.set(entering, { opacity: 0, scale: dir === 'up' ? 0.94 : 1.06 }); gsap.set(leaving, { opacity: 1, scale: 1 });
            gsap.to(entering, { opacity: 1, scale: 1, duration: 0.7, ease: 'power2.out' });
            gsap.to(leaving, { opacity: 0, scale: dir === 'up' ? 1.06 : 0.94, duration: 0.65, ease: 'power2.in', onComplete: () => {
                leaving.classList.remove('fp-active'); gsap.set([leaving, entering], { clearProps: 'opacity,transform,zIndex' });
                leaving.style.transition = ''; entering.style.transition = '';
            }});
        }

        function fpGoTo(idx) {
            if (!FP_ENABLED) return;
            if (fpTransitioning) return;
            if (idx < 0 || idx >= fpPages.length || idx === fpIdx) return;
            fpTransitioning = true;
            const leaving = fpPages[fpIdx]; const entering = fpPages[idx];
            entering.scrollTop = 0; fpRevealIn(entering);

            if (HAS_GSAP) { fpAnimateSwap(leaving, entering, idx > fpIdx ? 'up' : 'down'); } 
            else {
                const direction = idx > fpIdx ? 'up' : 'down';
                leaving.classList.remove('fp-active'); leaving.classList.add(direction === 'up' ? 'fp-leaving-up' : 'fp-leaving-down');
                entering.classList.add('fp-active');
            }

            const backToTopBtn = document.getElementById('backToTop');
            if (backToTopBtn) {
                if (idx === 4) { backToTopBtn.classList.add('show'); } 
                else { backToTopBtn.classList.remove('show'); }
            }

            const phone1Sel = '.how-phone-item:nth-child(1) .how-phone-img';
            const phone3Sel = '.how-phone-item:nth-child(3) .how-phone-img';
            
            if (idx === 3 && fpIdx === 2) PP_SLOT_CONFIG[3].sel = phone1Sel;
            else if (idx === 4 && fpIdx === 3) PP_SLOT_CONFIG[3].sel = phone3Sel;
            else if (idx === 3 && fpIdx === 4) PP_SLOT_CONFIG[3].sel = phone3Sel;
            else if (idx === 2 && fpIdx === 3) PP_SLOT_CONFIG[3].sel = phone1Sel;

            ppMeasureSlots();

            if (PP_ENABLED && ppEl) {
                if (fpIdx === 3 && idx === 4) {
                    const slot3 = ppSlots[3]; 
                    gsap.set(ppEl, { x: slot3.x, y: slot3.y, scale: slot3.width / 100, rotation: slot3.rotate });
                    ppEl.src = slot3.img; ppCurrentImg = slot3.img;
                } else if (fpIdx === 3 && idx === 2) {
                    const slot1 = ppSlots[3]; 
                    gsap.set(ppEl, { x: slot1.x, y: slot1.y, scale: slot1.width / 100, rotation: slot1.rotate });
                    ppEl.src = slot1.img; ppCurrentImg = slot1.img; 
                }
            }

            const p1 = document.querySelector(phone1Sel); const p3 = document.querySelector(phone3Sel);
            if (p1) p1.style.visibility = 'visible'; if (p3) p3.style.visibility = 'visible';

            if (idx === 3 || fpIdx === 3) {
                const activeP = document.querySelector(PP_SLOT_CONFIG[3].sel);
                if (activeP) activeP.style.visibility = 'hidden';
            }

            ppMoveTo(idx);

            setTimeout(() => {
                if (HAS_GSAP) gsap.killTweensOf(fpPages);
                fpPages.forEach((p, i) => {
                    p.classList.remove('fp-leaving-up', 'fp-leaving-down');
                    p.classList.toggle('fp-active', i === idx);
                    p.style.transition = ''; p.style.opacity = ''; p.style.transform = '';
                    p.style.zIndex = ''; p.style.clipPath = '';
                });
                fpResetReveals(leaving);
                fpIdx = idx; fpTransitioning = false;
            }, FP_DURATION);
        }

        let wheelLocked = false; let wheelIdleTimer = null;
        const armWheelUnlock = () => { clearTimeout(wheelIdleTimer); wheelIdleTimer = setTimeout(() => { wheelLocked = false; }, 90); };

        window.addEventListener('wheel', (e) => {
            if (!FP_ENABLED) return;
            const current = fpPages[fpIdx]; const down = e.deltaY > 0;
            const atBottom = current.scrollTop + current.clientHeight >= current.scrollHeight - 2;
            const atTop = current.scrollTop <= 2;
            if ((down && !atBottom) || (!down && !atTop)) { wheelLocked = true; armWheelUnlock(); return; }
            e.preventDefault(); armWheelUnlock();
            if (wheelLocked || fpTransitioning) return;
            if (Math.abs(e.deltaY) < 8) return;
            wheelLocked = true; fpGoTo(fpIdx + (down ? 1 : -1));
        }, { passive: false });

        let touchStartY = 0; let touchDeltaY = 0; const TOUCH_THRESHOLD = 60;
        window.addEventListener('touchstart', (e) => { if (!FP_ENABLED) return; touchStartY = e.touches[0].clientY; touchDeltaY = 0; }, { passive: true });
        window.addEventListener('touchmove', (e) => { if (!FP_ENABLED) return; touchDeltaY = touchStartY - e.touches[0].clientY; }, { passive: true });
        window.addEventListener('touchend', () => {
            if (!FP_ENABLED) return;
            if (Math.abs(touchDeltaY) < TOUCH_THRESHOLD) return;
            const current = fpPages[fpIdx]; const down = touchDeltaY > 0;
            const atBottom = current.scrollTop + current.clientHeight >= current.scrollHeight - 5;
            const atTop = current.scrollTop <= 5;
            if (down && atBottom) { fpGoTo(fpIdx + 1); } else if (!down && atTop) { fpGoTo(fpIdx - 1); }
        }, { passive: true });

        window.addEventListener('keydown', (e) => {
            if (!FP_ENABLED) return;
            const tag = (e.target.tagName || '').toLowerCase();
            if (['input', 'textarea', 'select'].includes(tag)) return;
            const cur = fpPages[fpIdx];
            const atBottom = cur.scrollTop + cur.clientHeight >= cur.scrollHeight - 2;
            const atTop = cur.scrollTop <= 2;
            const stepDown = e.key === 'PageDown' ? cur.clientHeight * 0.85 : 160;
            const stepUp = e.key === 'PageUp' ? cur.clientHeight * 0.85 : 160;
            if (['ArrowDown', 'PageDown', ' '].includes(e.key)) { e.preventDefault(); if (!atBottom) cur.scrollBy({ top: stepDown, behavior: 'smooth' }); else fpGoTo(fpIdx + 1); } 
            else if (['ArrowUp', 'PageUp'].includes(e.key)) { e.preventDefault(); if (!atTop) cur.scrollBy({ top: -stepUp, behavior: 'smooth' }); else fpGoTo(fpIdx - 1); } 
            else if (e.key === 'Home') { e.preventDefault(); fpGoTo(0); }
            else if (e.key === 'End') { e.preventDefault(); fpGoTo(fpPages.length - 1); }
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetSel = this.getAttribute('href');
                if (!targetSel || targetSel === '#') return;
                const target = document.querySelector(targetSel);
                if (!target) return;
                const idx = fpPages.indexOf(target);
                if (FP_ENABLED && idx >= 0) {
                    e.preventDefault(); fpGoTo(idx);
                } else if (!FP_ENABLED) {
                    e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        if (FP_ENABLED && fpPages[0]) {
            fpRevealIn(fpPages[0]);
        }

        const fpRemovePreload = () => document.documentElement.classList.remove('fp-preload');
        requestAnimationFrame(() => requestAnimationFrame(fpRemovePreload));
        window.addEventListener('load', () => setTimeout(fpRemovePreload, 60));

        document.addEventListener('mousemove', (e) => {
            if(!FP_ENABLED) return;
            const x = (e.clientX / window.innerWidth - 0.5) * 20;
            const y = (e.clientY / window.innerHeight - 0.5) * 20;
            const c2 = document.querySelector('.deco-circle.c2');
            const c3 = document.querySelector('.deco-circle.c3');
            if (c2) c2.style.transform = `translate(${x}px, ${y}px)`;
            if (c3) c3.style.transform = `translate(${-x}px, ${-y}px)`;
        });

        const backBtn = document.getElementById('backToTop');
        if (backBtn) {
            backBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (FP_ENABLED) { fpGoTo(0); } else { window.scrollTo({ top: 0, behavior: 'smooth' }); }
            });
        }

        // Mobile back-to-top button (section 5)
        const mobileBackBtn = document.getElementById('mobileBackToTop');
        if (mobileBackBtn) {
            mobileBackBtn.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // Responsive scroll animations (Intersection Observer)
        if (!FP_ENABLED) {
            // Instantly reveal hero elements on mobile so there's no delay
            if (fpPages[0]) {
                fpPages[0].querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-up, .reveal-scale').forEach(el => {
                    el.classList.add('visible');
                });
                const firstPhone = fpPages[0].querySelector('.iphone-mockup');
                if (firstPhone) firstPhone.classList.add('visible-phone');
            }

            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Reveal child elements with animate/reveal classes
                        entry.target.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-up, .reveal-scale').forEach(el => {
                            el.classList.add('visible');
                        });
                        // Reveal phone mockup specifically
                        const phone = entry.target.querySelector('.iphone-mockup, .about-phone, .services-phone, .final-phone');
                        if (phone) {
                            phone.classList.add('visible-phone');
                        }
                        // Unobserve once revealed to keep performance high
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                root: null,
                threshold: 0.1, // Trigger when 10% of the section is visible
                rootMargin: '0px 0px -50px 0px' // Trigger slightly before it fully enters viewport
            });

            // Observe all main sections (excluding the first one since it's already manually revealed)
            fpPages.slice(1).forEach(section => {
                revealObserver.observe(section);
            });

            // Setup slider navigation
            function setupSlider(containerSelector, dotsSelector, itemSelector) {
                const container = document.querySelector(containerSelector);
                const dots = document.querySelectorAll(dotsSelector);
                if (!container || !dots.length) return;

                // Update active dot on scroll
                container.addEventListener('scroll', () => {
                    const firstItem = container.querySelector(itemSelector);
                    if (!firstItem) return;
                    const cardWidth = firstItem.clientWidth + 20; // card width + gap
                    const index = Math.round(container.scrollLeft / cardWidth);
                    dots.forEach((dot, i) => {
                        dot.classList.toggle('active', i === index);
                    });
                });

                // Click to scroll to dot's item
                dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => {
                        const firstItem = container.querySelector(itemSelector);
                        if (!firstItem) return;
                        const cardWidth = firstItem.clientWidth + 20;
                        container.scrollTo({
                            left: index * cardWidth,
                            behavior: 'smooth'
                        });
                    });
                });
            }

            setupSlider('.services-cards', '#services-dots .dot', '.service-card');
            setupSlider('.how-phones-row', '#how-dots .dot', '.how-phone-item');
        }
    </script>
</body>
</html>