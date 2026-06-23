<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateau Smart City — État civil simplifié</title>

    <link rel="shortcut icon" href="{{ asset('assets/assets/img/logo plateau.png') }}" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-blue: #d7e5fa;
            --primary-dark: #1f4083;
            --primary: #1977cc;
            --primary-light: #4facfe;
            --success: #01b574;
            --success-dark: #019560;
            --text-navy: #1a2e5c;
            --text-grey: #6b7280;
            --white: #ffffff;
            --circle-blue: #b8d3f0;
            --circle-deep: #7ba9d6;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-blue);
            color: var(--text-navy);
            overflow-x: hidden;
        }

        /* ─────────────── HERO SECTION ─────────────── */
        .hero {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            padding: 32px 64px 80px;
        }

        /* Cercles décoratifs en arrière-plan */
        .hero::before,
        .hero::after,
        .deco-circle {
            content: '';
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        /* Grand cercle bleu clair (derrière le téléphone) */
        .hero::before {
            width: 900px;
            height: 900px;
            background: radial-gradient(circle, rgba(123, 169, 214, 0.25) 0%, rgba(123, 169, 214, 0) 70%);
            right: -250px;
            top: 200px;
        }

        /* Cercle bleu profond (gros, à droite) */
        .hero::after {
            width: 820px;
            height: 820px;
            background: #5e8ec5;
            opacity: 0.25;
            right: -260px;
            top: 250px;
        }

        .deco-circle.c1 {
            width: 620px;
            height: 620px;
            background: var(--primary);
            opacity: 0.18;
            right: -100px;
            top: 380px;
            filter: blur(2px);
        }

        .deco-circle.c2 {
            width: 200px;
            height: 200px;
            background: var(--primary-light);
            opacity: 0.15;
            left: 40%;
            top: 60px;
            animation: floatCircle 6s ease-in-out infinite;
        }

        .deco-circle.c3 {
            width: 100px;
            height: 100px;
            background: var(--success);
            opacity: 0.12;
            left: 38%;
            bottom: 100px;
            animation: floatCircle 4s ease-in-out infinite reverse;
        }

        @keyframes floatCircle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* ─────────────── HEADER ─────────────── */
        .top-bar {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 60px;
            animation: fadeInDown 0.8s ease-out;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1;
        }

        .logo-text .name {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary-dark);
            letter-spacing: 0.5px;
        }

        .logo-text .subtitle {
            font-size: 11px;
            font-weight: 700;
            color: var(--success);
            letter-spacing: 4px;
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .logo-text .subtitle::before {
            content: '';
            display: inline-block;
            width: 18px;
            height: 3px;
            background: var(--success);
        }

        .nav-buttons {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .btn-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }

        .btn-pill.outline {
            background: transparent;
            color: var(--success);
            border-color: var(--success);
        }

        .btn-pill.outline:hover {
            background: var(--success);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(1, 181, 116, 0.25);
        }

        .btn-pill.solid {
            background: var(--success);
            color: white;
        }

        .btn-pill.solid:hover {
            background: var(--success-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(1, 181, 116, 0.3);
        }

        /* ─────────────── CONTENU HERO ─────────────── */
        .hero-container {
            position: relative;
            z-index: 5;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 60px;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Côté gauche : Texte */
        .hero-left {
            animation: fadeInLeft 1s ease-out 0.2s both;
        }

        .hero-title {
            font-size: 92px;
            font-weight: 800;
            line-height: 0.95;
            margin-bottom: 24px;
            letter-spacing: -2px;
        }

        .hero-title .word-1 {
            color: var(--primary-dark);
            display: block;
        }

        .hero-title .word-2 {
            color: var(--success);
            display: block;
            font-weight: 900;
            letter-spacing: 4px;
        }

        .hero-description {
            font-size: 16px;
            line-height: 1.6;
            color: var(--text-grey);
            max-width: 480px;
            margin-bottom: 36px;
        }

        /* Features (3 colonnes) */
        .features {
            display: grid;
            grid-template-columns: repeat(3, auto);
            gap: 32px;
            margin-bottom: 36px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: white;
            box-shadow: 0 4px 12px rgba(31, 64, 131, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-size: 18px;
            flex-shrink: 0;
        }

        .feature-text {
            font-size: 13px;
            color: var(--text-navy);
            line-height: 1.3;
        }

        .feature-text strong {
            font-weight: 700;
            display: block;
        }

        /* Boutons download */
        .download-buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 40px;
        }

        .btn-store {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background: #000;
            color: white;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-store:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .btn-store i {
            font-size: 32px;
        }

        .btn-store .store-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .btn-store .store-text small {
            font-size: 10px;
            opacity: 0.8;
        }

        .btn-store .store-text span {
            font-size: 16px;
            font-weight: 700;
        }

        /* Témoignage / Trust */
        .trust-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .avatars {
            display: flex;
        }

        .avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
            background: var(--primary-light);
            margin-left: -10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .avatar:first-child { margin-left: 0; }

        .avatar-fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
        }

        .trust-text {
            display: flex;
            flex-direction: column;
            line-height: 1.3;
        }

        .trust-text .count {
            font-weight: 700;
            color: var(--text-navy);
            font-size: 14px;
        }

        .trust-text .subtitle {
            color: var(--text-grey);
            font-size: 13px;
        }

        .stars {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
        }

        .stars-icons {
            color: var(--success);
            font-size: 13px;
            letter-spacing: 2px;
        }

        .rating {
            font-weight: 700;
            color: var(--primary-dark);
            font-size: 13px;
        }

        /* Côté droit : iPhone mockup */
        .hero-right {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            animation: fadeInRight 1.2s ease-out 0.4s both;
        }

        .iphone-mockup {
            max-width: 100%;
            height: auto;
            width: 580px;
            filter: drop-shadow(0 30px 60px rgba(31, 64, 131, 0.25));
            transform: rotate(-3deg);
            animation: floatPhone 6s ease-in-out infinite;
        }

        @keyframes floatPhone {
            0%, 100% { transform: rotate(-3deg) translateY(0); }
            50%      { transform: rotate(-3deg) translateY(-15px); }
        }

        /* ─────────────── SCROLL DOWN BUTTON ─────────────── */
        .scroll-down {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(25, 119, 204, 0.3);
            animation: bounce 2s infinite;
            z-index: 10;
            transition: all 0.3s;
        }

        .scroll-down:hover {
            background: var(--primary-dark);
            transform: translateX(-50%) scale(1.1);
        }

        .scroll-down i {
            font-size: 18px;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
            40% { transform: translateX(-50%) translateY(-12px); }
            60% { transform: translateX(-50%) translateY(-6px); }
        }

        /* ─────────────── ANIMATIONS ─────────────── */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ─────────────── RESPONSIVE ─────────────── */
        @media (max-width: 1200px) {
            .hero-title { font-size: 72px; }
            .iphone-mockup { width: 480px; }
        }

        @media (max-width: 992px) {
            .hero {
                padding: 24px 32px 80px;
            }
            .hero-container {
                grid-template-columns: 1fr;
                gap: 40px;
                text-align: center;
            }
            .hero-title { font-size: 60px; }
            .features {
                justify-content: center;
                grid-template-columns: repeat(auto-fit, minmax(160px, auto));
            }
            .download-buttons,
            .trust-section {
                justify-content: center;
            }
            .iphone-mockup { width: 400px; transform: rotate(0); animation: none; }
        }

        @media (max-width: 768px) {
            .hero {
                padding: 20px 20px 80px;
            }
            .top-bar {
                flex-wrap: wrap;
                gap: 16px;
                margin-bottom: 40px;
            }
            .logo-text .name { font-size: 22px; }
            .hero-title { font-size: 44px; }
            .hero-description { font-size: 14px; }
            .features {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            .feature-item { justify-content: center; }
            .download-buttons {
                flex-direction: column;
                align-items: center;
            }
            .iphone-mockup { width: 280px; }
            .btn-pill { padding: 10px 18px; font-size: 13px; }
        }

        /* ════════════════════════════════════════════════════════
           SECTION "À PROPOS" — fond bleu nuit avec city skyline
           ════════════════════════════════════════════════════════ */
        .about-section {
            position: relative;
            min-height: 100vh;
            padding: 100px 64px;
            background: url('{{ asset("assets/landing/about-background.png") }}') center/cover no-repeat,
                        linear-gradient(180deg, #0a2954 0%, #061a3a 100%);
            color: white;
            overflow: hidden;
        }

        .about-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(10, 41, 84, 0.6) 0%, rgba(6, 26, 58, 0.5) 100%);
            z-index: 0;
        }

        .about-container {
            position: relative;
            z-index: 5;
            display: grid;
            grid-template-columns: 1fr 1.2fr 1fr;
            gap: 60px;
            align-items: center;
            max-width: 1500px;
            margin: 0 auto;
        }

        /* Eyebrow (label NOTRE VISION / A PROPOS) */
        .eyebrow {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .eyebrow .line {
            width: 38px;
            height: 4px;
            background: var(--success);
            border-radius: 2px;
        }

        .eyebrow .label {
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.9);
        }

        .eyebrow.right {
            justify-content: flex-end;
        }

        .eyebrow.right .line {
            order: 2;
        }

        /* Titres des 2 colonnes about */
        .about-title {
            font-size: 40px;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -1px;
            margin-bottom: 32px;
            color: white;
        }

        .about-title .accent {
            color: var(--success);
        }

        .about-title.right { font-size: 33px; }

        .about-text {
            font-size: 14px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 16px;
        }

        .about-text .accent {
            color: var(--success);
            font-weight: 600;
        }

        /* COLONNE GAUCHE : 4 mini-cards (Proche / Transparente / Rapide / À l'écoute) */
        .mini-cards {
            display: flex;
            gap: 12px;
            margin-top: 36px;
            flex-wrap: wrap;
        }

        .mini-card {
            background: rgba(217, 217, 217, 0.08);
            border: 1px solid rgba(217, 217, 217, 0.3);
            border-radius: 19px;
            padding: 12px 8px;
            width: 82px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: default;
        }

        .mini-card:hover {
            background: rgba(217, 217, 217, 0.15);
            border-color: var(--success);
            transform: translateY(-4px);
        }

        .mini-card .icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1977cc, #4facfe);
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }

        .mini-card .label {
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.3;
        }

        /* COLONNE CENTRE : iPhone tourné */
        .about-phone-wrap {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .about-phone {
            width: 480px;
            max-width: 100%;
            transform: rotate(8deg);
            filter: drop-shadow(0 30px 80px rgba(0, 0, 0, 0.4));
            animation: floatPhoneAbout 7s ease-in-out infinite;
        }

        @keyframes floatPhoneAbout {
            0%, 100% { transform: rotate(8deg) translateY(0); }
            50%      { transform: rotate(8deg) translateY(-20px); }
        }

        /* Halo lumineux derrière iPhone */
        .about-phone-wrap::before {
            content: '';
            position: absolute;
            width: 620px;
            height: 620px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79, 172, 254, 0.35) 0%, rgba(79, 172, 254, 0) 65%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
        }

        /* Cercles concentriques sous le phone */
        .about-phone-wrap::after {
            content: '';
            position: absolute;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.08);
            top: 60%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow:
                0 0 0 80px rgba(255, 255, 255, 0.04),
                0 0 0 160px rgba(255, 255, 255, 0.02);
        }

        /* COLONNE DROITE : Carte de sécurité + bouton */
        .security-card {
            margin-top: 28px;
            padding: 24px;
            background: rgba(217, 217, 217, 0.08);
            border: 1px solid rgba(217, 217, 217, 0.3);
            border-radius: 20px;
            display: flex;
            gap: 16px;
            align-items: center;
            transition: all 0.3s ease;
        }

        .security-card:hover {
            background: rgba(217, 217, 217, 0.12);
            border-color: var(--success);
        }

        .security-card .shield-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary-dark);
            flex-shrink: 0;
        }

        .security-card .security-text {
            font-size: 13px;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.85);
        }

        .security-card .security-text .accent {
            color: var(--success);
            font-weight: 700;
        }

        .btn-discover {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 24px;
            padding: 18px 32px;
            background: white;
            color: var(--primary-dark);
            border-radius: 999px;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-discover:hover {
            background: var(--success);
            color: white;
            transform: translateX(6px);
            box-shadow: 0 12px 30px rgba(1, 181, 116, 0.3);
        }

        .btn-discover .arrow-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(31, 64, 131, 0.1);
            transition: all 0.3s ease;
        }

        .btn-discover:hover .arrow-icon {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(45deg);
        }

        /* Reveal animations on scroll */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-left {
            opacity: 0;
            transform: translateX(-40px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .reveal-left.visible {
            opacity: 1;
            transform: translateX(0);
        }

        .reveal-right {
            opacity: 0;
            transform: translateX(40px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .reveal-right.visible {
            opacity: 1;
            transform: translateX(0);
        }

        /* Responsive about section */
        @media (max-width: 1200px) {
            .about-phone { width: 380px; }
            .about-title { font-size: 32px; }
            .about-title.right { font-size: 28px; }
        }

        @media (max-width: 992px) {
            .about-section { padding: 60px 32px; }
            .about-container {
                grid-template-columns: 1fr;
                gap: 60px;
                text-align: center;
            }
            .eyebrow,
            .eyebrow.right { justify-content: center; }
            .mini-cards { justify-content: center; }
            .security-card { text-align: left; }
            .about-phone { width: 320px; transform: rotate(0); animation: none; }
            .about-phone-wrap::after { display: none; }
        }

        @media (max-width: 768px) {
            .about-section { padding: 50px 20px; }
            .about-title { font-size: 26px; }
            .about-title.right { font-size: 24px; }
            .about-text { font-size: 13px; }
            .about-phone { width: 240px; }
            .btn-discover { padding: 14px 24px; font-size: 14px; }
        }

        /* ════════════════════════════════════════════════════════
           SECTION 3 — "Des services pensés Pour vous simplifier la vie"
           ════════════════════════════════════════════════════════ */
        .services-section {
            position: relative;
            min-height: 100vh;
            padding: 80px 64px 80px;
            background: url('{{ asset("assets/landing/services-bg.png") }}') top center/cover no-repeat,
                        linear-gradient(180deg, #0f2d5e 0%, #0a2150 100%);
            color: white;
            overflow: hidden;
        }

        .services-top {
            position: relative;
            z-index: 5;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: start;
            max-width: 1400px;
            margin: 0 auto 60px;
        }

        .services-title {
            font-size: 38px;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -1px;
            margin-bottom: 24px;
            color: white;
        }

        .services-title .accent {
            color: #3271d7;
        }

        .services-text {
            font-size: 14px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.85);
            max-width: 440px;
        }

        .services-phone-side {
            position: relative;
            min-height: 400px;
        }

        .services-phone {
            position: absolute;
            top: -50px;
            right: -100px;
            width: 540px;
            max-width: 100%;
            transform: rotate(12deg);
            filter: drop-shadow(0 30px 70px rgba(0, 0, 0, 0.4));
            animation: floatPhoneServices 7s ease-in-out infinite;
        }

        @keyframes floatPhoneServices {
            0%, 100% { transform: rotate(12deg) translateY(0); }
            50%      { transform: rotate(12deg) translateY(-18px); }
        }

        /* 5 service cards alignées en bas avec wave effect */
        .services-cards-wrap {
            position: relative;
            z-index: 5;
            background: white;
            border-radius: 70% 70% 0 0 / 90px 90px 0 0;
            padding: 90px 40px 60px;
            margin: 0 -64px -80px;
        }

        .services-cards {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .service-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-8px);
        }

        .service-card .circle {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: rgba(15, 77, 142, 0.05);
            border: 3px solid #0f4d8e;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: #0f4d8e;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }

        .service-card:hover .circle {
            background: #0f4d8e;
            color: white;
            box-shadow: 0 8px 24px rgba(0, 122, 255, 0.3);
        }

        .service-card .name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-navy);
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .service-card .name .accent {
            color: #0f4d8e;
        }

        .service-card .underline {
            width: 33px;
            height: 3px;
            background: var(--success);
            border-radius: 2px;
            margin: 12px 0 14px;
        }

        .service-card .desc {
            font-size: 13px;
            color: #1c1826;
            line-height: 1.4;
            max-width: 180px;
        }

        /* ════════════════════════════════════════════════════════
           SECTION 4 — "Comment ça marche ?" — 4 iPhones tilted
           ════════════════════════════════════════════════════════ */
        .how-section {
            position: relative;
            padding: 80px 64px 120px;
            background: linear-gradient(180deg, #ffffff 0%, #f3f8ff 100%);
            overflow: hidden;
        }

        .how-section::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 400px;
            background: linear-gradient(180deg, transparent 0%, var(--primary-dark) 100%);
            clip-path: polygon(0 30%, 50% 0, 100% 30%, 100% 100%, 0 100%);
            z-index: 0;
        }

        .how-title {
            font-size: 56px;
            font-weight: 800;
            text-align: center;
            color: var(--text-navy);
            margin-bottom: 80px;
            letter-spacing: -1.5px;
            position: relative;
            z-index: 5;
        }

        .how-title .accent {
            color: #0f4d8e;
        }

        .how-phones-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 5;
        }

        .how-phone-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .how-phone-img {
            width: 200px;
            height: auto;
            margin-bottom: 30px;
            filter: drop-shadow(0 25px 50px rgba(0, 0, 0, 0.3));
            transition: transform 0.4s ease;
        }

        .how-phone-item:nth-child(1) .how-phone-img { transform: rotate(-6deg); }
        .how-phone-item:nth-child(2) .how-phone-img { transform: rotate(0deg); }
        .how-phone-item:nth-child(3) .how-phone-img { transform: rotate(4deg); }
        .how-phone-item:nth-child(4) .how-phone-img { transform: rotate(8deg); }

        .how-phone-item:hover .how-phone-img {
            transform: rotate(0) translateY(-10px) scale(1.05);
        }

        .how-step-line {
            width: 80%;
            height: 2px;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.2));
            margin-bottom: 16px;
        }

        .how-step-number {
            font-size: 42px;
            font-weight: 700;
            color: #9ec8fe;
            line-height: 1;
            margin-bottom: 8px;
        }

        .how-step-name {
            font-size: 24px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .how-step-desc {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.4;
            max-width: 200px;
        }

        /* ════════════════════════════════════════════════════════
           SECTION 5 — "Le Plateau plus proche que jamais" + Footer
           ════════════════════════════════════════════════════════ */
        .final-section {
            position: relative;
            padding: 80px 64px 0;
            background: url('{{ asset("assets/landing/final-bg.png") }}') center/cover no-repeat,
                        linear-gradient(180deg, #cce0ff 0%, #94c4fc 100%);
            overflow: hidden;
        }

        .final-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(220, 235, 255, 0.4) 0%, rgba(180, 215, 255, 0.3) 100%);
            z-index: 0;
        }

        .final-container {
            position: relative;
            z-index: 5;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .final-title {
            font-size: 72px;
            font-weight: 800;
            line-height: 0.95;
            letter-spacing: -2px;
            margin-bottom: 24px;
        }

        .final-title .line-1 { color: #0f4d8e; display: block; }
        .final-title .line-2 { color: var(--success); display: block; }
        .final-title .line-3 { color: #0f4d8e; display: block; }

        .final-text {
            font-size: 15px;
            color: #1c1826;
            line-height: 1.5;
            margin-bottom: 40px;
            max-width: 480px;
        }

        /* 4 mini stats avec icônes */
        .final-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            max-width: 600px;
        }

        .final-stat {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
        }

        .final-stat .icon-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: white;
            box-shadow: 0 4px 10px rgba(15, 77, 142, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f4d8e;
            font-size: 18px;
            margin-bottom: 12px;
        }

        .final-stat .stat-name {
            font-size: 11px;
            font-weight: 700;
            color: #1c1826;
            margin-bottom: 4px;
        }

        .final-stat .stat-desc {
            font-size: 9px;
            color: #1c1826;
            line-height: 1.3;
        }

        /* iPhone droite */
        .final-phone-wrap {
            position: relative;
            display: flex;
            justify-content: center;
        }

        .final-phone {
            width: 480px;
            max-width: 100%;
            transform: rotate(-6deg);
            filter: drop-shadow(0 30px 60px rgba(31, 64, 131, 0.3));
            animation: floatFinalPhone 6s ease-in-out infinite;
        }

        @keyframes floatFinalPhone {
            0%, 100% { transform: rotate(-6deg) translateY(0); }
            50%      { transform: rotate(-6deg) translateY(-15px); }
        }

        /* Download CTA bar (au bas de section 5) */
        .download-bar {
            position: relative;
            z-index: 5;
            background: var(--primary-dark);
            border-radius: 90% 90% 0 0 / 80px 80px 0 0;
            margin: 100px -64px 0;
            padding: 80px 64px 50px;
            color: white;
            text-align: center;
        }

        .download-bar h3 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .download-bar p {
            font-size: 15px;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .download-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .download-trust {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-right: 30px;
            border-right: 2px solid rgba(255, 255, 255, 0.2);
        }

        .download-trust .avatars {
            display: flex;
        }

        .download-trust .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid white;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            margin-left: -10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 12px;
        }

        .download-trust .avatar:first-child { margin-left: 0; }

        .download-trust .info {
            text-align: left;
            font-size: 12px;
        }

        .download-trust .info strong { display: block; font-size: 13px; }
        .download-trust .info .rating { color: var(--success); font-weight: 700; }

        /* Footer */
        .footer {
            background: var(--primary-dark);
            padding: 30px 64px 40px;
            color: white;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
            align-items: center;
        }

        .footer-left .logo-text .name { color: white; }
        .footer-left .footer-tagline {
            font-size: 13px;
            opacity: 0.85;
            margin-top: 8px;
            line-height: 1.4;
        }

        .footer-social {
            display: flex;
            justify-content: center;
            gap: 18px;
        }

        .footer-social a {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
        }

        .footer-social a:hover {
            background: var(--success);
            transform: translateY(-2px);
        }

        .footer-right {
            text-align: right;
            font-size: 13px;
            opacity: 0.9;
            line-height: 1.5;
        }

        /* ════════════════════════════════════════════════════════
           TRANSITIONS DE SECTION (snap scroll + smooth)
           ════════════════════════════════════════════════════════ */
        html {
            scroll-behavior: smooth;
        }

        /* Indicateur de progression (dots latéraux) */
        .scroll-nav {
            position: fixed;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 100;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .scroll-nav-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .scroll-nav-dot:hover {
            background: var(--primary);
            transform: scale(1.2);
        }

        .scroll-nav-dot.active {
            background: var(--primary);
            transform: scale(1.4);
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Animations supplémentaires */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to   { opacity: 1; transform: scale(1); }
        }

        .reveal-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .reveal-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-scale {
            opacity: 0;
            transform: scale(0.9);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .reveal-scale.visible {
            opacity: 1;
            transform: scale(1);
        }

        /* Stagger delay pour cards */
        .stagger-1 { transition-delay: 0.1s; }
        .stagger-2 { transition-delay: 0.2s; }
        .stagger-3 { transition-delay: 0.3s; }
        .stagger-4 { transition-delay: 0.4s; }
        .stagger-5 { transition-delay: 0.5s; }

        /* Responsive sections finales */
        @media (max-width: 1200px) {
            .services-title { font-size: 30px; }
            .services-phone { width: 420px; }
            .how-title { font-size: 44px; }
            .how-phone-img { width: 160px; }
            .final-title { font-size: 56px; }
            .final-phone { width: 380px; }
        }

        @media (max-width: 992px) {
            .services-section { padding: 60px 32px; }
            .services-top { grid-template-columns: 1fr; }
            .services-phone-side { min-height: 0; }
            .services-phone { position: relative; top: 0; right: 0; transform: rotate(0); margin: 30px auto; display: block; animation: none; }
            .services-cards-wrap { margin: 30px -32px -60px; padding: 60px 24px 50px; border-radius: 50% 50% 0 0 / 60px 60px 0 0; }
            .services-cards { grid-template-columns: repeat(2, 1fr); }

            .how-section { padding: 60px 32px 100px; }
            .how-title { font-size: 36px; margin-bottom: 50px; }
            .how-phones-row { grid-template-columns: repeat(2, 1fr); gap: 50px 30px; }

            .final-section { padding: 60px 32px 0; }
            .final-container { grid-template-columns: 1fr; text-align: center; }
            .final-title { font-size: 48px; }
            .final-stats { grid-template-columns: repeat(2, 1fr); margin: 0 auto; }
            .final-stat { align-items: center; text-align: center; }
            .final-phone { width: 320px; transform: rotate(0); animation: none; }

            .download-bar { margin: 60px -32px 0; padding: 60px 32px 40px; border-radius: 50% 50% 0 0 / 50px 50px 0 0; }
            .download-bar h3 { font-size: 22px; }

            .footer { padding: 30px 32px; }
            .footer-container { grid-template-columns: 1fr; text-align: center; gap: 20px; }
            .footer-right { text-align: center; }

            .scroll-nav { display: none; }
        }

        @media (max-width: 768px) {
            .services-cards { grid-template-columns: 1fr; gap: 20px; }
            .service-card .circle { width: 100px; height: 100px; font-size: 38px; }

            .how-phones-row { grid-template-columns: 1fr; }
            .how-phone-img { width: 200px; }

            .final-title { font-size: 36px; }
            .final-stats { grid-template-columns: 1fr 1fr; gap: 16px; }

            .download-bar h3 { font-size: 18px; }
            .download-row { flex-direction: column; gap: 20px; }
            .download-trust { border-right: none; padding-right: 0; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.2); }
        }
        
    </style>
</head>

<body>

<section class="hero">
    <!-- Cercles décoratifs -->
    <div class="deco-circle c1"></div>
    <div class="deco-circle c2"></div>
    <div class="deco-circle c3"></div>

    <!-- HEADER : Logo + Boutons -->
    <header class="top-bar">
        <div class="logo">
            <div class="logo-text">
                <span class="name">PLATEAU</span>
                <span class="subtitle">SMART CITY</span>
            </div>
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
                    <i class="fab fa-google-play"></i>
                    <div class="store-text">
                        <small>Disponible sur</small>
                        <span>Google Play</span>
                    </div>
                </a>
            </div>

            <!-- Trust / Témoignage -->
            <div class="trust-section">
                <div class="avatars">
                    <div class="avatar avatar-fallback" style="background: linear-gradient(135deg, #f59e0b, #ef4444);">A</div>
                    <div class="avatar avatar-fallback" style="background: linear-gradient(135deg, #3b82f6, #8b5cf6);">B</div>
                    <div class="avatar avatar-fallback" style="background: linear-gradient(135deg, #10b981, #059669);">C</div>
                    <div class="avatar avatar-fallback" style="background: linear-gradient(135deg, #ec4899, #be185d);">D</div>
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
            <img src="{{ asset('assets/landing/iphone-mockup.png') }}"
                 alt="Aperçu de l'application Plateau"
                 class="iphone-mockup"
                 onerror="this.style.display='none'; this.parentElement.innerHTML += '<div style=&quot;padding:60px;color:#999;text-align:center;&quot;>📱 Mockup iPhone</div>';">
        </div>

    </div>

    <!-- Scroll down -->
    <a href="#next-section" class="scroll-down" aria-label="Défiler vers le bas">
        <i class="fas fa-chevron-down"></i>
    </a>
</section>

<!-- ════════════════════════════════════════════════════════
     SECTION "À PROPOS" — Bleu nuit + city skyline + iPhone
     ════════════════════════════════════════════════════════ -->
<section id="next-section" class="about-section">
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

            <!-- 4 mini-cards : Proche / Transparente / Rapide / À l'écoute -->
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
            <img src="{{ asset('assets/landing/iphone-mockup.png') }}"
                 alt="Plateau Apps mobile"
                 class="about-phone"
                 onerror="this.style.display='none';">
        </div>

        <!-- COLONNE DROITE : À Propos + Carte sécurité + CTA -->
        <div class="about-right reveal-right">
            <div class="eyebrow right">
                <span class="label">A PROPOS</span>
                <span class="line"></span>
            </div>

            <h2 class="about-title right" style="text-align: right;">
                Plateau Apps<br>
                Votre mairie <span class="accent">dans</span><br>
                <span class="accent">la poche.</span>
            </h2>

            <p class="about-text" style="text-align: right;">
                Plateau Apps est le portail officiel conçu pour
                dématérialiser vos démarches citoyennes les
                plus essentielles.
            </p>

            <p class="about-text" style="text-align: right;">
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
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="security-text">
                    Vos données sont protégées<br>
                    à <span class="accent">100%</span> et traitées avec<br>
                    le plus haut niveau de sécurité.
                </div>
            </div>

            <!-- Bouton CTA -->
            <div style="text-align: right; margin-top: 24px;">
                <a href="#services" class="btn-discover">
                    Découvrir Plateau Apps
                    <span class="arrow-icon">
                        <i class="fas fa-arrow-right"></i>
                    </span>
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
        <!-- Texte gauche -->
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
        </div>

        <!-- iPhone droite -->
        <div class="services-phone-side reveal-right">
            <img src="{{ asset('assets/landing/iphone-mockup.png') }}"
                 alt="Plateau Apps services"
                 class="services-phone"
                 onerror="this.style.display='none';">
        </div>
    </div>

    <!-- 5 service cards avec wave effect -->
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
    </div>
</section>

<!-- ════════════════════════════════════════════════════════
     SECTION 4 — "Comment ça marche ?" — 4 iPhones tilted
     ════════════════════════════════════════════════════════ -->
<section id="how" class="how-section">
    <h2 class="how-title reveal-up">
        Comment ça <span class="accent">marche ?</span>
    </h2>

    <div class="how-phones-row">
        <div class="how-phone-item reveal-up stagger-1">
            <img src="{{ asset('assets/landing/iphone-mockup.png') }}" alt="Acte de naissance" class="how-phone-img">
            <div class="how-step-line"></div>
            <div class="how-step-number">01</div>
            <div class="how-step-name">Acte de Naissance</div>
            <div class="how-step-desc">Demande et copie conforme en ligne</div>
        </div>

        <div class="how-phone-item reveal-up stagger-2">
            <img src="{{ asset('assets/landing/iphone-historique.png') }}" alt="Historique" class="how-phone-img">
            <div class="how-step-line"></div>
            <div class="how-step-number">02</div>
            <div class="how-step-name">Acte de Mariage</div>
            <div class="how-step-desc">Planification et documents d'union</div>
        </div>

        <div class="how-phone-item reveal-up stagger-3">
            <img src="{{ asset('assets/landing/iphone-rechercher.png') }}" alt="Décès" class="how-phone-img">
            <div class="how-step-line"></div>
            <div class="how-step-number">03</div>
            <div class="how-step-name">Acte de Décès</div>
            <div class="how-step-desc">Assistance administrative</div>
        </div>

        <div class="how-phone-item reveal-up stagger-4">
            <img src="{{ asset('assets/landing/iphone-rdv.png') }}" alt="Rendez-vous" class="how-phone-img">
            <div class="how-step-line"></div>
            <div class="how-step-number">04</div>
            <div class="how-step-name">Rendez-vous</div>
            <div class="how-step-desc">Planification en mairie en 2 min</div>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════════════════
     SECTION 5 — "Le Plateau plus proche que jamais" + Footer
     ════════════════════════════════════════════════════════ -->
<section id="final" class="final-section">
    <div class="final-container">
        <!-- Texte gauche -->
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
                    <div class="stat-desc">Évitez les files d'attente et les déplacements.</div>
                </div>

                <div class="final-stat">
                    <div class="icon-circle"><i class="fas fa-shield-alt"></i></div>
                    <div class="stat-name">Services officiel</div>
                    <div class="stat-desc">Vos données sont protégées.</div>
                </div>

                <div class="final-stat">
                    <div class="icon-circle"><i class="fas fa-mobile-alt"></i></div>
                    <div class="stat-name">Simple à utiliser</div>
                    <div class="stat-desc">Une expérience claire et intuitive.</div>
                </div>

                <div class="final-stat">
                    <div class="icon-circle"><i class="fas fa-leaf"></i></div>
                    <div class="stat-name">Eco-responsable</div>
                    <div class="stat-desc">Moins de papier, plus de planète.</div>
                </div>
            </div>
        </div>

        <!-- iPhone droite -->
        <div class="final-phone-wrap reveal-right">
            <img src="{{ asset('assets/landing/iphone-rdv.png') }}"
                 alt="Plateau Apps RDV"
                 class="final-phone"
                 onerror="this.style.display='none';">
        </div>
    </div>

    <!-- Download bar bleu -->
    <div class="download-bar">
        <h3>Téléchargez Plateau Apps</h3>
        <p>et simplifiez votre quotidien dès aujourd'hui.</p>
        <div class="download-row">
            <div class="download-trust">
                <div class="avatars">
                    <div class="avatar">A</div>
                    <div class="avatar">B</div>
                    <div class="avatar">C</div>
                    <div class="avatar">D</div>
                </div>
                <div class="info">
                    <strong>+12000 habitants</strong>
                    utilisent déjà Plateau Apps
                    <div><span class="rating">★★★★★ 4.8/5</span></div>
                </div>
            </div>
            <a href="#" class="btn-store">
                <i class="fab fa-apple"></i>
                <div class="store-text">
                    <small>Télécharger</small>
                    <span>l'App Store</span>
                </div>
            </a>
            <a href="#" class="btn-store">
                <i class="fab fa-google-play"></i>
                <div class="store-text">
                    <small>Disponible sur</small>
                    <span>Google Play</span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-left">
            <div class="logo">
                <div class="logo-text">
                    <span class="name">PLATEAU</span>
                    <span class="subtitle" style="color: var(--success);">SMART CITY</span>
                </div>
            </div>
            <div class="footer-tagline">
                Une administration moderne,<br>
                proche de vous, pour vous.
            </div>
        </div>

        <div class="footer-social">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>

        <div class="footer-right">
            Service officiel de la Mairie du Plateau<br>
            © 2026 Tous droits réservés.
        </div>
    </div>
</footer>

<!-- Indicateur de progression latéral -->
<nav class="scroll-nav" aria-label="Navigation des sections">
    <div class="scroll-nav-dot active" data-target=".hero" title="Accueil"></div>
    <div class="scroll-nav-dot" data-target="#next-section" title="À propos"></div>
    <div class="scroll-nav-dot" data-target="#services" title="Services"></div>
    <div class="scroll-nav-dot" data-target="#how" title="Comment ça marche"></div>
    <div class="scroll-nav-dot" data-target="#final" title="Téléchargement"></div>
</nav>

<script>
    // Smooth scroll vers la section suivante
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Parallax léger sur les cercles décoratifs
    document.addEventListener('mousemove', (e) => {
        const x = (e.clientX / window.innerWidth - 0.5) * 20;
        const y = (e.clientY / window.innerHeight - 0.5) * 20;
        const c2 = document.querySelector('.deco-circle.c2');
        const c3 = document.querySelector('.deco-circle.c3');
        if (c2) c2.style.transform = `translate(${x}px, ${y}px)`;
        if (c3) c3.style.transform = `translate(${-x}px, ${-y}px)`;
    });

    // ═══════════════════════════════════════════════════════
    // REVEAL ANIMATIONS — IntersectionObserver
    // ═══════════════════════════════════════════════════════
    const revealEls = document.querySelectorAll(
        '.reveal, .reveal-left, .reveal-right, .reveal-up, .reveal-scale'
    );
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

    revealEls.forEach(el => revealObserver.observe(el));

    // ═══════════════════════════════════════════════════════
    // PARALLAX iPhones (about + services + final)
    // ═══════════════════════════════════════════════════════
    const parallaxPhones = [
        { el: '.about-phone',    section: '.about-section',    speed: 0.05 },
        { el: '.services-phone', section: '.services-section', speed: 0.08 },
        { el: '.final-phone',    section: '.final-section',    speed: 0.06 },
    ];

    const parallaxTargets = parallaxPhones
        .map(p => ({ el: document.querySelector(p.el), section: document.querySelector(p.section), speed: p.speed }))
        .filter(p => p.el && p.section);

    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                parallaxTargets.forEach(({ el, section, speed }) => {
                    const rect = section.getBoundingClientRect();
                    if (rect.top < window.innerHeight && rect.bottom > 0) {
                        const offset = (rect.top - window.innerHeight) * speed;
                        el.style.translate = `0 ${offset}px`;
                    }
                });
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });

    // ═══════════════════════════════════════════════════════
    // SCROLL NAVIGATION DOTS (sidebar droite)
    // ═══════════════════════════════════════════════════════
    const navDots = document.querySelectorAll('.scroll-nav-dot');
    const sections = [
        { sel: '.hero',            el: document.querySelector('.hero') },
        { sel: '#next-section',    el: document.querySelector('#next-section') },
        { sel: '#services',        el: document.querySelector('#services') },
        { sel: '#how',             el: document.querySelector('#how') },
        { sel: '#final',           el: document.querySelector('#final') },
    ].filter(s => s.el);

    // Click sur un dot → scroll vers la section
    navDots.forEach(dot => {
        dot.addEventListener('click', () => {
            const target = document.querySelector(dot.dataset.target);
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Update dot actif au scroll
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY + window.innerHeight / 3;
        let activeIdx = 0;
        sections.forEach((s, i) => {
            const top = s.el.offsetTop;
            const bottom = top + s.el.offsetHeight;
            if (scrollY >= top && scrollY < bottom) activeIdx = i;
        });
        navDots.forEach((d, i) => d.classList.toggle('active', i === activeIdx));
    }, { passive: true });

    // ═══════════════════════════════════════════════════════
    // TRANSITION CINÉMATIQUE — Effet "page change" entre sections
    // (un voile bleu balaye l'écran quand on franchit une section)
    // ═══════════════════════════════════════════════════════
    let lastActiveSection = 0;
    const transitionOverlay = document.createElement('div');
    transitionOverlay.style.cssText = `
        position: fixed; inset: 0; pointer-events: none; z-index: 9999;
        background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        transform: translateY(-100%);
        transition: transform 0.6s cubic-bezier(0.65, 0, 0.35, 1);
    `;
    document.body.appendChild(transitionOverlay);

    function flashTransition() {
        transitionOverlay.style.transform = 'translateY(0)';
        setTimeout(() => { transitionOverlay.style.transform = 'translateY(100%)'; }, 350);
        setTimeout(() => { transitionOverlay.style.transform = 'translateY(-100%)'; transitionOverlay.style.transition = 'none'; setTimeout(() => transitionOverlay.style.transition = 'transform 0.6s cubic-bezier(0.65, 0, 0.35, 1)', 30); }, 950);
    }

    // Détecte le changement de section au scroll (déclenche flash subtil sur transitions importantes)
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY + window.innerHeight / 3;
        let currentIdx = 0;
        sections.forEach((s, i) => {
            if (scrollY >= s.el.offsetTop) currentIdx = i;
        });
        if (currentIdx !== lastActiveSection && Math.abs(currentIdx - lastActiveSection) === 1) {
            // Effet de transition léger entre About → Services et Final → Footer
            // (commenté par défaut pour éviter d'être trop agressif visuel — décommente si tu veux le flash)
            // flashTransition();
        }
        lastActiveSection = currentIdx;
    }, { passive: true });

    // ═══════════════════════════════════════════════════════
    // WHEEL HIJACK — snap fluide entre sections (optionnel)
    // (désactivé par défaut, peut être agressif sur trackpads)
    // ═══════════════════════════════════════════════════════
    // window.addEventListener('wheel', (e) => {
    //     // Réservé : possibilité d'implémenter un snap-scroll plus tard
    // }, { passive: true });
</script>

</body>
</html>
