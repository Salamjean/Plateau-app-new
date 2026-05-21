<?php

$key = 'VAU34I';
$secret = '8A03R3';
$credentialId = 'gtvB04rzE_wkvb4S2';
$baseUrl = 'https://test.tresormoney.ci';
$tel = '0767664010';

// Token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/Authentification_V1");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('Key' => $key, 'Secret' => $secret)));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$result = curl_exec($ch);
curl_close($ch);
$token = json_decode($result, true)['Token'] ?? null;

// InitierPaiement
$produit = array(
    'LibelleProduit' => 'TIMBRE ETAT CIVIL',
    'Montant' => '500',
    'IdProduit' => '0',
    'nEstUnServicePrive' => '0',
    'TypeProduit' => '0',
    'Reference_code_Produit' => '',
    'Reference_Code_Produit' => '',
    'Quantite' => '1'
);

$credentiel = array(
    'Credentiel' => $credentialId,
    'Produits' => array($produit)
);

$payload = array(
    'Token' => $token,
    'Nom_usager' => 'Mairie',
    'Prenom_usager' => 'Plateau',
    'Email' => 'contact@plateau.ci',
    'Telephone' => $tel,
    'Additif' => 'Timbre Etat Civil',
    'code_paiement' => 'TP-TEST-001',
    'Url_Retour' => 'https://localhost',
    'Url_Callback' => 'https://localhost',
    'TypeOperation' => '1',
    'TCredentiel' => array($credentiel)
);

echo "=== PAYLOAD ENVOYE ===\n";
echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/InitierPaiement_V1");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$result = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

$decoded = json_decode($result, true);

echo "=== REPONSE TRESORPAY ===\n";
echo "HTTP Status: " . $info['http_code'] . "\n";
echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
