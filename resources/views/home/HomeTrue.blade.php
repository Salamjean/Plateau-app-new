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
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-blue);
            color: var(--text-navy);
            overflow-x: hidden;
        }

        /* ─────────────── HERO SECTION ─────────────── */
         .hero {
            position: relative;
            height: 100vh; /* Force la hauteur à 100% de l'écran */
            min-height: 700px; /* Sécurité pour les très petits écrans */
            padding: 32px 64px; /* On retire le padding-bottom de 80px qui prenait trop de place */
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Empêche les éléments décoratifs de déborder */
        }

        /* Cercles décoratifs en arrière-plan */
        .hero::before,
        .deco-circle {
            content: '';
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        /* Grand cercle bleu clair (derrière le téléphone) */
         .hero::before {
            width: 80vh;
            height: 80vh;
            background: var(--circle-blue);
            opacity: 0.5;
            right: -10vw;
            top: 10vh;
            left: auto; /* Sécurité pour annuler l'ancienne modification */
        }

        /* Grand cercle bleu marine plein derrière le téléphone */
        .hero-circle-bg {
            position: absolute;
            width: 70vh;
            height: 70vh;
            background: #0a2954;
            opacity: 1;
            right: -8vw;
            top: 15vh;
            border: none;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            will-change: transform;
            transform-origin: center center;
        }

        .deco-circle.c1 {
            width: 50vh;
            height: 50vh;
            background: #0a2954;
            opacity: 0.9;
            right: 0;
            top: 30vh;
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

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        /* ─────────────── HEADER ─────────────── */
          .top-bar {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0; /* Supprimé (était à 60px) pour laisser Flexbox gérer l'espace */
            flex-shrink: 0; /* Empêche le header de s'écraser */
            animation: fadeInDown 0.8s ease-out;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-img {
            height: 65px;
            width: auto;
            object-fit: contain;
            border-radius: 6px;
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
            flex: 1; /* Prend tout l'espace restant sous le header */
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 40px; /* Réduit un peu (était à 60px) */
            align-content: center; /* Centre parfaitement le contenu verticalement */
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* Côté gauche : Texte */
        .hero-left {
            animation: fadeInLeft 1s ease-out 0.2s both;
        }

        .hero-title {
            font-size: 76px; /* Réduit (était 92px) */
            font-weight: 800;
            line-height: 1; /* Ajusté (était 0.95) */
            margin-bottom: 20px;
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
            font-size: 15px; /* Réduit légèrement */
            line-height: 1.5;
            color: var(--text-grey);
            max-width: 480px;
            margin-bottom: 28px; /* Ajusté */
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

        .avatar:first-child {
            margin-left: 0;
        }

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
            justify-content: flex-end; /* Aligne le téléphone sur la droite de sa colonne */
            align-items: center;
            animation: fadeInRight 1.2s ease-out 0.4s both;
        }

        .hero-phone-scroll-wrapper {
            position: relative;
            z-index: 10;
            width: 460px; /* Taille de la 1ère version */
            margin-right: -40px; /* On pousse le téléphone de 40px à l'intérieur du cercle bleu */
            max-width: 100%;
            height: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            will-change: transform;
            transform-style: preserve-3d;
        }

        .iphone-mockup {
            width: 100%;
            height: auto;
            filter: drop-shadow(0 30px 60px rgba(31, 64, 131, 0.25));
            transform: rotate(-3deg);
            animation: floatPhone 6s ease-in-out infinite;
        }

        @keyframes floatPhone {

            0%,
            100% {
                transform: rotate(-3deg) translateY(0);
            }

            50% {
                transform: rotate(-3deg) translateY(-15px);
            }
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

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateX(-50%) translateY(0);
            }

            40% {
                transform: translateX(-50%) translateY(-12px);
            }

            60% {
                transform: translateX(-50%) translateY(-6px);
            }
        }

        /* ─────────────── ANIMATIONS ─────────────── */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* ─────────────── RESPONSIVE ─────────────── */
        @media (max-width: 1200px) {
            .hero-title {
                font-size: 72px;
            }

            .hero-phone-scroll-wrapper {
                width: 480px;
            }
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

            .hero-title {
                font-size: 60px;
            }

            .features {
                justify-content: center;
                grid-template-columns: repeat(auto-fit, minmax(160px, auto));
            }

            .download-buttons,
            .trust-section {
                justify-content: center;
            }

            .hero-phone-scroll-wrapper {
                width: 400px;
                transform: none !important;
            }

            .iphone-mockup {
                transform: rotate(0);
                animation: none;
            }
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

            .logo-text .name {
                font-size: 22px;
            }

            .hero-title {
                font-size: 44px;
            }

            .hero-description {
                font-size: 14px;
            }

            .features {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .feature-item {
                justify-content: center;
            }

            .download-buttons {
                flex-direction: column;
                align-items: center;
            }

            .hero-phone-scroll-wrapper {
                width: 280px;
            }

            .btn-pill {
                padding: 10px 18px;
                font-size: 13px;
            }
        }

        /* ════════════════════════════════════════════════════════
           SECTION "À PROPOS" — fond bleu nuit avec city skyline
           ════════════════════════════════════════════════════════ */
        .about-section {
            position: relative;
            height: 100vh; /* Force la section à 100% de l'écran */
            min-height: 700px;
            padding: 40px 64px; /* On réduit le padding haut/bas */
            color: white;
            overflow: hidden;
            background-color: transparent;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Centre verticalement tout le contenu */
        }

        .about-bg {
            position: absolute;
            inset: 0;
            background: url('{{ asset("assets/landing/about-background.png") }}') center/cover no-repeat,
                linear-gradient(180deg, #0a2954 0%, #061a3a 100%);
            z-index: 0;
            opacity: 1;
        }

        .about-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(10, 41, 84, 0.6) 0%, rgba(6, 26, 58, 0.5) 100%);
            z-index: -1;
        }

        .about-container {
            position: relative;
            z-index: 5;
            display: grid;
            grid-template-columns: 1fr 1.2fr 1fr;
            gap: 40px; /* Réduit (était 60px) pour éviter le débordement */
            align-items: center;
            max-width: 1500px;
            margin: 0 auto;
            width: 100%;
        }

        /* Eyebrow (label NOTRE VISION / A PROPOS) */
        .eyebrow {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px; /* Réduit */
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
            font-size: 34px; /* Réduit (était 40px) pour tenir dans l'écran */
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -1px;
            margin-bottom: 24px;
            color: white;
        }

        .about-title .accent {
            color: var(--success);
        }

        .about-title.right {
            font-size: 30px; /* Réduit */
        }

        .about-text {
            font-size: 13.5px; /* Légèrement réduit */
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 12px; /* Réduit */
        }

        .about-text .accent {
            color: var(--success);
            font-weight: 600;
        }

        /* COLONNE GAUCHE : 4 mini-cards (Proche / Transparente / Rapide / À l'écoute) */
         .mini-cards {
            display: flex;
            gap: 10px; /* Réduit */
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .mini-card {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 10px 6px;
            width: 78px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: default;
        }

        .mini-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--success);
            transform: translateY(-4px);
        }

        .mini-card .icon-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.5);
            margin: 0 auto 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }

        .mini-card .label {
            font-size: 10px;
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
            width: 320px; /* Réduit (était 400px) pour un ajustement parfait à l'écran */
            max-width: 100%;
            transform: rotate(8deg);
            filter: drop-shadow(0 30px 80px rgba(0, 0, 0, 0.4));
            animation: floatPhoneAbout 7s ease-in-out infinite;
        }

        @keyframes floatPhoneAbout {
            0%, 100% { transform: rotate(8deg) translateY(0); }
            50% { transform: rotate(8deg) translateY(-15px); }
        }

        /* Halo lumineux réduit pour coller à la nouvelle taille */
        .about-phone-wrap::before {
            content: '';
            position: absolute;
            width: 500px; /* Réduit */
            height: 500px; /* Réduit */
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79, 172, 254, 0.35) 0%, rgba(79, 172, 254, 0) 65%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
        }

        /* Cercles concentriques réduits */
        .about-phone-wrap::after {
            content: '';
            position: absolute;
            width: 560px; /* Réduit */
            height: 560px; /* Réduit */
            border-radius: 50%;
            border: 2px solid rgba(79, 172, 254, 0.5);
            top: 75%;
            left: 50%;
            transform: translate(-50%, -50%) rotateX(75deg);
            box-shadow: 0 0 40px rgba(79, 172, 254, 0.6), inset 0 0 40px rgba(79, 172, 254, 0.4);
            z-index: -1;
        }
        /* COLONNE DROITE : Carte de sécurité + bouton */
        .security-card {
            margin-top: 20px; /* Réduit */
            padding: 16px 20px;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            gap: 16px;
            align-items: center;
            transition: all 0.3s ease;
        }

        .security-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--success);
        }

        .security-card .shield-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--primary-dark);
            flex-shrink: 0;
        }

        .security-card .security-text {
            font-size: 12.5px;
            line-height: 1.4;
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
            margin-top: 20px;
            padding: 14px 28px; /* Réduit un peu */
            background: white;
            color: var(--primary-dark);
            border-radius: 999px;
            font-weight: 600;
            font-size: 15px;
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
            transition: all 0.3s ease;
            transform: rotate(-45deg);
        }

        .btn-discover:hover .arrow-icon {
            transform: rotate(0deg);
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
        @media (min-width: 993px) {
            .about-phone-wrap {
                visibility: hidden !important;
            }
        }

        @media (max-width: 1200px) {
            .about-phone {
                width: 380px;
            }

            .about-title {
                font-size: 32px;
            }

            .about-title.right {
                font-size: 28px;
            }
        }

        @media (max-width: 992px) {
            .about-section {
                padding: 60px 32px;
            }

            .about-container {
                grid-template-columns: 1fr;
                gap: 60px;
                text-align: center;
            }

            .eyebrow,
            .eyebrow.right {
                justify-content: center;
            }

            .mini-cards {
                justify-content: center;
            }

            .security-card {
                text-align: left;
            }

            .about-phone {
                width: 320px;
                transform: rotate(0);
                animation: none;
            }

            .about-phone-wrap {
                visibility: visible !important;
            }

            .about-phone-wrap::after {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .about-section {
                padding: 50px 20px;
            }

            .about-title {
                font-size: 26px;
            }

            .about-title.right {
                font-size: 24px;
            }

            .about-text {
                font-size: 13px;
            }

            .about-phone {
                width: 240px;
            }

            .btn-discover {
                padding: 14px 24px;
                font-size: 14px;
            }
        }

        /* ════════════════════════════════════════════════════════
           SECTION 3 — "Des services pensés Pour vous simplifier la vie"
           ════════════════════════════════════════════════════════ */
        .services-section {
            position: relative;
            height: 100vh; /* Delimitation à l'écran */
            min-height: 700px;
            padding: 50px 64px 0; /* Plus de padding en bas, on gère avec flex */
            background: url('{{ asset("assets/landing/services-bg.png") }}') top center/cover no-repeat,
                linear-gradient(180deg, #0f2d5e 0%, #0a2150 100%);
            color: white;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Espace le contenu (texte en haut, cartes en bas) */
        }

        .services-top {
            position: relative;
            z-index: 5;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-content: center;
            align-items: center;
            max-width: 1400px;
            margin: auto auto 30px auto; /* Centre verticalement la zone du haut */
            width: 100%;
            flex: 1; /* Prend l'espace disponible en haut */
        }

        .services-title {
            font-size: 34px; /* Réduit */
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -1px;
            margin-bottom: 20px;
            color: white;
        }

        .services-title .accent {
            color: #3271d7;
        }

        .services-text {
            font-size: 14px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.85);
            max-width: 440px;
        }

        .services-phone-side {
            position: relative;
            min-height: 350px; /* Réduit */
        }

        .services-phone {
            position: absolute;
            top: -120px; /* Ajusté */
            right: -20px;
            width: 580px; /* Réduit pour ne pas déborder */
            max-width: none;
            transform: rotate(12deg);
            filter: drop-shadow(0 30px 70px rgba(0, 0, 0, 0.4));
            animation: floatPhoneServices 7s ease-in-out infinite;
        }

        @keyframes floatPhoneServices {
            0%, 100% { transform: rotate(12deg) translateY(0); }
            50% { transform: rotate(12deg) translateY(-15px); }
        }

        /* 5 service cards alignées en bas avec wave effect */
        .services-cards-wrap {
            position: relative;
            z-index: 5;
            background: white;
            border-radius: 70% 70% 0 0 / 90px 90px 0 0;
            padding: 60px 40px 40px; /* Padding réduit pour prendre moins de hauteur */
            margin: 0 -64px 0; /* On retire le margin-bottom négatif, le bloc est collé au bas via flex */
            flex-shrink: 0;
        }

        .services-cards {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px; /* Réduit */
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
            transform: translateY(-6px);
        }

        .service-card .circle {
            width: 100px; /* Cercle réduit pour gagner de la place */
            height: 100px;
            border-radius: 50%;
            background: rgba(15, 77, 142, 0.05);
            border: 3px solid #0f4d8e;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px; /* Icône réduite */
            color: #0f4d8e;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .service-card:hover .circle {
            background: #0f4d8e;
            color: white;
            box-shadow: 0 8px 24px rgba(0, 122, 255, 0.3);
        }

        .service-card .name {
            font-size: 16px; /* Réduit */
            font-weight: 700;
            color: var(--text-navy);
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .service-card .name .accent {
            color: #0f4d8e;
        }

        .service-card .underline {
            width: 30px;
            height: 3px;
            background: var(--success);
            border-radius: 2px;
            margin: 8px 0 10px;
        }

        .service-card .desc {
            font-size: 12px;
            color: #1c1826;
            line-height: 1.3;
            max-width: 180px;
        }
        /* ════════════════════════════════════════════════════════
           SECTION 4 — "Comment ça marche ?" — 4 iPhones tilted
           ════════════════════════════════════════════════════════ */
        .how-section {
            position: relative;
            padding: 56px 64px 0;
            background: var(--bg-blue);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Dark navy zone at the bottom with a gentle wave top edge (SVG) */
        .how-section::after {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 1024' preserveAspectRatio='none'%3E%3Cpath d='M0,556 C320,508 560,600 880,560 C1130,528 1320,548 1440,538 L1440,1024 L0,1024 Z' fill='%23122554'/%3E%3C/svg%3E") no-repeat;
            background-size: 100% 100%;
            z-index: 0;
        }

        .how-title {
            font-size: 52px;
            font-weight: 800;
            text-align: center;
            color: #172440;
            margin-bottom: 36px;
            letter-spacing: -1px;
            position: relative;
            z-index: 5;
        }

        .how-title .accent {
            color: #2f73d8;
        }

        /* The row of 4 phones — aligned at the bottom of the light-blue zone */
        .how-phones-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            align-items: flex-end;
            max-width: 100%;
            margin: 0 auto;
            position: relative;
            z-index: 5;
        }

        .how-phone-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        /* Flex grow so phones push to the bottom, texts align consistently */
        .how-phone-img-wrap {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            flex: 1;
            height: 340px;
        }

        /* All 4 mockups share the same tilt baked into the PNG → uniform height + baseline */
        .how-phone-img {
            width: auto;
            height: 320px;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(0 26px 50px rgba(0, 0, 0, 0.45));
            transition: transform 0.4s ease;
            display: block;
        }

        .how-phone-item:hover .how-phone-img {
            transform: translateY(-12px) scale(1.04);
        }

        /* Separator line — marks the boundary phones / text */
        .how-step-line {
            width: 80%;
            height: 2px;
            background: rgba(100, 150, 220, 0.3);
            margin: 24px 0 20px;
            align-self: center;
            position: relative;
            z-index: 5;
        }

        /* Number + title on the same row */
        .how-step-info {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            width: 100%;
            padding: 0 8px;
            position: relative;
            z-index: 5;
        }

        .how-step-number {
            font-size: 34px;
            font-weight: 800;
            color: #5f8ed1;
            line-height: 1;
            flex-shrink: 0;
            padding-top: 3px;
        }

        .how-step-text {
            display: flex;
            flex-direction: column;
        }

        .how-step-name {
            font-size: 19px;
            font-weight: 800;
            color: white;
            line-height: 1.15;
            margin-bottom: 6px;
        }

        .how-step-desc {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.72);
            line-height: 1.4;
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

        .final-title .line-1 {
            color: #0f4d8e;
            display: block;
        }

        .final-title .line-2 {
            color: var(--success);
            display: block;
        }

        .final-title .line-3 {
            color: #0f4d8e;
            display: block;
        }

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
            width: 850px;
            max-width: none;
            transform: rotate(-6deg) translateX(50px);
            filter: drop-shadow(0 30px 60px rgba(31, 64, 131, 0.3));
            animation: floatFinalPhone 6s ease-in-out infinite;
        }

        @keyframes floatFinalPhone {

            0%,
            100% {
                transform: rotate(-6deg) translateX(50px) translateY(0);
            }

            50% {
                transform: rotate(-6deg) translateX(50px) translateY(-15px);
            }
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
            object-fit: cover;
            margin-left: -10px;
        }

        .download-trust .avatar:first-child {
            margin-left: 0;
        }

        .download-trust .info {
            text-align: left;
            font-size: 12px;
        }

        .download-trust .info strong {
            display: block;
            font-size: 13px;
        }

        .download-trust .info .rating {
            color: var(--success);
            font-weight: 700;
        }

        /* ════════════════════════════════════════════════════════
           SECTION 5 — Final + Footer
           ════════════════════════════════════════════════════════ */
        .final-section {
            position: relative;
            background: var(--bg-blue);
            padding: 60px 64px 0;
            overflow: hidden;
        }

        /* City skyline watermark behind content */
        .final-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('{{ asset("assets/landing/final-bg.png") }}') center / cover no-repeat;
            opacity: 0.55;
            z-index: 0;
        }

        /* Small logo inside the section */
        .final-logo {
            position: relative;
            z-index: 5;
            margin-bottom: 30px;
        }

        .final-logo img {
            height: 60px;
            width: auto;
        }

        .final-container {
            position: relative;
            z-index: 5;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .final-title {
            font-size: 76px;
            font-weight: 900;
            line-height: 0.92;
            letter-spacing: -2.5px;
            margin-bottom: 20px;
        }

        .final-title .line-1 {
            color: #0d2e6e;
            display: block;
        }

        .final-title .line-2 {
            color: var(--success);
            display: block;
        }

        .final-title .line-3 {
            color: #0d2e6e;
            display: block;
        }

        .final-text {
            font-size: 14.5px;
            color: #2a3a5e;
            line-height: 1.55;
            margin-bottom: 36px;
            max-width: 420px;
        }

        /* 4 mini stats */
        .final-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            max-width: 560px;
            border-top: 1px solid rgba(15, 77, 142, 0.12);
            padding-top: 24px;
        }

        .final-stat {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding-right: 16px;
        }

        .final-stat .icon-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 8px rgba(15, 77, 142, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f4d8e;
            font-size: 15px;
            margin-bottom: 10px;
        }

        .final-stat .stat-name {
            font-size: 11px;
            font-weight: 700;
            color: #172440;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .final-stat .stat-desc {
            font-size: 9.5px;
            color: #3a4e72;
            line-height: 1.35;
        }

        /* Phone right side */
        .final-phone-wrap {
            position: relative;
            display: flex;
            justify-content: center; /* Centre le téléphone au lieu de le coller à droite */
            align-items: flex-end;
            padding-bottom: 0;
            padding-right: 40px; /* L'éloigne un peu plus du bord droit */
        }

         .final-phone {
            width: 460px;
            max-width: 100%;
            transform: rotate(-10deg) translateY(20px);
            filter: drop-shadow(0 30px 70px rgba(12, 40, 100, 0.35));
            animation: floatFinalPhone 6s ease-in-out infinite;
        }
       @keyframes floatFinalPhone {
            0%, 100% { transform: rotate(-10deg) translateY(20px); }
            50% { transform: rotate(-10deg) translateY(5px); }
        }
  /* ─────────────── BOUTON RETOUR EN HAUT ─────────────── */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.4s ease;
            box-shadow: 0 6px 15px rgba(25, 119, 204, 0.3);
            text-decoration: none;
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .back-to-top:hover {
            background: var(--primary-dark);
            transform: translateY(-5px);
            color: white;
        }
        /* Smooth wave transition from the light section into the navy download zone */
        .final-wave {
            position: relative;
            z-index: 5;
            width: calc(100% + 128px);
            margin: 40px -64px -1px;
            height: 110px;
            background: transparent url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 110' preserveAspectRatio='none'%3E%3Cpath d='M0,52 C360,4 760,96 1060,58 C1250,34 1360,46 1440,40 L1440,110 L0,110 Z' fill='%23122554'/%3E%3C/svg%3E") no-repeat;
            background-size: 100% 100%;
        }

        /* Download CTA dark zone */
        .download-bar {
            position: relative;
            z-index: 5;
            background: #122554;
            border-radius: 0;
            margin: 0 -64px;
            padding: 30px 64px 50px;
            color: white;
            text-align: center;
        }

        .download-bar h3 {
            font-size: 30px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .download-bar>p {
            font-size: 15px;
            opacity: 0.85;
            margin-bottom: 36px;
        }

        .download-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .download-trust {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-right: 24px;
            border-right: 1.5px solid rgba(255, 255, 255, 0.2);
        }

        .download-trust .avatars {
            display: flex;
        }

        .download-trust .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid white;
            object-fit: cover;
            margin-left: -10px;
        }

        .download-trust .avatar:first-child {
            margin-left: 0;
        }

        .download-trust .info {
            text-align: left;
            font-size: 12px;
            line-height: 1.4;
        }

        .download-trust .info strong {
            display: block;
            font-size: 14px;
            font-weight: 800;
        }

        .download-trust .info .rating {
            color: #4ade80;
            font-weight: 700;
            font-size: 13px;
        }

        /* Store buttons — styled exactly like reference */
        .btn-store-dark {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #0d2252;
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-store-dark:hover {
            background: #1a3a7c;
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }

        .btn-store-dark i {
            font-size: 26px;
        }

        .btn-store-dark .store-text {
            text-align: left;
            line-height: 1.2;
        }

        .btn-store-dark .store-text small {
            font-size: 10px;
            opacity: 0.75;
            display: block;
        }

        .btn-store-dark .store-text span {
            font-size: 16px;
            font-weight: 700;
        }

        /* Scroll-down chevron line */
        .download-separator {
            position: relative;
            z-index: 5;
            max-width: 1400px;
            margin: 60px auto 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .download-separator::before,
        .download-separator::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .scroll-down-final {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            color: white;
            margin: 0 20px;
            font-size: 12px;
            opacity: 0.75;
            transition: opacity 0.3s;
            text-decoration: none;
        }

        .scroll-down-final:hover {
            opacity: 1;
        }

        /* Grid Background for Footer area */
        .footer-grid-wrapper {
            position: relative;
            background: #081a42;
            overflow: hidden;
            margin: 0 -64px;
            padding: 0 64px;
        }

        .footer-grid-bg {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255,255,255,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 40px 40px;
            background-position: center bottom;
            transform: perspective(600px) rotateX(60deg) scale(2);
            transform-origin: bottom center;
            z-index: 1;
        }

        /* Footer */
        .footer {
            position: relative;
            z-index: 5;
            padding: 40px 0 60px;
            color: white;
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 30px;
            align-items: center;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .footer-left .logo-img {
            height: 45px;
        }

        .footer-left .footer-tagline {
            font-size: 13px;
            opacity: 0.9;
            line-height: 1.4;
            padding-left: 20px;
            border-left: 1.5px solid rgba(255, 255, 255, 0.2);
        }

        .footer-social {
            display: flex;
            justify-content: center;
            gap: 14px;
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
            font-size: 12.5px;
            opacity: 0.85;
            line-height: 1.5;
        }

        /* ════════════════════════════════════════════════════════
           TRANSITIONS DE SECTION (snap scroll + smooth)
           ════════════════════════════════════════════════════════ */
        /* ════════════════════════════════════════════════════════
           FULLPAGE TRANSITIONS — chaque section devient une "page"
           qui scale + fade entre elles. Pas de scroll classique.
           ════════════════════════════════════════════════════════ */

        html, body {
            height: 100%;
            overflow: hidden;
            scroll-behavior: auto;
        }

        body.fullpage-mode {
            position: fixed;
            inset: 0;
        }

        /* Filet anti-empilement au rafraîchissement : sur desktop (= conditions du
           mode fullpage), tant que le JS n'a pas activé `.fullpage-mode`, on masque
           déjà toutes les sections sauf le Hero → plus d'effet "sections superposées". */
        @media (min-width: 992px) and (prefers-reduced-motion: no-preference) {
            body:not(.fullpage-mode) > section:not(.hero),
            body:not(.fullpage-mode) > footer {
                opacity: 0;
                pointer-events: none;
            }
        }

        /* Toutes les pages = fixed plein écran avec transition scale + opacity */
        body.fullpage-mode > section,
        body.fullpage-mode > footer {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            min-height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            opacity: 0;
            transform: scale(0.88);
            transition:
                opacity 0.75s cubic-bezier(0.65, 0, 0.35, 1),
                transform 0.75s cubic-bezier(0.65, 0, 0.35, 1);
            pointer-events: none;
            will-change: transform, opacity;
            z-index: 1;
        }

        /* Section actuellement visible */
        body.fullpage-mode > section.fp-active,
        body.fullpage-mode > footer.fp-active {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
            z-index: 5;
        }

        /* Section qui sort vers le bas (scrolling up) */
        body.fullpage-mode > section.fp-leaving-down,
        body.fullpage-mode > footer.fp-leaving-down {
            opacity: 0;
            transform: scale(1.08);
            z-index: 4;
        }

        /* Section qui sort vers le haut (scrolling down) */
        body.fullpage-mode > section.fp-leaving-up,
        body.fullpage-mode > footer.fp-leaving-up {
            opacity: 0;
            transform: scale(0.88);
            z-index: 4;
        }

        /* Footer un peu plus petit qu'une section pleine — on l'agrandit */
        body.fullpage-mode > footer.fp-active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Hide scrollbar on sections (scroll interne discret) */
        body.fullpage-mode > section::-webkit-scrollbar,
        body.fullpage-mode > footer::-webkit-scrollbar {
            width: 0;
            background: transparent;
        }

        /* Indicateur "scroll to continue" qui pulse */
        .fp-hint {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 200;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.7);
            background: rgba(15, 30, 60, 0.55);
            padding: 8px 16px;
            border-radius: 999px;
            backdrop-filter: blur(8px);
            pointer-events: none;
            animation: fpHintPulse 2.4s ease-in-out infinite;
            opacity: 0;
            transition: opacity 0.4s;
        }
        .fp-hint.visible { opacity: 1; }
        .fp-hint i { margin-left: 8px; animation: fpHintArrow 2.4s ease-in-out infinite; }

        @keyframes fpHintPulse {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50%      { transform: translateX(-50%) translateY(-4px); }
        }
        @keyframes fpHintArrow {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(3px); }
        }

        /* En mode mobile (<992px) ou prefers-reduced-motion :
           on désactive le fullpage et on garde le scroll classique. */
        @media (max-width: 991px) {
            html, body {
                height: auto;
                overflow: auto;
            }
            body.fullpage-mode {
                position: static;
            }
            body.fullpage-mode > section,
            body.fullpage-mode > footer {
                position: relative !important;
                width: auto !important;
                height: auto !important;
                min-height: auto !important;
                opacity: 1 !important;
                transform: none !important;
                pointer-events: auto !important;
                transition: none !important;
            }
            .fp-hint, .scroll-nav { display: none !important; }
        }

        @media (prefers-reduced-motion: reduce) {
            body.fullpage-mode > section,
            body.fullpage-mode > footer {
                transition: opacity 0.2s linear !important;
                transform: none !important;
            }
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
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
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
        .stagger-1 {
            transition-delay: 0.1s;
        }

        .stagger-2 {
            transition-delay: 0.2s;
        }

        .stagger-3 {
            transition-delay: 0.3s;
        }

        .stagger-4 {
            transition-delay: 0.4s;
        }

        .stagger-5 {
            transition-delay: 0.5s;
        }

        /* Responsive sections finales */
        @media (max-width: 1200px) {
            .services-title {
                font-size: 30px;
            }

            .services-phone {
                width: 540px;
                top: -100px;
                right: -20px;
            }

            .how-title {
                font-size: 44px;
            }

            .how-phone-img {
                width: 160px;
            }

            .final-title {
                font-size: 56px;
            }

            .final-phone {
                width: 700px;
                max-width: none;
                transform: rotate(-6deg) translateX(30px);
            }
        }

        @media (max-width: 992px) {
            .services-section {
                padding: 60px 32px;
            }

            .services-top {
                grid-template-columns: 1fr;
            }

            .services-phone-side {
                min-height: 0;
            }

            .services-phone {
                position: relative;
                top: 0;
                right: 0;
                transform: rotate(0);
                margin: 30px auto;
                display: block;
                animation: none;
            }

            .services-cards-wrap {
                margin: 30px -32px -60px;
                padding: 60px 24px 50px;
                border-radius: 50% 50% 0 0 / 60px 60px 0 0;
            }

            .services-cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .how-section {
                padding: 60px 32px 100px;
            }

            .how-title {
                font-size: 36px;
                margin-bottom: 50px;
            }

            .how-phones-row {
                grid-template-columns: repeat(2, 1fr);
                gap: 50px 30px;
            }

            .final-section {
                padding: 60px 32px 0;
            }

            .final-container {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .final-title {
                font-size: 48px;
            }

            .final-stats {
                grid-template-columns: repeat(2, 1fr);
                margin: 0 auto;
            }

            .final-stat {
                align-items: center;
                text-align: center;
            }

            .final-phone {
                width: 480px;
                max-width: none;
                transform: rotate(0);
                animation: none;
            }

            .download-bar {
                margin: 60px -32px 0;
                padding: 60px 32px 40px;
                border-radius: 50% 50% 0 0 / 50px 50px 0 0;
            }

            .download-bar h3 {
                font-size: 22px;
            }

            .footer {
                padding: 30px 32px;
            }

            .footer-container {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 20px;
            }

            .footer-right {
                text-align: center;
            }

            .scroll-nav {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .services-cards {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .service-card .circle {
                width: 100px;
                height: 100px;
                font-size: 38px;
            }

            .how-phones-row {
                grid-template-columns: 1fr;
            }

            .how-phone-img {
                width: 200px;
            }

            .final-title {
                font-size: 36px;
            }

            .final-stats {
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }

            .download-bar h3 {
                font-size: 18px;
            }

            .download-row {
                flex-direction: column;
                gap: 20px;
            }

            .download-trust {
                border-right: none;
                padding-right: 0;
                padding-bottom: 20px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            }
        }

        /* ════════════════════════════════════════════════════════
           TÉLÉPHONE PERSISTANT — un SEUL mockup qui voyage entre
           les sections et se pose à l'emplacement de chacune.
           (créé/piloté en JS via GSAP — voir le script en bas)
           ════════════════════════════════════════════════════════ */
        .persistent-phone {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 60;                 /* au-dessus des sections (z 1-5), sous le hint/dots */
            pointer-events: none;        /* ne bloque jamais les clics */
            opacity: 0;
            will-change: transform, opacity, width;
            transform-style: preserve-3d;
            backface-visibility: hidden;
            filter: drop-shadow(0 30px 60px rgba(15, 30, 60, 0.35));
            display: none;               /* activé seulement en mode fullpage desktop */
        }
        body.fullpage-mode .persistent-phone { display: block; }

        /* Sur mobile / tablette : pas de téléphone persistant,
           on garde les téléphones inline de chaque section. */
        @media (max-width: 991px) {
            .persistent-phone { display: none !important; }
        }
           
        /* Cache immédiatement les téléphones statiques pour qu'ils ne clignotent pas au chargement */
     body.fullpage-mode .iphone-mockup,
        body.fullpage-mode .about-phone,
        body.fullpage-mode .services-phone,
        body.fullpage-mode .final-phone {
            visibility: hidden;
        }
        /* Aucune animation/transition au TOUT PREMIER rendu (= rafraîchissement) :
           la page apparaît instantanément, sans effet d'entrée. Réactivé par le JS
           juste après le premier paint (classe retirée). */
        html.fp-preload *,
        html.fp-preload *::before,
        html.fp-preload *::after {
            transition: none !important;
            animation: none !important;
        }
    </style>
</head>

<body>
    <!-- SCRIPT ANTI-CLIGNOTEMENT (À l'intérieur du body) -->
    <script>
        // 1) Aucune animation/transition au premier rendu (refresh) — retiré après le 1er paint
        document.documentElement.classList.add('fp-preload');
        // 2) Active le mode fullpage immédiatement (évite l'empilement des sections)
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

    <section class="hero fp-active">
        <!-- Cercles décoratifs -->
        <div class="hero-circle-bg"></div>
        <div class="deco-circle c1"></div>
        <div class="deco-circle c2"></div>
        <div class="deco-circle c3"></div>

        <!-- HEADER : Logo + Boutons -->
        <header class="top-bar">
            <div class="logo">
                <img src="{{ asset('assets/assets/img/plateau-mart.png') }}" alt="Plateau Smart City"
                    style="height: 65px; width: auto; display: block;" class="logo-img">
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
                        onerror="this.style.display='none'; this.parentElement.innerHTML += '<div style=&quot;padding:60px;color:#999;text-align:center;&quot;>📱 Mockup iPhone</div>';">
                </div>
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
                <img src="{{ asset('assets/assets/img/telephone2HD.png') }}" alt="Plateau Apps mobile" class="about-phone"
                    onerror="this.style.display='none';">
            </div>

            <!-- COLONNE DROITE : À Propos + Carte sécurité + CTA -->
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

                <!-- Bouton CTA -->
                <div style="margin-top: 24px;">
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
                <img src="{{ asset('assets/landing/iphone-mockup.png') }}" alt="Plateau Apps services"
                    class="services-phone" onerror="this.style.display='none';">
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

        <!-- Phones row (light blue background area) -->
        <div class="how-phones-row">

            <div class="how-phone-item reveal-up stagger-1">
                <div class="how-phone-img-wrap">
                    <img src="{{ asset('assets/landing/how-naissance.png') }}" alt="Acte de naissance"
                        class="how-phone-img">
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
                    <img src="{{ asset('assets/landing/how-mariage.png') }}" alt="Acte de mariage"
                        class="how-phone-img">
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

    </section>

    <!-- ════════════════════════════════════════════════════════
     SECTION 5 — "Le Plateau plus proche que jamais" + Footer
     ════════════════════════════════════════════════════════ -->
    <section id="final" class="final-section">

        <!-- Logo dans la section -->
        <div class="final-logo">
            <img src="{{ asset('assets/assets/img/plateau-mart.png') }}" alt="Plateau Smart City">
        </div>

        <!-- Grille gauche (texte + stats) / droite (téléphone) -->
        <div class="final-container">

            <!-- COL GAUCHE -->
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

            <!-- COL DROITE : iPhone incliné -->
            <div class="final-phone-wrap reveal-right">
                <img src="{{ asset('assets/landing/how-deces.png') }}" alt="Plateau Apps RDV" class="final-phone"
                    onerror="this.style.display='none';">
            </div>

        </div>

        <!-- Grande vague blanche -> transition vers le bleu marine -->
        <div class="final-wave"></div>

        <!-- Barre téléchargement bleue marine -->
        <div class="download-bar">
            <h3>Téléchargez Plateau Apps</h3>
            <p>et simplifiez votre quotidien dès aujourd'hui.</p>

            <div class="download-row">
                <!-- Avatars + rating -->
                <div class="download-trust">
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
                    <div class="info">
                        <strong>+12000 habitants</strong>
                        utilisent déjà plateau Apps
                        <div><span class="rating">★★★★★ 4.8/5</span></div>
                    </div>
                </div>

                <!-- Bouton App Store -->
                <a href="#" class="btn-store-dark">
                    <i class="fab fa-apple"></i>
                    <div class="store-text">
                        <small>Télécharger</small>
                        <span>l'App Store</span>
                    </div>
                </a>

                <!-- Bouton Google Play -->
                <a href="#" class="btn-store-dark">
                    <i class="fab fa-google-play"></i>
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

            <!-- Footer -->
            <footer class="footer">
                <div class="footer-container">
                    <div class="footer-left">
                        <img src="{{ asset('assets/assets/img/plateau-mart.png') }}" alt="Plateau Smart City"
                            class="logo-img">
                        <div class="footer-tagline">
                            Une administration moderne,<br>
                            proche de vous, pour vous.
                        </div>
                    </div>

                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>

                    <div class="footer-right">
                        Service officiel de la Mairie du Plateau<br>
                        © 2026 Tous droits réservés.
                    </div>
                </div>
            </footer>
        </div>

    </section>

    <!-- GSAP — utilisé uniquement pour piloter le téléphone persistant -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

    <script>
        // ════════════════════════════════════════════════════════
        // FULLPAGE NAVIGATION — chaque section = une "page"
        // wheel/touch/keyboard déclenche un swap animé (scale+fade)
        // ════════════════════════════════════════════════════════

        const FP_ENABLED = window.innerWidth >= 992
            && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        const fpPages = Array.from(document.querySelectorAll('body > section, body > footer'));
        let fpIdx = 0;
        let fpTransitioning = false;
        const FP_DURATION = 800; // doit matcher le CSS transition

        if (FP_ENABLED) {
            document.body.classList.add('fullpage-mode');
            if (fpPages[0]) fpPages[0].classList.add('fp-active');
        }

        // ════════════════════════════════════════════════════════
        // TÉLÉPHONE PERSISTANT — un SEUL mockup qui suit le scroll
        // et vient se poser exactement à l'emplacement de chaque
        // section. Il n'apparaît jamais ailleurs que là où on est.
        // ════════════════════════════════════════════════════════
        const HAS_GSAP = typeof gsap !== 'undefined';
        const PP_ENABLED = FP_ENABLED && HAS_GSAP;

        // ── Config des "emplacements" (slots) par index de section ──
        //    sel    = sélecteur du téléphone inline original (sert d'ancre de mesure)
        //    rotate = rotation finale à cet emplacement (°), reprise du CSS existant
        //    Mettre null pour une section sans téléphone unique (ex: "Comment ça marche" = 4 phones).
        //    >>> Pour ajuster : change rotate ici, ou la position via le CSS de l'ancre. <<<
        const PP_SLOT_CONFIG = [
            { sel: '.iphone-mockup', rotate: -3 },                       // 0 · Hero      (iphone-mockup.png)
            { sel: '.about-phone', rotate: 8 },                          // 1 · À propos  (iphone-mockup.png)
            { sel: '.services-phone', rotate: 12 },                      // 2 · Services  (iphone-mockup.png)
            { sel: '.how-phone-item:nth-child(1) .how-phone-img', rotate: 0 }, // 3 · Section 4 (DYNAMIQUE)
            { sel: '.final-phone', rotate: -10 },                        // 4 · Final     (iphone-rdv.png)
        ];

        let ppEl = null;            // l'élément <img> persistant
        let ppSlots = [];           // positions mesurées { left, top, width, rotate, img }
        let ppVisible = false;      // est-il actuellement affiché ?
        let ppCurrentImg = '';      // src actuellement chargée (pour détecter un changement d'écran)
        let ppCurrentIdx = -1;      // index du slot où il se trouve

        // Mesure la position/taille de chaque ancre, section neutralisée (scale 1, opacity 1)
        // pour obtenir l'emplacement RÉEL (au repos), sans le scale/fade du swap.
        function ppMeasureSlots() {
            ppSlots = PP_SLOT_CONFIG.map((cfg, i) => {
                if (!cfg) return null;
                const sec = fpPages[i];
                const anchor = sec && sec.querySelector(cfg.sel);
                if (!anchor) return null;

                // Sauver puis neutraliser temporairement (aucun repaint : lecture synchrone)
                const sT = sec.style.transform, sO = sec.style.opacity, sTr = sec.style.transition;
                const aT = anchor.style.transform, aA = anchor.style.animation, aV = anchor.style.visibility;
                sec.style.transition = 'none'; sec.style.transform = 'none'; sec.style.opacity = '1';
                anchor.style.animation = 'none'; anchor.style.transform = 'none'; anchor.style.visibility = 'visible';

                const r = anchor.getBoundingClientRect();
                const slot = { left: r.left, top: r.top, width: r.width, rotate: cfg.rotate, img: anchor.getAttribute('src') };

                // Restaurer (l'ancre reste cachée)
                sec.style.transform = sT; sec.style.opacity = sO; sec.style.transition = sTr;
                anchor.style.transform = aT; anchor.style.animation = aA; anchor.style.visibility = aV;
                return slot;
            });
        }

        // Déplace / fait apparaître le téléphone persistant vers le slot de la section idx
        function ppMoveTo(idx) {
            if (!PP_ENABLED || !ppEl) return;
            const slot = ppSlots[idx];

            // Section sans slot (ex: "Comment ça marche") → on efface le persistant
            if (!slot) {
                gsap.killTweensOf(ppEl);
                gsap.to(ppEl, { opacity: 0, duration: 0.4, ease: 'power2.out', onComplete: () => { ppVisible = false; } });
                ppCurrentIdx = idx;
                return;
            }

            const imgChanged = ppCurrentImg !== slot.img;
            gsap.killTweensOf(ppEl);

            if (!ppVisible) {
                // Apparition "fraîche" (le phone était caché) : on se place puis fondu d'entrée.
                if (imgChanged) { ppEl.src = slot.img; ppCurrentImg = slot.img; }
                gsap.set(ppEl, { x: slot.left, y: slot.top, width: slot.width, rotation: slot.rotate, opacity: 0 });
                gsap.to(ppEl, { opacity: 1, duration: 0.55, ease: 'power2.out' });
                ppVisible = true;
            } else if (imgChanged) {
                // Voyage AVEC changement d'écran (ex: Services → "Acte de Naissance") :
                // on déplace en continu ET on fond-enchaîne l'écran au milieu du trajet
                // → le téléphone "suit" sans jamais disparaître.
                gsap.to(ppEl, { x: slot.left, y: slot.top, width: slot.width, rotation: slot.rotate, duration: 0.8, ease: 'power3.inOut' });
                gsap.to(ppEl, { opacity: 0.2, duration: 0.4, ease: 'power1.inOut',
                    onComplete: () => { ppEl.src = slot.img; ppCurrentImg = slot.img; } });
                gsap.to(ppEl, { opacity: 1, duration: 0.4, delay: 0.4, ease: 'power1.inOut' });
            } else {
                // Voyage continu, même écran : position + rotation + taille
                gsap.to(ppEl, {
                    x: slot.left, y: slot.top, width: slot.width, rotation: slot.rotate, opacity: 1,
                    duration: 0.8, ease: 'power3.inOut'
                });
            }
            ppCurrentIdx = idx;
        }

      function ppInit() {
            if (!PP_ENABLED) return;

            // Le téléphone est maintenant récupéré directement depuis le HTML
            ppEl = document.getElementById('flying-phone');

            // Cacher tous les téléphones inline "uniques" (ils servent juste d'ancres de mesure)
           PP_SLOT_CONFIG.forEach((cfg, i) => {
                if (!cfg) return;
                const anchor = fpPages[i] && fpPages[i].querySelector(cfg.sel);
                // On cache tout sauf l'index 3 (Section 4) qui est géré dynamiquement
                if (anchor && i !== 3) anchor.style.visibility = 'hidden';
            });

            // Quand une section dépasse l'écran et qu'on scrolle dedans, le téléphone
            // (fixe) DÉFILE avec le contenu (translation) au lieu de "disparaître"
            fpPages.forEach((sec, i) => {
                sec.addEventListener('scroll', () => {
                    if (i !== fpIdx || !ppEl || !ppSlots[i] || fpTransitioning) return;
                    gsap.set(ppEl, { y: ppSlots[i].top - sec.scrollTop });
                }, { passive: true });
            });

            ppMeasureSlots();

            // Placement initial sur la 1re section (Hero)
            const first = ppSlots[0];
            if (first) {
                ppEl.src = first.img; 
                ppCurrentImg = first.img;
                gsap.set(ppEl, { x: first.left, y: first.top, width: first.width, rotation: first.rotate, opacity: 1 });
                ppVisible = true; 
                ppCurrentIdx = 0;
            }
        }
        // Re-mesure + repositionnement instantané (resize fenêtre)
        let ppResizeTimer = null;
        window.addEventListener('resize', () => {
            if (!PP_ENABLED || !ppEl) return;
            clearTimeout(ppResizeTimer);
            ppResizeTimer = setTimeout(() => {
                ppMeasureSlots();
                const slot = ppSlots[ppCurrentIdx];
                if (slot) gsap.set(ppEl, { x: slot.left, y: slot.top, width: slot.width, rotation: slot.rotate });
            }, 150);
        });

        // Après chargement complet (images dimensionnées), re-mesurer pour être pile au bon endroit
        window.addEventListener('load', () => {
            if (!PP_ENABLED || !ppEl) return;
            ppMeasureSlots();
            const slot = ppSlots[ppCurrentIdx];
            if (slot) gsap.set(ppEl, { x: slot.left, y: slot.top, width: slot.width, rotation: slot.rotate });
        });

        ppInit();

        // Helper : déclenche les reveal animations dans une section
        function fpRevealIn(section) {
            section.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-up, .reveal-scale')
                .forEach(el => el.classList.add('visible'));
        }

        // Helper : retire les reveal animations (pour qu'elles re-jouent à la prochaine entrée)
        function fpResetReveals(section) {
            section.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-up, .reveal-scale')
                .forEach(el => el.classList.remove('visible'));
        }

        // Swap standard piloté par GSAP (fondu croisé + léger zoom). Fiable sur tous
        // les navigateurs — les transitions CSS ne se déclenchaient pas toujours
        // (Firefox), d'où l'impression de "coupure" sans fondu entre les sections.
        function fpAnimateSwap(leaving, entering, dir) {
            gsap.killTweensOf([leaving, entering]);
            entering.classList.add('fp-active');     // pointer-events + z-index via la classe
            leaving.style.transition = 'none';        // on neutralise la transition CSS
            entering.style.transition = 'none';
            entering.style.zIndex = '6';
            leaving.style.zIndex = '5';
            gsap.set(entering, { opacity: 0, scale: dir === 'up' ? 0.94 : 1.06 });
            gsap.set(leaving, { opacity: 1, scale: 1 });
            gsap.to(entering, { opacity: 1, scale: 1, duration: 0.7, ease: 'power2.out' });
            gsap.to(leaving, {
                opacity: 0, scale: dir === 'up' ? 1.06 : 0.94, duration: 0.65, ease: 'power2.in',
                onComplete: () => {
                    leaving.classList.remove('fp-active');
                    // retour aux états gérés par les classes, sans flash (transition encore none)
                    gsap.set([leaving, entering], { clearProps: 'opacity,transform,zIndex' });
                    leaving.style.transition = '';
                    entering.style.transition = '';
                }
            });
        }

   function fpGoTo(idx) {
            if (!FP_ENABLED) return;
            if (fpTransitioning) return;
            if (idx < 0 || idx >= fpPages.length || idx === fpIdx) return;

            fpTransitioning = true;
            const leaving = fpPages[fpIdx];
            const entering = fpPages[idx];

            entering.scrollTop = 0;
            fpRevealIn(entering);

            // Toutes les transitions utilisent le MÊME fondu croisé + léger zoom
            if (HAS_GSAP) {
                fpAnimateSwap(leaving, entering, idx > fpIdx ? 'up' : 'down');
            } else {
                const direction = idx > fpIdx ? 'up' : 'down';
                leaving.classList.remove('fp-active');
                leaving.classList.add(direction === 'up' ? 'fp-leaving-up' : 'fp-leaving-down');
                entering.classList.add('fp-active');
            }

            // Gestion de l'affichage du bouton retour en haut
            const backToTopBtn = document.getElementById('backToTop');
            if (backToTopBtn) {
                if (idx === 4) { // Si on est sur la 5ème section
                    backToTopBtn.classList.add('show');
                } else {
                    backToTopBtn.classList.remove('show');
                }
            }

            // ── GESTION DYNAMIQUE DU TÉLÉPHONE POUR LA SECTION 4 ──
            const phone1Sel = '.how-phone-item:nth-child(1) .how-phone-img';
            const phone3Sel = '.how-phone-item:nth-child(3) .how-phone-img';
            
            // 1. Déterminer la cible (Tel 1 ou Tel 3) selon le sens de défilement
            if (idx === 3 && fpIdx === 2) PP_SLOT_CONFIG[3].sel = phone1Sel;
            else if (idx === 4 && fpIdx === 3) PP_SLOT_CONFIG[3].sel = phone3Sel;
            else if (idx === 3 && fpIdx === 4) PP_SLOT_CONFIG[3].sel = phone3Sel;
            else if (idx === 2 && fpIdx === 3) PP_SLOT_CONFIG[3].sel = phone1Sel;

            ppMeasureSlots(); // Recalcule les positions exactes

            // 2. Téléportation invisible AVANT le mouvement
            if (PP_ENABLED && ppEl) {
                if (fpIdx === 3 && idx === 4) {
                    // On part de 4 vers 5 : on téléporte le mockup sur le Tel 3
                    const slot3 = ppSlots[3]; 
                    gsap.set(ppEl, { x: slot3.left, y: slot3.top, width: slot3.width, rotation: slot3.rotate });
                    ppEl.src = slot3.img; 
                    ppCurrentImg = slot3.img; // Trompe le script pour éviter le fondu d'opacité !
                }
                else if (fpIdx === 3 && idx === 2) {
                    // On part de 4 vers 3 : on téléporte le mockup sur le Tel 1
                    const slot1 = ppSlots[3]; 
                    gsap.set(ppEl, { x: slot1.left, y: slot1.top, width: slot1.width, rotation: slot1.rotate });
                    ppEl.src = slot1.img;
                    ppCurrentImg = slot1.img; 
                }
            }

            // 3. Rallumer les téléphones statiques pour éviter les "trous" visuels
            const p1 = document.querySelector(phone1Sel);
            const p3 = document.querySelector(phone3Sel);
            if (p1) p1.style.visibility = 'visible';
            if (p3) p3.style.visibility = 'visible';

            // 4. Éteindre uniquement le téléphone statique sur lequel on va atterrir
            if (idx === 3 || fpIdx === 3) {
                const activeP = document.querySelector(PP_SLOT_CONFIG[3].sel);
                if (activeP) activeP.style.visibility = 'hidden';
            }
            // ────────────────────────────────────────────────────────

            // Le téléphone persistant voyage de manière totalement fluide !
            ppMoveTo(idx);

            setTimeout(() => {
                // Finalisation FIABLE de l'état (au cas où un onComplete GSAP serait
                // retardé — ex. onglet en arrière-plan) : une seule section active, propre.
                if (HAS_GSAP) gsap.killTweensOf(fpPages);
                fpPages.forEach((p, i) => {
                    p.classList.remove('fp-leaving-up', 'fp-leaving-down');
                    p.classList.toggle('fp-active', i === idx);
                    p.style.transition = ''; p.style.opacity = ''; p.style.transform = '';
                    p.style.zIndex = ''; p.style.clipPath = '';
                });
                fpResetReveals(leaving);
                fpIdx = idx;
                fpTransitioning = false;
            }, FP_DURATION);
        }
        // ── Wheel (souris / trackpad) ──
        // Clé pour un rendu fluide au trackpad :
        //  1) tant que la section peut défiler en interne, on laisse le SCROLL NATIF
        //     (lisse + inertie) au lieu de bouger scrollTop à la main (saccadé) ;
        //  2) un verrou se libère seulement quand la molette/inertie s'arrête (~90 ms
        //     sans event) → une seule impulsion = un seul changement de section,
        //     l'inertie du trackpad ne déclenche plus de sauts multiples.
        let wheelLocked = false;
        let wheelIdleTimer = null;
        const armWheelUnlock = () => {
            clearTimeout(wheelIdleTimer);
            wheelIdleTimer = setTimeout(() => { wheelLocked = false; }, 90);
        };

        window.addEventListener('wheel', (e) => {
            if (!FP_ENABLED) return;

            const current = fpPages[fpIdx];
            const down = e.deltaY > 0;
            const atBottom = current.scrollTop + current.clientHeight >= current.scrollHeight - 2;
            const atTop = current.scrollTop <= 2;

            // Scroll interne natif tant qu'on n'est pas au bord de la section
            if ((down && !atBottom) || (!down && !atTop)) {
                wheelLocked = true;     // on défile dans la section → pas de swap
                armWheelUnlock();
                return;                 // pas de preventDefault → scroll natif fluide
            }

            // Au bord → on capture la molette pour changer de section
            e.preventDefault();
            armWheelUnlock();

            if (wheelLocked || fpTransitioning) return;
            if (Math.abs(e.deltaY) < 8) return;   // ignore les micro-mouvements

            wheelLocked = true;
            fpGoTo(fpIdx + (down ? 1 : -1));
        }, { passive: false });

        // ── Touch (mobile/tablet) ──
        let touchStartY = 0;
        let touchDeltaY = 0;
        const TOUCH_THRESHOLD = 70;

        window.addEventListener('touchstart', (e) => {
            if (!FP_ENABLED) return;
            touchStartY = e.touches[0].clientY;
            touchDeltaY = 0;
        }, { passive: true });

        window.addEventListener('touchmove', (e) => {
            if (!FP_ENABLED) return;
            touchDeltaY = touchStartY - e.touches[0].clientY;
        }, { passive: true });

        window.addEventListener('touchend', () => {
            if (!FP_ENABLED) return;
            if (Math.abs(touchDeltaY) > TOUCH_THRESHOLD) {
                fpGoTo(fpIdx + (touchDeltaY > 0 ? 1 : -1));
            }
        }, { passive: true });

        // ── Clavier (flèches, PageUp/Down, Espace, Home/End) ──
        // Si la section dépasse l'écran (ex: section 5 avec barre + footer), les flèches
        // défilent D'ABORD à l'intérieur ; arrivé au bord, elles changent de section.
        window.addEventListener('keydown', (e) => {
            if (!FP_ENABLED) return;
            const tag = (e.target.tagName || '').toLowerCase();
            if (['input', 'textarea', 'select'].includes(tag)) return;

            const cur = fpPages[fpIdx];
            const atBottom = cur.scrollTop + cur.clientHeight >= cur.scrollHeight - 2;
            const atTop = cur.scrollTop <= 2;
            const stepDown = e.key === 'PageDown' ? cur.clientHeight * 0.85 : 160;
            const stepUp = e.key === 'PageUp' ? cur.clientHeight * 0.85 : 160;

            if (['ArrowDown', 'PageDown', ' '].includes(e.key)) {
                e.preventDefault();
                if (!atBottom) cur.scrollBy({ top: stepDown, behavior: 'smooth' });
                else fpGoTo(fpIdx + 1);
            } else if (['ArrowUp', 'PageUp'].includes(e.key)) {
                e.preventDefault();
                if (!atTop) cur.scrollBy({ top: -stepUp, behavior: 'smooth' });
                else fpGoTo(fpIdx - 1);
            } else if (e.key === 'Home') { e.preventDefault(); fpGoTo(0); }
            else if (e.key === 'End') { e.preventDefault(); fpGoTo(fpPages.length - 1); }
        });

        // ── Liens internes (#anchors) ──
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetSel = this.getAttribute('href');
                if (!targetSel || targetSel === '#') return;
                const target = document.querySelector(targetSel);
                if (!target) return;
                const idx = fpPages.indexOf(target);
                if (FP_ENABLED && idx >= 0) {
                    e.preventDefault();
                    fpGoTo(idx);
                } else if (!FP_ENABLED) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // ── Initialiser : reveal animations de la première page ──
        if (FP_ENABLED && fpPages[0]) {
            fpRevealIn(fpPages[0]);
        } else {
            // Mode mobile : fallback IntersectionObserver classique
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
        }

        // Retirer "fp-preload" APRÈS le premier paint → réactive les transitions/animations
        // pour la NAVIGATION, alors qu'aucune ne joue au simple rafraîchissement.
        const fpRemovePreload = () => document.documentElement.classList.remove('fp-preload');
        requestAnimationFrame(() => requestAnimationFrame(fpRemovePreload));
        window.addEventListener('load', () => setTimeout(fpRemovePreload, 60));

        // ── Parallax léger sur les cercles décoratifs (souris) ──
        document.addEventListener('mousemove', (e) => {
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
                if (FP_ENABLED) {
                    fpGoTo(0); // Remonte tout en haut (index 0) avec une belle transition
                } else {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        }

    </script>

</body>

</html>