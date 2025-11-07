<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Statut du Paiement</title>
  <style>
    :root {
      --success: #28a745;
      --danger: #dc3545;
      --primary-color: #007bff;
      --light-gray: #f4f7f6;
      --text-color: #333;
      --text-muted: #666;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      background-color: var(--light-gray);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      text-align: center;
      padding: 1rem;
      color: var(--text-color);
    }
    .container {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      padding: 2.5rem 2rem;
      max-width: 400px;
      width: 100%;
    }
    .icon {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1rem;
    }
    .icon svg { width: 32px; height: 32px; stroke-width: 3; }
    .icon-success { background: #eaf6ec; color: var(--success); }
    .icon-danger { background: #fbebed; color: var(--danger); }
    .spinner {
      width: 60px;
      height: 60px;
      border: 5px solid var(--light-gray);
      border-top-color: var(--primary-color);
      border-radius: 50%;
      display: inline-block;
      animation: spin 1s linear infinite;
      margin-bottom: 1rem;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    #title { font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem; }
    #status { font-size: 1rem; color: var(--text-muted); margin-bottom: 1.5rem; }
    .btn {
      display: inline-block;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1rem;
      margin: 0.5rem;
      transition: background-color 0.2s;
      border: none;
      cursor: pointer;
      width: 100%;
    }
    .btn-primary { background-color: var(--primary-color); color: #fff; }
  _ .btn-primary:hover { background-color: #0056b3; }_
    .btn-store { background-color: #e9ecef; color: #333; }
    .btn-store:hover { background-color: #d1d5da; }
    #fallback-options { display: none; }
    .store-links { border-top: 1px solid var(--light-gray); margin-top: 1.5rem; padding-top: 1rem; }
    .small-text { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem; }
  </style>
</head>
<body>

  <div class="container">
    
    <div id="icon-loading" class="spinner"></div>
    <div id="icon-success" class="icon icon-success" style="display: none;">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
    </div>
    <div id="icon-danger" class="icon icon-danger" style="display: none;">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
    </div>

    <h1 id="title">Vérification du paiement...</h1>
    <p id="status">Veuillez patienter, nous confirmons le statut de votre transaction.</p>

    <div id="fallback-options">
      <a id="openAppBtn" class="btn btn-primary" href="#">Ouvrir l'application</a>
      <div class="store-links">
        <p class="small-text">Si l'application ne s'ouvre pas, téléchargez-la :</p>
        <p id="storeButtons"></p>
      </div>
    </div>
  </div>

<script>
(function() {
  
  // 1. Récupérer le TransactionID passé par le contrôleur PHP
  const transactionId = @json($transactionId ?? null);

  // DOM Elements
  const titleEl = document.getElementById('title');
  const statusEl = document.getElementById('status');
  const loadingIcon = document.getElementById('icon-loading');
  const successIcon = document.getElementById('icon-success');
  const dangerIcon = document.getElementById('icon-danger');
  const fallbackOptions = document.getElementById('fallback-options');
  const openBtn = document.getElementById('openAppBtn');
  const storeButtons = document.getElementById('storeButtons');

  // 2. Gérer le cas d'erreur initial
  if (!transactionId) {
    titleEl.textContent = 'Erreur';
    statusEl.textContent = 'Transaction invalide ou identifiant manquant.';
    loadingIcon.style.display = 'none';
    dangerIcon.style.display = 'inline-flex';
    return;
  }

  // 3. Configuration de l'application (À MODIFIER)
  const androidPackage = 'com.votre.package.android'; 
  const iosAppId = 'id1234567890'; 
  const playStoreUrl = `https://play.google.com/store/apps/details?id=${androidPackage}`;
  const appStoreUrl = `https://apps.apple.com/app/${iosAppId}`;

  let finalDeepLink = ''; // Sera défini après confirmation du statut

  // 4. Fonctions pour mettre à jour l'interface
  function showSuccess() {
    titleEl.textContent = 'Paiement terminé';
    titleEl.style.color = 'var(--success)';
    statusEl.textContent = "Nous allons vous rediriger vers l'application...";
    loadingIcon.style.display = 'none';
    successIcon.style.display = 'inline-flex';
    
    finalDeepLink = `plateauapps://payment?cinetpay=true&transactionId=${encodeURIComponent(transactionId)}`;
    tryOpenApp(); // Tenter l'ouverture auto
    setupFallback(true); // Préparer les boutons au cas où
  }

  function showFailure() {
    titleEl.textContent = 'Paiement échoué';
    titleEl.style.color = 'var(--danger)';
    statusEl.textContent = "Le paiement a été annulé ou a échoué. Vous allez être redirigé...";
    loadingIcon.style.display = 'none';
    dangerIcon.style.display = 'inline-flex';
    
    finalDeepLink = `plateauapps://payment?cinetpay=false&transactionId=${encodeURIComponent(transactionId)}`;
    tryOpenApp(); // Tenter l'ouverture auto
    setupFallback(false); // Préparer les boutons au cas où
  }
  
  function showError(message) {
      titleEl.textContent = 'Erreur de vérification';
      statusEl.textContent = message || "Impossible de vérifier le statut de votre paiement.";
      loadingIcon.style.display = 'none';
      dangerIcon.style.display = 'inline-flex';
  }

  // 5. Logique de Polling (interrogation de l'API)
  let pollCount = 0;
  const maxPolls = 15; // (15 * 2 = 30 secondes max)

  function pollStatus() {
    pollCount++;
    if (pollCount > maxPolls) {
      showError("Le statut du paiement est toujours en attente. Veuillez contacter le support.");
      return;
    }

    // *** DÉBUT DE LA CORRECTION ***
    // Déterminer quelle API appeler en fonction du préfixe de la transaction
    
    let apiPath = '';
    if (transactionId.startsWith('AD')) {
        apiPath = `/api/deces/payment-status/${transactionId}`;
    } else if (transactionId.startsWith('AM')) {
        apiPath = `/api/mariage/payment-status/${transactionId}`;
    } else if (transactionId.startsWith('AN')) {
        // ✅ LIGNE ACTIVÉE POUR NAISSANCE
        apiPath = `/api/naissance/payment-status/${transactionId}`; 
    } else {
        showError("Type de transaction inconnu. Impossible de vérifier le statut.");
        return;
    }

    // Appelle l'API dynamique
    fetch(apiPath)
    // *** FIN DE LA CORRECTION ***
    
      .then(response => {
        if (!response.ok) {
          throw new Error('Transaction non trouvée.');
        }
        return response.json();
      })
      .then(data => {
        const status = data.status;
        
        // Les statuts sont les mêmes, la logique ici n'a pas besoin de changer
        if (status === 'en attente de livraison') {
          // SUCCÈS
          showSuccess();
        } else if (status === 'paiement échoué' || status === 'REFUSED') {
          // ÉCHEC
          showFailure();
        } else if (status === 'en attente de paiement' || status === 'PENDING' || status === 'AWAITING') {
          // TOUJOURS EN ATTENTE, on ré-essaie
          setTimeout(pollStatus, 2000); // Réessayer dans 2 secondes
        } else {
          // Statut inconnu ou 'not_found'
          showError('Statut de transaction inconnu.');
        }
      })
      .catch(err => {
        showError(err.message);
      });
  }

  // 6. Logique de redirection et fallback (INCHANGÉE)
  const ua = navigator.userAgent || '';
  const isAndroid = /android/i.test(ua);
  const isIOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream;

  function tryOpenApp() {
    if (!finalDeepLink) return;

    const androidIntent = `intent://payment?cinetpay=${finalDeepLink.includes('true') ? 'true' : 'false'}&transactionId=${encodeURIComponent(transactionId)}#Intent;scheme=plateauapps;package=${androidPackage};end`;

    if (isAndroid) {
      window.location = androidIntent;
    } else {
      window.location = finalDeepLink;
    }
  }

  // 7. Configurer les boutons de fallback (INCHANGÉE)
  function setupFallback(isSuccess) {
    let pageHidden = false;
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) pageHidden = true;
    });

    openBtn.href = finalDeepLink;
    openBtn.addEventListener('click', function(e) {
      e.preventDefault(); 
      statusEl.textContent = "Tentative d'ouverture de l'application...";
      tryOpenApp();
      setTimeout(() => { window.close(); }, 500);
    });
    
    let btns = `<a class="btn btn-store" href="${playStoreUrl}" target="_blank">Google Play Store</a>`;
    if (isIOS) {
      btns = `<a class="btn btn-store" href="${appStoreUrl}" target="_blank">App Store</a>`;
    } else if (!isAndroid) {
      btns += ` <a class="btn btn-store" href="${appStoreUrl}" target="_blank">App Store</a>`;
    }
    storeButtons.innerHTML = btns;

    setTimeout(() => {
      if (!pageHidden) {
        statusEl.textContent = "L'application ne s'est pas ouverte automatiquement.";
        fallbackOptions.style.display = 'block';
      }
    }, 1500);
  }

  // 8. DÉMARRER LE PROCESSUS
  pollStatus();

})();
</script>

</body>
</html>