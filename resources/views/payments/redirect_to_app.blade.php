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
    .btn-primary:hover { background-color: #0056b3; }
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
  <div id="payment-data" 
  data-transaction-id="{{ $transactionId ?? '' }}"
  style="display: none;">
</div>
<script>
  (function() {
    
    // SOLUTION CORRIGÉE : Récupération sécurisée du transactionId
    const paymentDataEl = document.getElementById('payment-data');
    const transactionId = paymentDataEl ? paymentDataEl.dataset.transactionId : null;
  
    console.log('Transaction ID récupéré:', transactionId); // Pour debug
  
    // DOM Elements
    const titleEl = document.getElementById('title');
    const statusEl = document.getElementById('status');
    const loadingIcon = document.getElementById('icon-loading');
    const successIcon = document.getElementById('icon-success');
    const dangerIcon = document.getElementById('icon-danger');
    const fallbackOptions = document.getElementById('fallback-options');
    const openBtn = document.getElementById('openAppBtn');
    const storeButtons = document.getElementById('storeButtons');
  
    // 2. Gérer le cas d'erreur initial - CORRIGÉ
    if (!transactionId || transactionId === '') {
      console.error('Transaction ID manquant ou vide');
      titleEl.textContent = 'Erreur';
      statusEl.textContent = 'Transaction invalide ou identifiant manquant.';
      loadingIcon.style.display = 'none';
      dangerIcon.style.display = 'inline-flex';
      return;
    }
  
    // Le reste de votre code JavaScript reste inchangé...
    // 3. Configuration de l'application
    const androidPackage = 'com.votre.package.android'; 
    const iosAppId = 'id1234567890'; 
    const playStoreUrl = `https://play.google.com/store/apps/details?id=${androidPackage}`;
    const appStoreUrl = `https://apps.apple.com/app/${iosAppId}`;
  
    let finalDeepLink = '';
  
    // 4. Fonctions pour mettre à jour l'interface
    function showSuccess() {
      titleEl.textContent = 'Paiement terminé';
      titleEl.style.color = 'var(--success)';
      statusEl.textContent = "Nous allons vous rediriger vers l'application...";
      loadingIcon.style.display = 'none';
      successIcon.style.display = 'inline-flex';
      
      finalDeepLink = `plateauapps://app/payment-result?status=success&transactionId=${encodeURIComponent(transactionId)}`;
      tryOpenApp();
      setupFallback(true);
    }
  
    function showFailure() {
      titleEl.textContent = 'paiement échoué';
      titleEl.style.color = 'var(--danger)';
      statusEl.textContent = "Le paiement a été annulé ou a échoué. Vous allez être redirigé...";
      loadingIcon.style.display = 'none';
      dangerIcon.style.display = 'inline-flex';
      
      finalDeepLink = `plateauapps://app/payment-result?status=cancel&transactionId=${encodeURIComponent(transactionId)}`;
      tryOpenApp();
      setupFallback(false);
    }
    
    function showError(message) {
        titleEl.textContent = 'Erreur de vérification';
        statusEl.textContent = message || "Impossible de vérifier le statut de votre paiement.";
        loadingIcon.style.display = 'none';
        dangerIcon.style.display = 'inline-flex';
    }
  
    // 5. Logique de Polling
    let pollCount = 0;
    const maxPolls = 15;
  
    function pollStatus() {
  pollCount++;
  if (pollCount > maxPolls) {
    showError("Le statut du paiement est toujours en attente. Veuillez contacter le support.");
    return;
  }

  let apiPath = '';
  if (transactionId.startsWith('AD')) {
      apiPath = `/api/deces/payment-status/${transactionId}`;
  } else if (transactionId.startsWith('AM')) {
      apiPath = `/api/mariage/payment-status/${transactionId}`;
  } else if (transactionId.startsWith('AN')) {
      apiPath = `/api/naissance/payment-status/${transactionId}`; 
  } else {
      showError("Type de transaction inconnu. Impossible de vérifier le statut.");
      return;
  }

  console.log('Appel API:', apiPath);

  fetch(apiPath)
    .then(response => {
      console.log('Response status:', response.status);
      console.log('Response headers:', response.headers);
      
      if (!response.ok) {
        throw new Error(`Erreur HTTP: ${response.status}`);
      }
      
      // Vérifier le content-type avant de parser
      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        throw new Error('Réponse non-JSON reçue du serveur');
      }
      
      return response.text().then(text => {
        console.log('Response text:', text);
        try {
          return JSON.parse(text);
        } catch (parseError) {
          console.error('Erreur parsing JSON:', parseError);
          throw new Error('Données JSON invalides');
        }
      });
    })
    .then(data => {
      console.log('Data reçu:', data);
      const status = data.status;
      
      if (status === 'en attente') {
        showSuccess();
      } else if (status === 'paiement_echoue' || status === 'REFUSED') {
        showFailure();
      } else if (status === 'en attente de paiement' || status === 'PENDING' || status === 'AWAITING') {
        setTimeout(pollStatus, 2000);
      } else {
        showError('Statut de transaction inconnu: ' + status);
      }
    })
    .catch(err => {
      console.error('Erreur fetch complète:', err);
      if (pollCount <= maxPolls) {
        // Réessayer après un délai en cas d'erreur réseau
        setTimeout(pollStatus, 2000);
      } else {
        showError('Impossible de vérifier le statut: ' + err.message);
      }
    });
}
  
    // 6. Logique de redirection et fallback
    const ua = navigator.userAgent || '';
    const isAndroid = /android/i.test(ua);
    const isIOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream;
  
    function tryOpenApp() {
      if (!finalDeepLink) return;
  
      window.location = finalDeepLink;
    }
  
    // 7. Configurer les boutons de fallback
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