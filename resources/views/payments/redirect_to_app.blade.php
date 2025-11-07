<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Retour vers l'application</title>
  <style>
    body{font-family:Inter,Arial,Helvetica,sans-serif; padding:24px; text-align:center;}
    .btn{display:inline-block;padding:12px 18px;border-radius:10px;text-decoration:none;background:#ff4d4f;color:#fff;margin:10px 0;}
    .muted{color:#666;font-size:0.9rem;margin-top:8px;}
    .small{font-size:0.85rem;color:#444}
  </style>
</head>
<body>
  <h1>Paiement terminé</h1>
  <p id="status" class="muted">Nous allons vous rediriger vers l'application...</p>

  <p id="manualOpen" style="display:none">
    <a id="openAppBtn" class="btn" href="#">Ouvrir l'application</a>
  </p>

  <div id="storeLinks" style="margin-top:18px; display:none;">
    <p class="small">Si l'application ne s'ouvre pas, téléchargez-la depuis :</p>
    <p id="storeButtons"></p>
  </div>

<script>
(function(){
  // Récupère les paramètres depuis l'URL
  const params = new URLSearchParams(window.location.search);
  const transactionId = params.get('transactionId') || params.get('transaction_id') || '';
  const cancel = params.get('cancel') || '0';
  const cinetpay = params.get('cinetpay') || 'true';

  // Utiliser cinetpay parameter pour déterminer le statut
  const paymentStatus = cinetpay === 'true' && cancel === '0';

  if(!transactionId){
    document.getElementById('status').textContent = 'Transaction invalide — aucun identifiant trouvé.';
    return;
  }
    // Construire le deep link avec le bon statut
    const deepLink = `plateauapps://payment?cinetpay=${paymentStatus ? 'true' : 'false'}&transactionId=${encodeURIComponent(transactionId)}`;

  // Optional: Android intent (plus fiable sur Chrome Android)
  // Remplace com.ton.app par ton package Android réel si tu veux l'utiliser
  const androidIntent = `intent://payment?cinetpay=${cancel === '1' ? 'false' : 'true'}&transactionId=${encodeURIComponent(transactionId)}#Intent;scheme=plateauapps;package=com.ton.app;end`;

  // Définitions store links (remplace par tes urls réelles)
  const playStore = 'https://play.google.com/store/apps/details?id=com.ton.app';
  const appStore = 'https://apps.apple.com/app/idTON_APP_ID';

  // Mettre à jour bouton "Ouvrir l'application" (pour clic manuel)
  const openBtn = document.getElementById('openAppBtn');
  openBtn.href = deepLink;
  openBtn.addEventListener('click', function(){ document.getElementById('status').textContent = 'Ouverture de l\\'application...'; });

  // Essayer d'ouvrir automatiquement
  const ua = navigator.userAgent || '';
  const isAndroid = /android/i.test(ua);
  const isIOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream;

  // Tenter l'ouverture adaptée à la plateforme
  function tryOpen() {
    if (isAndroid) {
      // Chrome Android supporte intent:// (plus fiable que custom scheme)
      window.location = androidIntent;
    } else {
      // iOS et desktop -> custom scheme
      window.location = deepLink;
    }
  }

  // Méthode de détection : on mesure le temps passé / écoute visibilitychange
  const start = Date.now();
  let pageHidden = false;

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) pageHidden = true;
  });

  // Lancer la tentative après un court délai
  setTimeout(() => {
    tryOpen();
  }, 200);

  // Après délai, si la page n'a pas perdu le focus (app non ouverte), proposer store / bouton
  setTimeout(() => {
    const elapsed = Date.now() - start;
    // Si la page est toujours visible, l'app n'a probablement pas été ouverte
    if (!pageHidden && elapsed > 500) {
      document.getElementById('status').textContent = "L'application ne s'est pas ouverte automatiquement.";
      document.getElementById('manualOpen').style.display = 'block';
      document.getElementById('storeLinks').style.display = 'block';

      // Ajouter boutons store
      const btns = [];
      if (isAndroid) {
        btns.push(`<a class="btn" href="${playStore}" target="_blank">Télécharger sur Google Play</a>`);
      } else if (isIOS) {
        btns.push(`<a class="btn" href="${appStore}" target="_blank">Télécharger sur l'App Store</a>`);
      } else {
        // Desktop
        btns.push(`<a class="btn" href="${playStore}" target="_blank">Télécharger l'application</a>`);
      }
      document.getElementById('storeButtons').innerHTML = btns.join(' ');
    } else {
      // pageHidden true => l'app a probablement été ouverte ; on peut afficher message court
      document.getElementById('status').textContent = 'Si vous êtes redirigé vers l’application, le processus est terminé.';
    }
  }, 500);

})();
</script>
</body>
</html>
