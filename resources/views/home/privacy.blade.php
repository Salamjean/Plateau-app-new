@extends('home.layouts.tamplate')
@section('content')
<style>
    /* Styles de base */
    .privacy-container {
        max-width: 85%;
        margin: 0 auto;
        padding: 30px 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.7;
        color: #333;
        background: #f8f9fa;
    }
    
    .privacy-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 3px solid #1977cc;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(25, 119, 204, 0.1);
    }
    
    .privacy-header h1 {
        color: #1977cc;
        font-size: 32px;
        margin-bottom: 15px;
        font-weight: 700;
    }
    
    .mairie-info {
        background: linear-gradient(135deg, #1977cc 0%, #0d5aa7 100%);
        color: white;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
        font-size: 18px;
    }
    
    .update-date {
        color: #666;
        font-style: italic;
        background: #fff3cd;
        padding: 8px 15px;
        border-radius: 5px;
        display: inline-block;
        border-left: 4px solid #1977cc;
    }
    
    .privacy-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 35px;
        margin-top: 20px;
    }
    
    .privacy-column {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border-top: 5px solid #1977cc;
        transition: transform 0.3s ease;
    }
    
    .privacy-column:hover {
        transform: translateY(-5px);
    }
    
    .privacy-section {
        margin-bottom: 30px;
        padding-bottom: 25px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .privacy-section:last-child {
        border-bottom: none;
    }
    
    .privacy-section h2 {
        color: #1977cc;
        font-size: 24px;
        margin-bottom: 18px;
        padding-left: 15px;
        border-left: 5px solid #ff6b35;
        font-weight: 600;
    }
    
    .privacy-section p {
        margin-bottom: 18px;
        color: #444;
        text-align: justify;
    }
    
    .highlight-box {
        background: #e8f4ff;
        border-left: 4px solid #1977cc;
        padding: 20px;
        margin: 20px 0;
        border-radius: 0 8px 8px 0;
    }
    
    .privacy-list {
        padding-left: 25px;
        margin-bottom: 20px;
    }
    
    .privacy-list li {
        margin-bottom: 12px;
        color: #444;
        position: relative;
        padding-left: 10px;
    }
    
    .privacy-list li:before {
        content: "•";
        color: #1977cc;
        font-size: 20px;
        position: absolute;
        left: -15px;
        top: -2px;
    }
    
    .contact-box {
        background: linear-gradient(135deg, #1977cc 0%, #0d5aa7 100%);
        color: white;
        padding: 25px;
        border-radius: 10px;
        margin-top: 20px;
        text-align: center;
    }
    
    .contact-link {
        color: #fff;
        background: #ff6b35;
        padding: 10px 25px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        margin-top: 10px;
        transition: background 0.3s;
    }
    
    .contact-link:hover {
        background: #ff5722;
        text-decoration: none;
        color: white;
        transform: scale(1.05);
    }
    
    .important-note {
        background: #fff8e1;
        border: 2px solid #ffc107;
        padding: 20px;
        border-radius: 8px;
        margin: 25px 0;
        font-weight: 500;
    }
    
    .data-category {
        background: #f1f8ff;
        border: 1px solid #1977cc;
        border-radius: 6px;
        padding: 15px;
        margin: 15px 0;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .privacy-content {
            grid-template-columns: 1fr;
            gap: 25px;
        }
        
        .privacy-container {
            max-width: 95%;
            padding: 20px 15px;
        }
        
        .privacy-header h1 {
            font-size: 28px;
        }
    }
    
    @media (max-width: 576px) {
        .privacy-header {
            padding: 20px 15px;
        }
        
        .privacy-column {
            padding: 20px 15px;
        }
        
        .privacy-section h2 {
            font-size: 22px;
        }
    }
</style>

<div class="privacy-container">
    <div class="privacy-header">
        <h1>📄 Politique de Confidentialité</h1>
        <div class="mairie-info">
            Plateforme de Demande d'Extraits d'État Civil - Mairie du Plateau
        </div>
        <p class="update-date">Dernière mise à jour : {{ date('d/m/Y') }}</p>
    </div>
    
    <div class="privacy-content">
        <!-- Première colonne -->
        <div class="privacy-column">
            <section class="privacy-section">
                <h2>🏛️ 1. Présentation</h2>
                <p>
                    La Mairie du Plateau ("nous", "notre", "nos") s'engage à protéger la vie privée des citoyens utilisant notre plateforme numérique de demande d'extraits d'état civil. Cette politique décrit comment nous collectons, traitons et protégeons vos données personnelles.
                </p>
                <div class="highlight-box">
                    <strong>⚠️ Important :</strong> Cette plateforme est un service public numérique de la Mairie du Plateau. Vos données sont traitées dans le strict respect des lois ivoiriennes sur la protection des données et des procédures administratives.
                </div>
            </section>
            
            <section class="privacy-section">
                <h2>📋 2. Données Collectées</h2>
                <p>Pour traiter votre demande d'extrait, nous collectons :</p>
                
                <div class="data-category">
                    <strong>🔹 Informations d'identité :</strong>
                    <ul class="privacy-list">
                        <li>Nom, prénoms, date et lieu de naissance</li>
                        <li>Noms des parents</li>
                        <li>Numéro d'acte d'état civil (si disponible)</li>
                    </ul>
                </div>
                
                <div class="data-category">
                    <strong>🔹 Coordonnées :</strong>
                    <ul class="privacy-list">
                        <li>Adresse email et numéro de téléphone</li>
                        <li>Adresse postale et commune de résidence</li>
                    </ul>
                </div>
                
                <div class="data-category">
                    <strong>🔹 Documents justificatifs :</strong>
                    <ul class="privacy-list">
                        <li>Copie de carte nationale d'identité</li>
                        <li>Autres documents d'identité requis</li>
                        <li>Pièces justificatives selon le type d'extrait</li>
                    </ul>
                </div>
            </section>
            
            <section class="privacy-section">
                <h2>🎯 3. Finalités du Traitement</h2>
                <p>Vos données sont utilisées exclusivement pour :</p>
                <ul class="privacy-list">
                    <li>Traiter et instruire votre demande d'extrait d'état civil</li>
                    <li>Vérifier votre identité et l'authenticité des documents</li>
                    <li>Établir et délivrer les actes administratifs requis</li>
                    <li>Vous informer du statut de votre demande</li>
                    <li>Gérer les services de livraison (le cas échéant)</li>
                    <li>Améliorer continuellement notre service public numérique</li>
                </ul>
            </section>
            
            <section class="important-note">
                <strong>🔒 Base Légale :</strong> Le traitement de vos données est nécessaire à l'exécution d'une mission d'intérêt public et à l'obligation légale de la Mairie du Plateau de délivrer des actes d'état civil.
            </section>
        </div>
        
        <!-- Deuxième colonne -->
        <div class="privacy-column">
            <section class="privacy-section">
                <h2>🤝 4. Partage des Données</h2>
                <p>Vos données peuvent être partagées avec :</p>
                <ul class="privacy-list">
                    <li><strong>Services internes de la Mairie :</strong> Service d'état civil, archives</li>
                    <li><strong>Prestataires techniques :</strong> Hébergeur agréé, service de paiement sécurisé</li>
                    <li><strong>Services de livraison :</strong> Pour la distribution des documents</li>
                    <li><strong>Autorités judiciaires :</strong> Sur réquisition légale uniquement</li>
                </ul>
                <p class="highlight-box">
                    <strong>🚫 Nous ne vendons ni ne commercialisons vos données personnelles.</strong>
                </p>
            </section>
            
            <section class="privacy-section">
                <h2>🛡️ 5. Sécurité des Données</h2>
                <p>Nous mettons en œuvre des mesures de sécurité rigoureuses :</p>
                <ul class="privacy-list">
                    <li>Chiffrement SSL pour toutes les transmissions</li>
                    <li>Stockage sécurisé sur serveurs en Côte d'Ivoire</li>
                    <li>Accès restreint et authentification forte</li>
                    <li>Audits de sécurité réguliers</li>
                    <li>Sauvegardes chiffrées quotidiennes</li>
                </ul>
            </section>
            
            <section class="privacy-section">
                <h2>⚖️ 6. Vos Droits</h2>
                <p>Conformément à la réglementation, vous pouvez :</p>
                <ul class="privacy-list">
                    <li>Accéder à vos données personnelles</li>
                    <li>Demander leur rectification en cas d'erreur</li>
                    <li>Vous opposer à certains traitements</li>
                    <li>Demander la limitation du traitement</li>
                    <li>Retirer votre consentement (pour les communications)</li>
                </ul>
                <p><strong>Durée de conservation :</strong> Vos données sont conservées pendant la durée légale requise par les archives administratives, puis anonymisées.</p>
            </section>
            
            <section class="privacy-section">
                <h2>🌐 7. Cookies & Traçage</h2>
                <p>Nous utilisons des cookies essentiels pour :</p>
                <ul class="privacy-list">
                    <li>Maintenir votre session sécurisée</li>
                    <li>Mémoriser vos préférences linguistiques</li>
                    <li>Améliorer la performance du site</li>
                </ul>
                <p>Vous pouvez gérer les cookies via les paramètres de votre navigateur.</p>
            </section>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 40px; padding: 20px; background: white; border-radius: 10px; border-top: 3px solid #1977cc;">
        <p style="color: #666; font-size: 14px;">
            <strong>Mairie du Plateau</strong> - Service d'État Civil Numérique<br>
            Cette politique est régie par le droit ivoirien. Toute modification sera publiée sur cette page.
        </p>
    </div>
</div>
@endsection