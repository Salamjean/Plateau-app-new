<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivre ma demande — Plateau Smart City</title>

    <link rel="shortcut icon" href="{{ asset('assets/assets/img/logo plateau.png') }}" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #0d2a54 0%, #051429 100%);
            color: #ffffff;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
            padding: 32px 64px;
        }

        .background-skyline {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 450px;
            background: url('{{ asset("assets/landing/about-background.png") }}') center bottom / cover no-repeat;
            opacity: 0.08;
            z-index: 1;
            pointer-events: none;
        }

        /* ─────────────── HEADER ─────────────── */
        .top-bar {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-shrink: 0;
            width: 100%;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-img {
            height: 52px;
            width: auto;
            object-fit: contain;
            border-radius: 6px;
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

        /* ─────────────── BREADCRUMB ─────────────── */
        .breadcrumb {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            width: 100%;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .breadcrumb a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            transition: color 0.3s;
            display: inline-flex;
            align-items: center;
        }

        .breadcrumb a:hover {
            color: var(--success);
        }

        .breadcrumb .separator {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.4);
        }

        .breadcrumb .active {
            color: #ffffff;
            font-weight: 500;
        }

        /* ─────────────── MAIN CONTENT ─────────────── */
        .main-content {
            flex: 1;
            max-width: 1400px;
            margin: 0 auto 60px auto;
            width: 100%;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 60px;
            align-items: start;
            position: relative;
            z-index: 5;
        }

        /* ─────────────── LEFT COLUMN ─────────────── */
        .left-column {
            display: flex;
            flex-direction: column;
        }

        .section-label {
            color: var(--success);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 12px;
            display: block;
            text-transform: uppercase;
        }

        .main-title {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 20px;
            color: #ffffff;
            letter-spacing: -1px;
        }

        .main-title span {
            color: var(--success);
        }

        .description {
            font-size: 15px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 35px;
            max-width: 520px;
        }

        /* Form Card */
        .search-form-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 30px;
            max-width: 520px;
            margin-bottom: 40px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .search-input-wrapper {
            position: relative;
            width: 100%;
        }

        .search-input-wrapper .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.4);
            font-size: 16px;
        }

        .search-input-wrapper input {
            width: 100%;
            padding: 16px 20px 16px 52px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #ffffff;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .search-input-wrapper input:focus {
            outline: none;
            border-color: var(--success);
            background: rgba(0, 0, 0, 0.3);
            box-shadow: 0 0 0 4px rgba(1, 181, 116, 0.15);
        }

        .help-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            transition: color 0.3s;
            margin-top: 10px;
            cursor: pointer;
        }

        .help-link:hover {
            color: var(--success);
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: var(--success);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background: var(--success-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(1, 181, 116, 0.3);
        }

        .security-notice {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.4);
            font-size: 12px;
            line-height: 1.5;
        }

        .security-notice i {
            font-size: 16px;
            color: var(--success);
            margin-top: 2px;
        }

        .security-notice strong {
            color: rgba(255, 255, 255, 0.7);
            display: block;
            margin-bottom: 2px;
        }

        /* Alert styling */
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            padding: 14px 16px;
            color: #fca5a5;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .alert-error i {
            font-size: 16px;
            color: #ef4444;
        }

        /* ─────────────── RESULT MODAL (popup when found) ─────────────── */
        .result-modal-overlay {
            display: none;
            position: fixed;
            z-index: 2000;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .result-modal-overlay.open {
            display: flex;
        }

        .result-card {
            background: linear-gradient(145deg, rgba(15, 28, 48, 0.98), rgba(8, 18, 35, 0.98));
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 32px;
            max-width: 560px;
            width: 100%;
            max-height: 85vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.15) transparent;
            backdrop-filter: blur(20px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
            position: relative;
            overflow-x: hidden;
            animation: resultModalIn 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes resultModalIn {
            from {
                opacity: 0;
                transform: scale(0.92) translateY(20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .result-card::-webkit-scrollbar {
            width: 6px;
        }

        .result-card::-webkit-scrollbar-track {
            background: transparent;
        }

        .result-card::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 3px;
        }

        .result-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-light), var(--success));
        }

        .result-close-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s;
            z-index: 10;
        }

        .result-close-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            transform: rotate(90deg);
        }

        .result-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
            padding-right: 40px;
        }

        .result-header-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(1, 181, 116, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--success);
            font-size: 20px;
            flex-shrink: 0;
        }

        .result-header-text h3 {
            font-size: 18px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 2px;
        }

        .result-header-text span {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
        }

        .result-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .result-info-box {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 14px;
        }

        .result-info-box .info-label {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: block;
        }

        .result-info-box .info-value {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
        }

        .result-info-box .info-value.status-success {
            color: var(--success);
        }

        .result-info-box .info-value.status-warning {
            color: #f59e0b;
        }

        .result-info-box .info-value.status-danger {
            color: #ef4444;
        }

        .result-info-box .info-value.status-info {
            color: var(--primary-light);
        }

        /* Progress Bar */
        .result-progress {
            margin-bottom: 24px;
        }

        .result-progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .result-progress-header span {
            font-size: 13px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.7);
        }

        .result-progress-badge {
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 800;
            color: #ffffff;
        }

        .result-progress-badge.bg-success { background: var(--success); }
        .result-progress-badge.bg-warning { background: #f59e0b; }
        .result-progress-badge.bg-danger { background: #ef4444; }
        .result-progress-badge.bg-info { background: var(--primary-light); }
        .result-progress-badge.bg-primary { background: var(--primary); }

        .result-progress-track {
            width: 100%;
            height: 10px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 99px;
            overflow: hidden;
        }

        .result-progress-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 0.6s ease;
            background: linear-gradient(90deg, var(--primary-light), var(--success));
        }

        .result-progress-fill.fill-warning {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

        .result-progress-fill.fill-danger {
            background: linear-gradient(90deg, #ef4444, #f87171);
        }

        .result-progress-desc {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .result-progress-desc i {
            color: var(--primary-light);
        }

        /* Step Tracker */
        .result-stepper {
            position: relative;
            padding: 0 4px;
        }

        .result-stepper-line {
            position: absolute;
            top: 20px;
            left: 16.66%;
            right: 16.66%;
            height: 3px;
            background: rgba(255, 255, 255, 0.1);
            z-index: 1;
            border-radius: 2px;
        }

        .result-stepper-line-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: var(--success);
            transition: width 0.5s ease-in-out;
            z-index: 2;
            border-radius: 2px;
        }

        .result-stepper-line-fill.fill-danger {
            background: #ef4444;
        }

        .result-stepper-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 3;
        }

        .result-stepper-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 33.33%;
        }

        .result-stepper-bubble {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.3);
            margin-bottom: 10px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .result-stepper-item.completed .result-stepper-bubble {
            background: rgba(1, 181, 116, 0.15);
            border-color: var(--success);
            color: var(--success);
            box-shadow: 0 0 0 4px rgba(1, 181, 116, 0.1);
        }

        .result-stepper-item.active .result-stepper-bubble {
            background: rgba(79, 172, 254, 0.15);
            border-color: var(--primary-light);
            color: var(--primary-light);
            box-shadow: 0 0 0 4px rgba(79, 172, 254, 0.1);
            animation: pulse-stepper 2s infinite;
        }

        .result-stepper-item.rejected .result-stepper-bubble {
            background: rgba(239, 68, 68, 0.15);
            border-color: #ef4444;
            color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        @keyframes pulse-stepper {
            0% { box-shadow: 0 0 0 0 rgba(79, 172, 254, 0.3); }
            70% { box-shadow: 0 0 0 8px rgba(79, 172, 254, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 172, 254, 0); }
        }

        .result-stepper-title {
            font-size: 12px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 2px;
        }

        .result-stepper-item.completed .result-stepper-title {
            color: var(--success);
        }

        .result-stepper-item.active .result-stepper-title {
            color: var(--primary-light);
        }

        .result-stepper-item.rejected .result-stepper-title {
            color: #ef4444;
        }

        .result-stepper-desc {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.35);
            max-width: 110px;
            line-height: 1.3;
        }

        /* Rejection alert */
        .result-rejection-alert {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
        }

        .result-rejection-alert .rejection-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .result-rejection-alert .rejection-header i {
            color: #ef4444;
            font-size: 16px;
        }

        .result-rejection-alert .rejection-header strong {
            color: #fca5a5;
            font-size: 14px;
        }

        .result-rejection-alert .rejection-body {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.5;
        }

        .result-rejection-alert .rejection-motif {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.15);
            border-radius: 8px;
            padding: 12px;
            margin-top: 8px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.5;
        }

        /* Status Grid Cards */
        .status-cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            max-width: 100%;
            margin-top: 20px;
        }

        .status-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s;
        }

        .status-card:hover {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-4px);
        }

        .status-card .icon-box {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(25, 119, 204, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-light);
            font-size: 16px;
            margin-bottom: 12px;
        }

        .status-card h4 {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .status-card p {
            font-size: 12px;
            line-height: 1.4;
            color: rgba(255, 255, 255, 0.5);
        }

        /* ─────────────── RIGHT COLUMN (IPHONE MOCKUP) ─────────────── */
        .phone-column {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            width: 100%;
        }

        .phone-circle-bg {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79, 172, 254, 0.12) 0%, rgba(79, 172, 254, 0) 70%);
            z-index: 1;
            pointer-events: none;
        }

        .iphone-frame {
            width: 320px;
            height: 640px;
            background: #000000;
            border: 10px solid #1f2937;
            border-radius: 44px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 2;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .iphone-island {
            width: 100px;
            height: 24px;
            background: #000000;
            border-radius: 15px;
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 100;
        }

        .iphone-screen {
            flex: 1;
            background: #f4f6f9;
            color: #0f172a;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            scrollbar-width: none;
            position: relative;
            padding: 0 0 20px 0;
        }

        .iphone-screen::-webkit-scrollbar {
            display: none;
        }

        .screen-blue-header {
            background: linear-gradient(180deg, #103370 0%, #0d2859 100%);
            padding: 45px 18px 45px;
            color: #ffffff;
            border-bottom-left-radius: 28px;
            border-bottom-right-radius: 28px;
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .screen-status-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            font-weight: 600;
            opacity: 0.9;
            margin-bottom: 12px;
            padding: 0 4px;
        }

        .screen-status-bar .icons {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .screen-blue-header .screen-header-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
        }

        .screen-blue-header .screen-header-nav h3 {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
        }

        .screen-blue-header .screen-header-nav .back-btn,
        .screen-blue-header .screen-header-nav .more-btn {
            color: #ffffff;
            opacity: 0.8;
            font-size: 15px;
            cursor: pointer;
        }

        .tracking-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            margin: -25px 14px 16px;
            position: relative;
            z-index: 2;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .tracking-card .ref-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }

        .tracking-card .ref-label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tracking-card .ref-value {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 10px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-badge.pending {
            background: #fff7ed;
            color: #ea580c;
        }

        .status-badge.validated {
            background: #f0fdf4;
            color: #16a34a;
        }

        .status-badge.rejected {
            background: #fef2f2;
            color: #dc2626;
        }

        .status-badge.processing {
            background: #f0f9ff;
            color: #0284c7;
        }

        .details-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .detail-item.justify-between {
            justify-content: space-between;
        }

        .detail-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(16, 51, 112, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #103370;
            font-size: 13px;
            flex-shrink: 0;
        }

        .detail-text {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 9px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .detail-value {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        .total-qty-badge {
            background: #eef5fc;
            border-radius: 8px;
            padding: 4px 10px;
            text-align: center;
            border: 1.5px solid #d0e3f7;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 48px;
        }

        .total-qty-badge .num {
            font-size: 14px;
            font-weight: 800;
            color: #103370;
            line-height: 1;
        }

        .total-qty-badge .lbl {
            font-size: 8px;
            font-weight: 700;
            color: #5b75a6;
            text-transform: uppercase;
            margin-top: 1px;
        }

        /* Steps */
        .steps-container {
            background: #ffffff;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            margin: 0 14px 16px;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .steps-container h4 {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .steps-list {
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .steps-list::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 10px;
            bottom: 10px;
            width: 2px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step-item {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .step-item:last-child {
            margin-bottom: 0;
        }

        .step-marker {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #94a3b8;
            font-weight: 700;
            flex-shrink: 0;
            transition: all 0.3s;
        }

        .step-item.active .step-marker {
            background: #103370;
            border-color: #103370;
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(16, 51, 112, 0.15);
        }

        .step-item.completed .step-marker {
            background: var(--success);
            border-color: var(--success);
            color: #ffffff;
        }

        .step-content {
            display: flex;
            flex-direction: column;
        }

        .step-title {
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 2px;
        }

        .step-item.active .step-title,
        .step-item.completed .step-title {
            color: #0f172a;
        }

        .step-desc {
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.4;
        }

        .step-item.active .step-desc {
            color: #64748b;
        }

        /* Mockup containers for user's static image */
        .phone-image-container {
            width: 320px;
            height: 640px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
            z-index: 2;
            border-radius: 44px;
            filter: drop-shadow(0 25px 50px rgba(0, 0, 0, 0.4));
        }

        .iphone-image-mockup {
            height: 125%;
            width: auto;
            object-fit: contain;
            display: block;
        }

        /* Demo badge overlay */
        .demo-badge-overlay {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 16px;
            font-size: 12px;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .demo-badge-overlay i {
            color: var(--primary);
        }

        /* ─────────────── MODAL ─────────────── */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.open {
            display: flex;
        }

        .modal-content {
            background: #0f1c30;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            max-width: 450px;
            width: 100%;
            padding: 24px;
            color: #ffffff;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
        }

        .close-modal {
            font-size: 24px;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            transition: color 0.3s;
        }

        .close-modal:hover {
            color: #ffffff;
        }

        .modal-body p {
            font-size: 14px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 12px;
        }

        .modal-body strong {
            color: var(--success);
        }

        /* ─────────────── DOWNLOAD BAR & FOOTER ─────────────── */
        .download-bar {
            position: relative;
            z-index: 5;
            background: #122554;
            border-radius: 0;
            margin: 40px -64px 0 -64px;
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

        .download-bar > p {
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

        .download-trust .stars-icons {
            color: #fbbf24;
            font-size: 13px;
            letter-spacing: 2px;
        }

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

        .footer-grid-wrapper {
            position: relative;
            background: #081a42;
            overflow: hidden;
            margin: 0 -64px -32px -64px;
            padding: 0 64px;
        }

        .footer-grid-bg {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100%;
            background-image: linear-gradient(rgba(255,255,255,0.06) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 40px 40px;
            background-position: center bottom;
            transform: perspective(600px) rotateX(60deg) scale(2);
            transform-origin: bottom center;
            z-index: 1;
        }

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

        /* ─────────────── RESPONSIVE ─────────────── */
        /* Mobile-only view */
        .mobile-search-view {
            display: none;
        }

        /* Footer mobile + bouton "réinitialiser" : cachés sur desktop, visibles en responsive */
        .mob-reset-search { display: none; }

        @media (max-width: 991px) {
            /* Hide desktop layout on mobile */
            .top-bar,
            .breadcrumb,
            .main-content,
            .background-skyline {
                display: none !important;
            }

            /* Allow the desktop grid's container to render on mobile when results exist, but as a simple block */
            body.rn-has-result .main-content {
                display: block !important;
            }
            body.rn-has-result .left-column {
                display: none !important;
            }

            body {
                padding: 0;
                background: #e8edf4;
                min-height: 100vh;
            }

            .mobile-search-view {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            /* Mobile Header */
            .mob-header {
                background: linear-gradient(135deg, #103370 0%, #0d2859 100%);
                padding: 48px 24px 36px 76px; /* Increased left padding from 24px to 76px to safely clear the back button */
                border-radius: 0 0 32px 32px;
                display: flex;
                align-items: center;
                gap: 16px;
                position: relative;
            }

            .mob-header-icon {
                width: 52px;
                height: 52px;
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.12);
                display: flex;
                align-items: center;
                justify-content: center;
                color: #ffffff;
                font-size: 22px;
                flex-shrink: 0;
            }

            .mob-header-text h1 {
                font-size: 22px;
                font-weight: 800;
                color: #ffffff;
                margin-bottom: 4px;
                line-height: 1.2;
            }

            .mob-header-text p {
                font-size: 14px;
                color: rgba(255, 255, 255, 0.6);
                font-weight: 400;
            }

            .mob-back-btn {
                position: absolute;
                top: 50%;
                left: 20px;
                transform: translateY(-50%);
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                color: #ffffff;
                text-decoration: none;
                font-size: 14px;
            }

            /* Mobile Form Card */
            .mob-form-card {
                background: #ffffff;
                border-radius: 20px;
                padding: 24px 20px;
                margin: -16px 16px 0;
                position: relative;
                z-index: 10;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            }

            .mob-form-card .mob-form-label {
                font-size: 11px;
                font-weight: 700;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 12px;
                display: block;
            }

            .mob-form-card .mob-input-wrap {
                position: relative;
                margin-bottom: 14px;
            }

            .mob-form-card .mob-input-wrap .mob-input-icon {
                position: absolute;
                left: 16px;
                top: 50%;
                transform: translateY(-50%);
                font-size: 18px;
                color: #94a3b8;
                font-weight: 700;
            }

            .mob-form-card .mob-input-wrap input {
                width: 100%;
                padding: 16px 16px 16px 48px;
                background: #f8fafc;
                border: 1.5px solid #e2e8f0;
                border-radius: 14px;
                font-size: 15px;
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-weight: 500;
                color: #0f172a;
                transition: all 0.3s;
            }

            .mob-form-card .mob-input-wrap input:focus {
                outline: none;
                border-color: #103370;
                background: #ffffff;
                box-shadow: 0 0 0 4px rgba(16, 51, 112, 0.08);
            }

            .mob-form-card .mob-input-wrap input::placeholder {
                color: #94a3b8;
                font-weight: 500;
            }

            .mob-form-card .mob-submit-btn {
                width: 100%;
                padding: 16px;
                background: #e2e8f0;
                border: none;
                border-radius: 14px;
                font-size: 14px;
                font-weight: 700;
                font-family: 'Plus Jakarta Sans', sans-serif;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                transition: all 0.3s;
            }

            .mob-form-card .mob-submit-btn:hover,
            .mob-form-card .mob-submit-btn:active {
                background: #103370;
                color: #ffffff;
            }

            .mob-alert-error {
                background: #fef2f2;
                border: 1px solid #fecaca;
                border-radius: 12px;
                padding: 12px 14px;
                margin-bottom: 14px;
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 13px;
                color: #dc2626;
                font-weight: 600;
            }

            .mob-alert-error i {
                font-size: 16px;
                flex-shrink: 0;
            }

            /* Mobile Info Sections */
            .mob-info-sections {
                padding: 28px 20px;
                display: flex;
                flex-direction: column;
                gap: 24px;
            }

            .mob-info-item {
                display: flex;
                align-items: flex-start;
                gap: 16px;
            }

            .mob-info-icon {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: rgba(16, 51, 112, 0.06);
                border: 1.5px solid rgba(16, 51, 112, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                color: #103370;
                font-size: 16px;
                flex-shrink: 0;
            }

            .mob-info-text h4 {
                font-size: 15px;
                font-weight: 800;
                color: #0f172a;
                margin-bottom: 4px;
            }

            .mob-info-text p {
                font-size: 13px;
                color: #64748b;
                line-height: 1.5;
                font-weight: 400;
            }

            /* Mobile Result Card (inline, not popup) */
            .mob-result-card {
                background: #ffffff;
                border-radius: 20px;
                padding: 24px 20px;
                margin: 20px 16px 0;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
                position: relative;
                overflow: hidden;
            }

            .mob-result-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #103370, var(--success));
            }

            .mob-result-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 20px;
            }

            .mob-result-header-icon {
                width: 44px;
                height: 44px;
                border-radius: 14px;
                background: rgba(1, 181, 116, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--success);
                font-size: 18px;
            }

            .mob-result-header-text h3 {
                font-size: 17px;
                font-weight: 800;
                color: #0f172a;
                margin-bottom: 2px;
            }

            .mob-result-header-text span {
                font-size: 12px;
                color: #94a3b8;
            }

            .mob-result-info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-bottom: 20px;
            }

            .mob-result-info-box {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 12px;
            }

            .mob-result-info-box .mob-info-label {
                font-size: 9px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 4px;
                display: block;
            }

            .mob-result-info-box .mob-info-value {
                font-size: 13px;
                font-weight: 700;
                color: #0f172a;
            }

            .mob-result-info-box .mob-info-value.mob-status-success {
                color: var(--success);
            }

            .mob-result-info-box .mob-info-value.mob-status-warning {
                color: #f59e0b;
            }

            .mob-result-info-box .mob-info-value.mob-status-danger {
                color: #ef4444;
            }

            .mob-result-info-box .mob-info-value.mob-status-info {
                color: #103370;
            }

            /* Mobile Progress */
            .mob-progress {
                margin-bottom: 20px;
            }

            .mob-progress-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 8px;
            }

            .mob-progress-header span {
                font-size: 13px;
                font-weight: 700;
                color: #475569;
            }

            .mob-progress-badge {
                padding: 3px 10px;
                border-radius: 99px;
                font-size: 11px;
                font-weight: 800;
                color: #ffffff;
            }

            .mob-progress-badge.mob-bg-success { background: var(--success); }
            .mob-progress-badge.mob-bg-warning { background: #f59e0b; }
            .mob-progress-badge.mob-bg-danger { background: #ef4444; }
            .mob-progress-badge.mob-bg-info { background: #103370; }
            .mob-progress-badge.mob-bg-primary { background: var(--primary); }

            .mob-progress-track {
                width: 100%;
                height: 8px;
                background: #e2e8f0;
                border-radius: 99px;
                overflow: hidden;
            }

            .mob-progress-fill {
                height: 100%;
                border-radius: 99px;
                transition: width 0.6s ease;
                background: linear-gradient(90deg, #103370, var(--success));
            }

            .mob-progress-fill.mob-fill-warning {
                background: linear-gradient(90deg, #f59e0b, #fbbf24);
            }

            .mob-progress-fill.mob-fill-danger {
                background: linear-gradient(90deg, #ef4444, #f87171);
            }

            .mob-progress-desc {
                font-size: 12px;
                color: #64748b;
                margin-top: 8px;
                display: flex;
                align-items: flex-start;
                gap: 6px;
                line-height: 1.4;
            }

            .mob-progress-desc i {
                color: #103370;
                margin-top: 2px;
                flex-shrink: 0;
            }

            /* Mobile Stepper */
            .mob-stepper {
                display: flex;
                flex-direction: column;
                gap: 0;
                position: relative;
                padding-left: 20px;
            }

            .mob-stepper::before {
                content: '';
                position: absolute;
                left: 20px;
                top: 18px;
                bottom: 18px;
                width: 2px;
                background: #e2e8f0;
                z-index: 1;
            }

            .mob-step-item {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 14px 0;
                position: relative;
                z-index: 2;
            }

            .mob-step-bubble {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: #ffffff;
                border: 2px solid #e2e8f0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
                color: #94a3b8;
                flex-shrink: 0;
                transition: all 0.3s;
            }

            .mob-step-item.completed .mob-step-bubble {
                background: #dcfce7;
                border-color: #22c55e;
                color: #15803d;
            }

            .mob-step-item.active .mob-step-bubble {
                background: #e0f2fe;
                border-color: #0284c7;
                color: #0284c7;
                box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12);
            }

            .mob-step-item.rejected .mob-step-bubble {
                background: #fee2e2;
                border-color: #ef4444;
                color: #b91c1c;
            }

            .mob-step-text h5 {
                font-size: 14px;
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 1px;
            }

            .mob-step-text p {
                font-size: 12px;
                color: #94a3b8;
                line-height: 1.3;
            }

            .mob-step-item.completed .mob-step-text h5 {
                color: #15803d;
            }

            .mob-step-item.active .mob-step-text h5 {
                color: #0284c7;
            }

            .mob-step-item.rejected .mob-step-text h5 {
                color: #b91c1c;
            }

            /* Mobile Rejection */
            .mob-rejection-alert {
                background: #fef2f2;
                border: 1px solid #fecaca;
                border-radius: 12px;
                padding: 14px;
                margin-top: 16px;
            }

            .mob-rejection-alert .mob-rej-header {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 8px;
            }

            .mob-rejection-alert .mob-rej-header i {
                color: #ef4444;
            }

            .mob-rejection-alert .mob-rej-header strong {
                color: #b91c1c;
                font-size: 14px;
            }

            .mob-rejection-alert .mob-rej-body {
                font-size: 13px;
                color: #64748b;
                line-height: 1.5;
            }

            .mob-rejection-alert .mob-rej-motif {
                background: #ffffff;
                border: 1px solid #fecaca;
                border-radius: 8px;
                padding: 10px;
                margin-top: 8px;
                font-size: 13px;
                color: #0f172a;
                line-height: 1.5;
            }

            /* Hide result popup on mobile */
            .result-modal-overlay {
                display: none !important;
            }

            /* ═══════ RÉSULTAT MOBILE ═══════ */
            .mob-result-card { display: none !important; }

            .phone-column { display: none !important; }
            .phone-circle-bg { display: none !important; }

            body.rn-has-result .phone-column {
                display: flex !important;
                justify-content: center;
                align-items: flex-start;
                width: 100%;
                min-height: 100vh;
                padding: 80px 16px 40px; /* Increased top padding from 36px to 80px to safely clear the fixed back button */
                background: #e8edf4;
            }

            /* Quand la référence est trouvée, le mockup REMPLACE l'écran de recherche */
            body.rn-has-result .mobile-search-view {
                display: none !important;
            }

            /* Transition d'apparition (glisse vers le haut + fondu) */
            body.rn-has-result .iphone-frame {
                animation: rnMobPhoneIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
            }
            @keyframes rnMobPhoneIn {
                from { opacity: 0; transform: translateY(50px) scale(0.94); }
                to   { opacity: 1; transform: translateY(0) scale(1); }
            }

            /* ═══════ Bouton "Nouvelle recherche" ═══════ */
            body.rn-has-result .mob-reset-search {
                display: flex;
                position: fixed;
                top: 16px;
                left: 16px;
                z-index: 200;
                width: 46px;
                height: 46px;
                border-radius: 50%;
                background: #103370;
                color: #ffffff;
                align-items: center;
                justify-content: center;
                font-size: 17px;
                text-decoration: none;
                box-shadow: 0 8px 20px rgba(16, 51, 112, 0.35);
                transition: transform 0.25s, background 0.25s;
            }
            body.rn-has-result .mob-reset-search:active,
            body.rn-has-result .mob-reset-search:hover {
                background: #0d2859;
                transform: scale(1.06);
            }

            /* ═══════ Footer mobile + Download bar ═══════ */
            .download-bar {
                margin: 40px -20px 0 !important;
                padding: 40px 20px !important;
                border-radius: 40px 40px 0 0 !important;
            }
            .download-bar h3 { font-size: 24px !important; }
            .download-row { flex-direction: column !important; gap: 20px; }
            .download-trust { border-right: none !important; padding-right: 0 !important; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 20px; justify-content: center; }
            .btn-store-dark { width: 100%; justify-content: center; max-width: 280px; }

            .footer-grid-wrapper { margin: 0 -20px !important; padding: 0 20px !important; }
            .footer-container { grid-template-columns: 1fr !important; text-align: center; gap: 25px !important; }
            .footer-left { flex-direction: column; align-items: center; gap: 15px; }
            .footer-left .footer-tagline { border-left: none !important; padding-left: 0 !important; }
            .footer-right { text-align: center !important; }
        }
    </style>
</head>

<body class="{{ $demande ? 'rn-has-result' : '' }}">
    <div class="background-skyline"></div>

    {{-- Bouton "Nouvelle recherche" — visible sur mobile quand un résultat est affiché --}}
    <a href="{{ route('recherche.demande') }}" class="mob-reset-search" aria-label="Nouvelle recherche" title="Nouvelle recherche">
        <i class="fas fa-arrow-left"></i>
    </a>

    <!-- HEADER : Logo + Boutons -->
    <header class="top-bar">
        <div class="logo">
            <a href="{{ route('home') }}">
                <img src="{{ asset('assets/assets/img/plateau-mart1.png') }}" alt="Plateau Smart City" class="logo-img">
            </a>
        </div>

        <nav class="nav-buttons">
            <a href="{{ route('recherche.demande') }}" class="btn-pill outline">
                <i class="fas fa-search"></i> Suivre ma demande
            </a>
            <a href="#" onclick="checkOpening(event)" class="btn-pill solid">
                <i class="fas fa-user"></i> Mon espace
            </a>
        </nav>
    </header>

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="{{ route('home') }}"><i class="fas fa-home"></i></a>
        <span class="separator"><i class="fas fa-chevron-right"></i></span>
        <span class="active">Suivre ma demande</span>
    </div>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- COLUMN LEFT -->
        <div class="left-column">
            <span class="section-label">Suivi de demande</span>
            <h1 class="main-title">Suivez l'avancement<br><span>de votre demande</span></h1>
            <p class="description">
                Renseignez votre numéro de référence pour consulter l'état d'avancement de votre dossier en temps réel.
            </p>

            <div class="search-form-card">
                <!-- Message d'erreur si référence introuvable -->
                @if ($etatDemande === false)
                    <div class="alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Aucune demande trouvée pour la référence <strong>{{ $reference }}</strong>.</span>
                    </div>
                @endif

                <form action="{{ route('recherche.demande') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="reference_naissance">Saisissez le numéro de référence figurant sur votre récépissé</label>
                        <div class="search-input-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="reference_naissance" id="reference_naissance"
                                placeholder="Ex : AN123456PXXXX" value="{{ $reference ?? '' }}" required>
                        </div>
                        <a class="help-link" id="helpLink">
                            <i class="far fa-question-circle"></i> Où trouver mon numéro de référence ?
                        </a>
                    </div>

                    <button type="submit" class="btn-submit">
                        Suivre ma demande <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <div class="security-notice">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong>Vos données sont sécurisées</strong>
                        Plateau Apps garantit la confidentialité et la protection de toutes vos informations.
                    </div>
                </div>
            </div>

            <!-- CARDS GRID -->
            <div class="status-cards-grid">
                <div class="status-card">
                    <div class="icon-box"><i class="far fa-clock"></i></div>
                    <h4>Suivi en temps réel</h4>
                    <p>Consultez l'état d'avancement à tout moment.</p>
                </div>
                <div class="status-card">
                    <div class="icon-box"><i class="far fa-bell"></i></div>
                    <h4>Notifications</h4>
                    <p>Soyez informé à chaque étape clé.</p>
                </div>
                <div class="status-card">
                    <div class="icon-box"><i class="fas fa-shield-alt"></i></div>
                    <h4>Sécurisé</h4>
                    <p>Vos informations sont chiffrées et protégées.</p>
                </div>
                <div class="status-card">
                    <div class="icon-box"><i class="fas fa-headset"></i></div>
                    <h4>Besoin d'aide ?</h4>
                    <p>Contactez notre support pour toute assistance.</p>
                </div>
            </div>
        </div>

        <!-- COLUMN RIGHT (IPHONE MOCKUP) -->
        <div class="phone-column">
            <div class="phone-circle-bg"></div>

            @if ($demande)
                <div class="iphone-frame">
                    <div class="iphone-island"></div>
                    <div class="iphone-screen">
                        
                        <!-- Header with blue background, status bar, and nav -->
                        <div class="screen-blue-header">
                            <div class="screen-status-bar">
                                <span>9:41</span>
                                <div class="icons">
                                    <i class="fas fa-signal"></i>
                                    <i class="fas fa-wifi"></i>
                                    <i class="fas fa-battery-three-quarters"></i>
                                </div>
                            </div>
                            <div class="screen-header-nav">
                                <i class="fas fa-chevron-left back-btn"></i>
                                <h3>Détails du suivi</h3>
                                <i class="fas fa-ellipsis-h more-btn"></i>
                            </div>
                        </div>

                        <!-- Card displaying tracking details -->
                        <div class="tracking-card">
                            <div class="ref-line">
                                <div>
                                    <span class="ref-label">Référence</span>
                                    <div class="ref-value">{{ $demande->reference }}</div>
                                </div>
                                @if ($demande->etat === 'valide' || $demande->etat === 'termine')
                                    <span class="status-badge validated"><i class="fas fa-circle" style="font-size: 6px; margin-right: 2px;"></i> Validée</span>
                                @elseif($demande->etat === 'rejete' || $demande->etat === 'rejeter')
                                    <span class="status-badge rejected"><i class="fas fa-circle" style="font-size: 6px; margin-right: 2px;"></i> Rejetée</span>
                                @elseif($demande->etat === 'encours')
                                    <span class="status-badge processing"><i class="fas fa-circle" style="font-size: 6px; margin-right: 2px;"></i> En cours</span>
                                @else
                                    <span class="status-badge pending"><i class="fas fa-circle" style="font-size: 6px; margin-right: 2px;"></i> En attente</span>
                                @endif
                            </div>

                            <div class="details-list">
                                <div class="detail-item">
                                    <div class="detail-icon"><i class="far fa-file-alt"></i></div>
                                    <div class="detail-text">
                                        <span class="detail-label">Type de document</span>
                                        <span class="detail-value">
                                            @if (isset($demande->nom_prenoms_pere))
                                                Extrait de Naissance
                                            @elseif(isset($demande->defunt_nom))
                                                Acte de Décès
                                            @else
                                                Acte de Mariage
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="detail-item justify-between">
                                    <div style="display: flex; gap: 12px; align-items: center;">
                                        <div class="detail-icon"><i class="fas fa-copy"></i></div>
                                        <div class="detail-text">
                                            <span class="detail-label">Nature du document</span>
                                            <span class="detail-value">
                                                @if ($demande->qty_integral > 0 && $demande->qty_simple > 0)
                                                    Copie Intégrale ({{ $demande->qty_integral }}) & Simple ({{ $demande->qty_simple }})
                                                @elseif($demande->qty_integral > 0)
                                                    Copie Intégrale ({{ $demande->qty_integral }})
                                                @else
                                                    Copie Simple ({{ $demande->qty_simple ?? 1 }})
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    @php
                                        $totalQty = ($demande->qty_simple ?? 0) + ($demande->qty_integral ?? 0);
                                        $totalQty = $totalQty > 0 ? $totalQty : ($demande->quantite ?? 1);
                                    @endphp
                                    <div class="total-qty-badge">
                                        <span class="num">{{ $totalQty }}</span>
                                        <span class="lbl">Total</span>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon"><i class="fas fa-university"></i></div>
                                    <div class="detail-text">
                                        <span class="detail-label">Mode de retrait</span>
                                        <span class="detail-value">{{ $statutDemande ?? 'Retrait en Mairie' }}</span>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon"><i class="far fa-calendar-alt"></i></div>
                                    <div class="detail-text">
                                        <span class="detail-label">Date de la demande</span>
                                        <span class="detail-value">{{ $demande->created_at ? $demande->created_at->format('d/m/Y à H:i') : 'Récemment' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Steps -->
                        <div class="steps-container">
                            <h4>Étapes d'avancement</h4>
                            <div class="steps-list">
                                <!-- Étape 1 : Réception -->
                                <div class="step-item {{ in_array($demande->etat, ['attente', 'en attente', 'encours', 'valide', 'termine']) ? 'completed' : '' }}">
                                    <div class="step-marker">
                                        @if(in_array($demande->etat, ['attente', 'en attente', 'encours', 'valide', 'termine']))
                                            <i class="fas fa-check"></i>
                                        @else
                                            1
                                        @endif
                                    </div>
                                    <div class="step-content">
                                        <div class="step-title">Réception</div>
                                        <div class="step-desc">Votre demande a été reçue et enregistrée.</div>
                                    </div>
                                </div>

                                <!-- Étape 2 : Vérification -->
                                <div class="step-item @if($demande->etat === 'encours') active @elseif(in_array($demande->etat, ['valide', 'termine'])) completed @endif">
                                    <div class="step-marker">
                                        @if(in_array($demande->etat, ['valide', 'termine']))
                                            <i class="fas fa-check"></i>
                                        @else
                                            2
                                        @endif
                                    </div>
                                    <div class="step-content">
                                        <div class="step-title">Vérification</div>
                                        <div class="step-desc">Examen des pièces justificatives par nos officiers.</div>
                                    </div>
                                </div>

                                <!-- Étape 3 : Retrait / Livraison -->
                                <div class="step-item @if(in_array($demande->etat, ['valide', 'termine'])) completed @endif">
                                    <div class="step-marker">
                                        @if(in_array($demande->etat, ['valide', 'termine']))
                                            <i class="fas fa-check"></i>
                                        @else
                                            3
                                        @endif
                                    </div>
                                    <div class="step-content">
                                        <div class="step-title">Mise à disposition</div>
                                        <div class="step-desc">
                                            @if($statutDemande === 'DHL' || $statutDemande === 'Livreur')
                                                Document expédié à l'adresse de livraison.
                                            @else
                                                Document disponible pour retrait en Mairie.
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="phone-image-container">
                    <img src="{{ asset('assets/assets/img/FaceTEL.png') }}" class="iphone-image-mockup" alt="Suivi de demande">
                </div>
            @endif
        </div>
    </main>

    {{-- ─────── MOBILE SEARCH VIEW ─────── --}}
    <div class="mobile-search-view">
        {{-- Mobile Header --}}
        <div class="mob-header">
            <a href="{{ route('home') }}" class="mob-back-btn"><i class="fas fa-chevron-left"></i></a>
            <div class="mob-header-icon">
                <i class="fas fa-search"></i>
            </div>
            <div class="mob-header-text">
                <h1>Rechercher un dossier</h1>
                <p>Entrez votre référence ci-dessous.</p>
            </div>
        </div>

        {{-- Mobile Form Card --}}
        <div class="mob-form-card">
            @if ($etatDemande === false)
                <div class="mob-alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Aucune demande trouvée pour <strong>{{ $reference }}</strong>.</span>
                </div>
            @endif

            <form action="{{ route('recherche.demande') }}" method="POST">
                @csrf
                <span class="mob-form-label">Référence de la demande</span>
                <div class="mob-input-wrap">
                    <span class="mob-input-icon">#</span>
                    <input type="text" name="reference_naissance" placeholder="Entrez la référence" value="{{ $reference ?? '' }}" required>
                </div>
                <button type="submit" class="mob-submit-btn">
                    <i class="fas fa-search"></i> RECHERCHER
                </button>
            </form>
        </div>

        {{-- Mobile Result (inline, when found) --}}
        @if ($demande)
            @php
                $mProgress = 10;
                $mBadgeClass = 'mob-bg-primary';
                $mFillClass = '';
                $mStatusLabel = $etatDemande;
                $mStatusDesc = '';
                $mStatusColor = 'mob-status-info';
                $mEtat = trim(strtolower($etatDemande));

                switch ($mEtat) {
                    case 'non_paye': case 'paiement_en_attente': case 'en attente de paiement': case 'paiement_echoue':
                        $mProgress = 10; $mBadgeClass = 'mob-bg-warning'; $mFillClass = 'mob-fill-warning';
                        $mStatusLabel = 'En attente de paiement'; $mStatusColor = 'mob-status-warning';
                        $mStatusDesc = 'Le paiement n\'a pas encore été validé.';
                        break;
                    case 'en attente': case 'attente':
                        $mProgress = 40; $mBadgeClass = 'mob-bg-info';
                        $mStatusLabel = 'Dossier reçu'; $mStatusColor = 'mob-status-info';
                        $mStatusDesc = 'Votre demande est en cours d\'analyse.';
                        break;
                    case 'réçu': case 'recu': case 'encours':
                        $mProgress = 75; $mBadgeClass = 'mob-bg-info';
                        $mStatusLabel = 'En cours de traitement'; $mStatusColor = 'mob-status-info';
                        $mStatusDesc = 'Le document est en cours de rédaction ou de signature.';
                        break;
                    case 'terminé': case 'termine': case 'valide':
                        $mProgress = 100; $mBadgeClass = 'mob-bg-success';
                        $mStatusLabel = 'Terminé'; $mStatusColor = 'mob-status-success';
                        $mStatusDesc = 'Votre document est prêt.';
                        break;
                    case 'rejetée': case 'rejete': case 'rejetee': case 'rejeter':
                        $mProgress = 100; $mBadgeClass = 'mob-bg-danger'; $mFillClass = 'mob-fill-danger';
                        $mStatusLabel = 'Rejeté'; $mStatusColor = 'mob-status-danger';
                        $mStatusDesc = 'Votre demande a été rejetée.';
                        break;
                    default:
                        $mProgress = 50; $mBadgeClass = 'mob-bg-primary';
                        $mStatusLabel = $etatDemande; $mStatusColor = 'mob-status-info';
                        $mStatusDesc = 'Votre demande est en cours de traitement.';
                }
            @endphp

            <div class="mob-result-card">
                <div class="mob-result-header">
                    <div class="mob-result-header-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="mob-result-header-text">
                        <h3>Dossier trouvé</h3>
                        <span>Réf : {{ $reference }}</span>
                    </div>
                </div>

                <div class="mob-result-info-grid">
                    <div class="mob-result-info-box">
                        <span class="mob-info-label">État actuel</span>
                        <span class="mob-info-value {{ $mStatusColor }}">{{ $mStatusLabel }}</span>
                    </div>
                    <div class="mob-result-info-box">
                        <span class="mob-info-label">Mode de livraison</span>
                        <span class="mob-info-value">{{ $statutDemande ?? 'Retrait Mairie' }}</span>
                    </div>
                </div>

                <div class="mob-progress">
                    <div class="mob-progress-header">
                        <span>Progression</span>
                        <span class="mob-progress-badge {{ $mBadgeClass }}">{{ $mProgress }}%</span>
                    </div>
                    <div class="mob-progress-track">
                        <div class="mob-progress-fill {{ $mFillClass }}" style="width: {{ $mProgress }}%"></div>
                    </div>
                    @if ($mStatusDesc)
                        <div class="mob-progress-desc">
                            <i class="fas fa-info-circle"></i>
                            <span>{{ $mStatusDesc }}</span>
                        </div>
                    @endif
                </div>

                {{-- Mobile Stepper --}}
                <div class="mob-stepper">
                    @php
                        $ms1 = 'pending';
                        if ($mProgress >= 75 || in_array($mEtat, ['rejetée', 'rejete', 'rejetee', 'rejeter'])) { $ms1 = 'completed'; }
                        elseif ($mProgress == 40) { $ms1 = 'active'; }
                    @endphp
                    <div class="mob-step-item {{ $ms1 }}">
                        <div class="mob-step-bubble">
                            @if($ms1 === 'completed') <i class="fas fa-check"></i>
                            @else <i class="fas fa-file-alt"></i> @endif
                        </div>
                        <div class="mob-step-text">
                            <h5>Réception</h5>
                            <p>Dossier reçu et vérifié</p>
                        </div>
                    </div>

                    @php
                        $ms2 = 'pending';
                        if ($mProgress == 100 || in_array($mEtat, ['rejetée', 'rejete', 'rejetee', 'rejeter'])) { $ms2 = 'completed'; }
                        elseif ($mProgress == 75) { $ms2 = 'active'; }
                    @endphp
                    <div class="mob-step-item {{ $ms2 }}">
                        <div class="mob-step-bubble">
                            @if($ms2 === 'completed') <i class="fas fa-check"></i>
                            @else <i class="fas fa-cog"></i> @endif
                        </div>
                        <div class="mob-step-text">
                            <h5>Traitement</h5>
                            <p>Rédaction et signatures</p>
                        </div>
                    </div>

                    @php
                        $ms3 = 'pending'; $ms3T = 'Disponibilité'; $ms3D = 'Prêt pour retrait';
                        if (in_array($mEtat, ['rejetée', 'rejete', 'rejetee', 'rejeter'])) { $ms3 = 'rejected'; $ms3T = 'Rejeté'; $ms3D = 'Dossier non validé'; }
                        elseif ($mProgress == 100) { $ms3 = 'completed'; }
                    @endphp
                    <div class="mob-step-item {{ $ms3 }}">
                        <div class="mob-step-bubble">
                            @if($ms3 === 'completed') <i class="fas fa-check"></i>
                            @elseif($ms3 === 'rejected') <i class="fas fa-times"></i>
                            @else <i class="fas fa-box"></i> @endif
                        </div>
                        <div class="mob-step-text">
                            <h5>{{ $ms3T }}</h5>
                            <p>{{ $ms3D }}</p>
                        </div>
                    </div>
                </div>

                @if (in_array($mEtat, ['rejetée', 'rejete', 'rejetee', 'rejeter']))
                    <div class="mob-rejection-alert">
                        <div class="mob-rej-header">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Demande rejetée</strong>
                        </div>
                        <div class="mob-rej-body">
                            Motif du rejet :
                            <div class="mob-rej-motif">
                                {!! nl2br(e($demande->motif_de_rejet ?? 'Aucun motif spécifié. Contactez le support.')) !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Mobile Info Sections (only show when no result) --}}
        @if (!$demande)
            <div class="mob-info-sections">
                <div class="mob-info-item">
                    <div class="mob-info-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="mob-info-text">
                        <h4>Où trouver ma référence ?</h4>
                        <p>Elle vous a été envoyée par SMS et par E-mail lors de votre demande. Vous pouvez aussi la retrouver dans l'historique de vos demandes.</p>
                    </div>
                </div>
                <div class="mob-info-item">
                    <div class="mob-info-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="mob-info-text">
                        <h4>Sécurité de vos données</h4>
                        <p>Toutes vos consultations sont chiffrées et sécurisées par les services officiels.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- RESULT POPUP MODAL -->
    @if ($demande)
        @php
            $progress = 10;
            $progressBadgeClass = 'bg-primary';
            $progressFillClass = '';
            $statusLabel = $etatDemande;
            $statusDesc = '';
            $statusColorClass = 'status-info';

            $etatNormalise = trim(strtolower($etatDemande));

            switch ($etatNormalise) {
                case 'non_paye':
                case 'paiement_en_attente':
                case 'en attente de paiement':
                case 'paiement_echoue':
                    $progress = 10;
                    $progressBadgeClass = 'bg-warning';
                    $progressFillClass = 'fill-warning';
                    $statusLabel = 'En attente de paiement';
                    $statusColorClass = 'status-warning';
                    $statusDesc = 'Le paiement n\'a pas encore été validé. Le traitement commencera dès réception du règlement.';
                    break;
                case 'en attente':
                case 'attente':
                    $progress = 40;
                    $progressBadgeClass = 'bg-info';
                    $statusLabel = 'Dossier reçu';
                    $statusColorClass = 'status-info';
                    $statusDesc = 'Votre demande a été reçue et est en cours d\'analyse par nos agents.';
                    break;
                case 'réçu':
                case 'recu':
                case 'encours':
                    $progress = 75;
                    $progressBadgeClass = 'bg-primary';
                    $statusLabel = 'En cours de traitement';
                    $statusColorClass = 'status-info';
                    $statusDesc = 'Votre dossier est validé et le document est en cours de rédaction ou de signature.';
                    break;
                case 'terminé':
                case 'termine':
                case 'valide':
                    $progress = 100;
                    $progressBadgeClass = 'bg-success';
                    $statusLabel = 'Terminé';
                    $statusColorClass = 'status-success';
                    $statusDesc = 'Votre document officiel est prêt et disponible selon le mode de retrait ou livraison choisi.';
                    break;
                case 'rejetée':
                case 'rejete':
                case 'rejetee':
                case 'rejeter':
                    $progress = 100;
                    $progressBadgeClass = 'bg-danger';
                    $progressFillClass = 'fill-danger';
                    $statusLabel = 'Rejeté';
                    $statusColorClass = 'status-danger';
                    $statusDesc = 'Votre demande a été rejetée. Consultez le motif ci-dessous.';
                    break;
                default:
                    $progress = 50;
                    $progressBadgeClass = 'bg-primary';
                    $statusLabel = $etatDemande;
                    $statusColorClass = 'status-info';
                    $statusDesc = 'Votre demande est en cours de traitement.';
            }
        @endphp

        <div class="result-modal-overlay open" id="resultModal">
            <div class="result-card">
                <span class="result-close-btn" id="closeResultModal">&times;</span>

                {{-- Header --}}
                <div class="result-header">
                    <div class="result-header-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="result-header-text">
                        <h3>Dossier trouvé</h3>
                        <span>Réf : {{ $reference }}</span>
                    </div>
                </div>

                {{-- Info grid --}}
                <div class="result-info-grid">
                    <div class="result-info-box">
                        <span class="info-label">État actuel</span>
                        <span class="info-value {{ $statusColorClass }}">{{ $statusLabel }}</span>
                    </div>
                    <div class="result-info-box">
                        <span class="info-label">Mode de livraison</span>
                        <span class="info-value">{{ $statutDemande ?? 'Retrait à la Mairie' }}</span>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div class="result-progress">
                    <div class="result-progress-header">
                        <span>Progression</span>
                        <span class="result-progress-badge {{ $progressBadgeClass }}">{{ $progress }}%</span>
                    </div>
                    <div class="result-progress-track">
                        <div class="result-progress-fill {{ $progressFillClass }}" style="width: {{ $progress }}%"></div>
                    </div>
                    @if ($statusDesc)
                        <div class="result-progress-desc">
                            <i class="fas fa-info-circle"></i>
                            <span>{{ $statusDesc }}</span>
                        </div>
                    @endif
                </div>

                {{-- Stepper --}}
                <div class="result-stepper">
                    <div class="result-stepper-line">
                        @php
                            $lineWidth = '0%';
                            if (in_array($etatNormalise, ['rejetée', 'rejete', 'rejetee', 'rejeter'])) {
                                $lineWidth = '100%';
                            } elseif ($progress <= 40) {
                                $lineWidth = '0%';
                            } elseif ($progress == 75) {
                                $lineWidth = '50%';
                            } elseif ($progress == 100) {
                                $lineWidth = '100%';
                            }
                        @endphp
                        <div class="result-stepper-line-fill @if(in_array($etatNormalise, ['rejetée', 'rejete', 'rejetee', 'rejeter'])) fill-danger @endif" style="width: {{ $lineWidth }}"></div>
                    </div>
                    <div class="result-stepper-steps">
                        {{-- Step 1 --}}
                        @php
                            $s1 = 'pending';
                            if ($progress >= 75 || in_array($etatNormalise, ['rejetée', 'rejete', 'rejetee', 'rejeter'])) {
                                $s1 = 'completed';
                            } elseif ($progress == 40) {
                                $s1 = 'active';
                            }
                        @endphp
                        <div class="result-stepper-item {{ $s1 }}">
                            <div class="result-stepper-bubble">
                                @if($s1 === 'completed')
                                    <i class="fas fa-check"></i>
                                @else
                                    <i class="fas fa-file-alt"></i>
                                @endif
                            </div>
                            <div>
                                <div class="result-stepper-title">Réception</div>
                                <div class="result-stepper-desc">Dossier reçu et vérifié</div>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        @php
                            $s2 = 'pending';
                            if ($progress == 100 || in_array($etatNormalise, ['rejetée', 'rejete', 'rejetee', 'rejeter'])) {
                                $s2 = 'completed';
                            } elseif ($progress == 75) {
                                $s2 = 'active';
                            }
                        @endphp
                        <div class="result-stepper-item {{ $s2 }}">
                            <div class="result-stepper-bubble">
                                @if($s2 === 'completed')
                                    <i class="fas fa-check"></i>
                                @else
                                    <i class="fas fa-cog"></i>
                                @endif
                            </div>
                            <div>
                                <div class="result-stepper-title">Traitement</div>
                                <div class="result-stepper-desc">Rédaction et signatures</div>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        @php
                            $s3 = 'pending';
                            $s3Title = 'Disponibilité';
                            $s3Desc = 'Prêt pour retrait / livraison';
                            if (in_array($etatNormalise, ['rejetée', 'rejete', 'rejetee', 'rejeter'])) {
                                $s3 = 'rejected';
                                $s3Title = 'Rejeté';
                                $s3Desc = 'Dossier non validé';
                            } elseif ($progress == 100) {
                                $s3 = 'completed';
                            }
                        @endphp
                        <div class="result-stepper-item {{ $s3 }}">
                            <div class="result-stepper-bubble">
                                @if($s3 === 'completed')
                                    <i class="fas fa-check"></i>
                                @elseif($s3 === 'rejected')
                                    <i class="fas fa-times"></i>
                                @else
                                    <i class="fas fa-box"></i>
                                @endif
                            </div>
                            <div>
                                <div class="result-stepper-title">{{ $s3Title }}</div>
                                <div class="result-stepper-desc">{{ $s3Desc }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Rejection alert --}}
                @if (in_array($etatNormalise, ['rejetée', 'rejete', 'rejetee', 'rejeter']))
                    <div class="result-rejection-alert">
                        <div class="rejection-header">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Demande rejetée</strong>
                        </div>
                        <div class="rejection-body">
                            Votre demande n'a pas pu être validée. Veuillez consulter le motif :
                            <div class="rejection-motif">
                                {!! nl2br(e($demande->motif_de_rejet ?? 'Aucun motif de rejet spécifique fourni. Veuillez contacter le support de la mairie.')) !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- MODAL -->
    <div class="modal" id="helpModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Où trouver la référence ?</h3>
                <span class="close-modal" id="closeModal">&times;</span>
            </div>
            <div class="modal-body">
                <p>Votre numéro de référence figure en haut à droite de votre reçu de paiement imprimé ou dans l'e-mail de confirmation qui vous a été envoyé après la soumission de votre demande en ligne.</p>
                <p>Il est composé de lettres et de chiffres, par exemple : <strong>AN123456PXXXX</strong>.</p>
                <div style="text-align: center; margin-top: 20px;">
                    <i class="fas fa-file-invoice" style="font-size: 48px; color: var(--success); opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- DOWNLOAD BAR -->
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

    <!-- FOOTER GRID WRAPPER -->
    <div class="footer-grid-wrapper">
        <div class="footer-grid-bg"></div>
        
        <div class="download-separator">
            <a href="#" class="scroll-down-final" onclick="window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }); return false;">
                <i class="fas fa-chevron-down"></i>
            </a>
        </div>

        <footer class="footer">
            <div class="footer-container">
                <div class="footer-left">
                    <img src="{{ asset('assets/assets/img/plateau-mart1.png') }}" alt="Plateau Smart City" class="logo-img">
                    <div class="footer-tagline">Une administration moderne,<br>proche de vous, pour vous.</div>
                </div>

                <div class="footer-social">
                    <a href="#" aria-label="Retour en haut" onclick="window.scrollTo({ top: 0, behavior: 'smooth' }); return false;">
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

    <!-- SCRIPT -->
    <script>
        const helpLink = document.getElementById('helpLink');
        const helpModal = document.getElementById('helpModal');
        const closeModal = document.getElementById('closeModal');

        if (helpLink && helpModal && closeModal) {
            helpLink.addEventListener('click', (e) => {
                e.preventDefault();
                helpModal.classList.add('open');
            });
            closeModal.addEventListener('click', () => {
                helpModal.classList.remove('open');
            });
            helpModal.addEventListener('click', (e) => {
                if (e.target === helpModal) {
                    helpModal.classList.remove('open');
                }
            });
        }

        // Result modal logic
        const resultModal = document.getElementById('resultModal');
        const closeResultModal = document.getElementById('closeResultModal');

        if (resultModal && closeResultModal) {
            closeResultModal.addEventListener('click', () => {
                resultModal.classList.remove('open');
            });
            resultModal.addEventListener('click', (e) => {
                if (e.target === resultModal) {
                    resultModal.classList.remove('open');
                }
            });
        }

        function alertSiteInDev(e) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'En développement',
                    text: "Le site est en développement et en phase de test, d'où aucune demande n'est disponible pour le moment.",
                    confirmButtonColor: '#6777ef',
                    confirmButtonText: 'Compris',
                    backdrop: `rgba(0,0,123,0.4)`
                });
            } else {
                alert("Site en développement\n\nLe site est en développement et en phase de test, d'où aucune demande n'est disponible pour le moment.");
            }
        }

        function checkOpening(event) {
            event.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Information',
                text: 'L\'ouverture de l\'espace citoyen est prévue pour ce lundi.',
                confirmButtonColor: '#1f4083',
                confirmButtonText: 'Compris'
            });
        }
    </script>
</body>

</html>