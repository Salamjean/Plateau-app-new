@extends('poste.layouts.template')

@section('content')
<style>
    :root {
        --primary: #1f4083;
        --secondary: #ea8c51;
        --success: #10b981;
        --text-main: #2d3748;
        --text-muted: #718096;
    }

    .center-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 200px);
        padding: 2rem;
    }

    .register-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        width: 100%;
        max-width: 600px;
        overflow: hidden;
        border: 1px solid #edf2f7;
    }

    .card-header {
        background: var(--primary);
        padding: 2rem;
        text-align: center;
        color: white;
        position: relative;
    }

    .card-header i {
        font-size: 3rem;
        margin-bottom: 1rem;
        background: rgba(255,255,255,0.1);
        padding: 15px;
        border-radius: 50%;
    }

    .card-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.5rem;
    }

    .card-body {
        padding: 3rem;
    }

    .instruction-text {
        text-align: center;
        color: var(--text-muted);
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }

    .input-group {
        position: relative;
        margin-bottom: 2rem;
    }

    .input-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary);
    }

    .form-control {
        width: 100%;
        padding: 15px 15px 15px 50px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.2s;
        text-transform: uppercase;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(31, 64, 131, 0.1);
        outline: none;
    }

    .btn-register {
        width: 100%;
        padding: 15px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-register:hover {
        background: #163266;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(31, 64, 131, 0.3);
    }

    .accent-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: var(--secondary);
    }

    @media (max-width: 480px) {
        .card-body {
            padding: 1.5rem;
        }
    }
</style>

<div class="center-container">
    <div class="register-card">
        <div class="card-header">
            <i class="material-icons">qr_code_scanner</i>
            <h2>Réception de Colis</h2>
            <p style="margin-top: 5px; opacity: 0.9;">Enregistrez de nouveaux colis arrivant au Courrier</p>
            <div class="accent-bar"></div>
        </div>
        
        <div class="card-body">
            @if(session('error'))
                <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
                    <i class="material-icons">error_outline</i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div style="background: #ecfdf5; color: #047857; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
                    <i class="material-icons">check_circle</i>
                    {{ session('success') }}
                </div>
            @endif

            <p class="instruction-text">
                Veuillez scanner le QR Code ou saisir manuellement la référence unique du colis figurant sur le récépissé.
            </p>

            <form action="{{ route('poste.attribuer-demande') }}" method="POST">
                @csrf
                <div class="input-group">
                    <i class="material-icons">label</i>
                    <input type="text" name="reference" class="form-control" placeholder="EX: NAIS-2023-XXXX" required autofocus>
                </div>

                <button type="submit" class="btn-register">
                    <i class="material-icons">file_download</i>
                    Enregistrer en courrier
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
