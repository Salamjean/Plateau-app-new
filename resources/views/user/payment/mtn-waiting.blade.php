@extends('user.layouts.template')

@section('content')
<style>
    .waiting-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        text-align: center;
        background: #f8f9fa;
        padding: 40px 20px;
        border-radius: 12px;
        margin: 40px auto;
        max-width: 600px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    .spinner {
        width: 80px;
        height: 80px;
        border: 8px solid #f3f3f3;
        border-top: 8px solid #fcb711; /* MTN Yellow */
        border-radius: 50%;
        animation: spin 1.5s linear infinite;
        margin-bottom: 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .waiting-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
    }

    .waiting-text {
        font-size: 1.1rem;
        color: #666;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    .mtn-highlight {
        color: #fcb711;
        font-weight: bold;
    }
</style>

<div class="waiting-container">
    <div class="spinner"></div>
    <h2 class="waiting-title">Paiement <span class="mtn-highlight">MTN MoMo</span> en cours...</h2>
    <p class="waiting-text">
        Veuillez consulter votre téléphone et entrer votre code secret MTN Mobile Money pour confirmer le paiement.
        <br><br>
        Ne fermez pas cette page, elle s'actualisera automatiquement une fois le paiement validé.
    </p>

    <p style="font-size: 0.9rem; color: #888;">Transaction : {{ $reference }}</p>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reference = '{{ $reference }}';
        const type = '{{ $type }}';
        const mtnRef = '{{ $mtnRef }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const checkStatus = () => {
            fetch('{{ route("user.payment.mtn.check") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    reference: reference,
                    type: type,
                    mtn_ref: mtnRef
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'SUCCESSFUL' || data.status === 'FAILED') {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    // Si toujours PENDING ou erreur, on réessaie dans 5 secondes
                    setTimeout(checkStatus, 5000);
                }
            })
            .catch(error => {
                console.error('Erreur de vérification:', error);
                setTimeout(checkStatus, 5000);
            });
        };

        // Démarrer la vérification
        setTimeout(checkStatus, 5000);
    });
</script>
@endsection
