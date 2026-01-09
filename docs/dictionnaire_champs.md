# Dictionnaire des Champs - Plateau App

## Description
Ce document présente la correspondance entre les noms techniques des champs dans la base de données et leurs libellés lisibles en français.

---

## Table NAISSANCE (Demandes d'extrait de naissance)

| Champ Technique | Libellé Français | Description |
|-----------------|------------------|-------------|
| `id` | Identifiant | Identifiant unique de la demande |
| `type` | Type d'extrait | Type d'extrait demandé (copie intégrale, extrait) |
| `pour` | Bénéficiaire | Personne pour qui l'extrait est demandé |
| `name` | Nom | Nom de famille de la personne concernée |
| `prenom` | Prénom | Prénom de la personne concernée |
| `number` | Numéro de registre | Numéro du registre d'état civil |
| `DateR` | Date de registre | Date d'enregistrement dans le registre |
| `CNI` | Carte Nationale d'Identité | Document CNI téléchargé (chemin fichier) |
| `reference` | Référence | Référence unique de la demande |
| `commune` | Commune | Commune d'enregistrement de la naissance |
| `quantite` | Quantité | Nombre d'exemplaires demandés |
| `etat` | État de traitement | Statut de la demande (en attente, réçu, terminé, rejetée) |
| `motif_de_rejet` | Motif de rejet | Explication du rejet de la demande |
| `champs_a_modifier` | Champs à corriger | Liste des champs nécessitant une modification |
| `peut_modifier` | Peut modifier | Indicateur si l'utilisateur peut modifier la demande |
| `statut_livraison` | Statut de livraison | État de la livraison (en attente, en cours, livré) |
| `livraison_code` | Code de livraison | Code unique pour la livraison |
| `qr_code_path` | Chemin QR Code | Chemin vers l'image du QR code |
| `choix_option` | Option de récupération | Mode de récupération (livraison, retrait sur place) |
| `montant_timbre` | Montant timbre | Coût du timbre fiscal |
| `montant_livraison` | Frais de livraison | Coût de la livraison |
| `nom_destinataire` | Nom du destinataire | Nom pour la livraison |
| `prenom_destinataire` | Prénom du destinataire | Prénom pour la livraison |
| `email_destinataire` | Email du destinataire | Adresse email pour la livraison |
| `contact_destinataire` | Téléphone du destinataire | Numéro de téléphone pour la livraison |
| `adresse_livraison` | Adresse de livraison | Adresse complète de livraison |
| `code_postal` | Code postal | Code postal de livraison |
| `ville` | Ville | Ville de livraison |
| `commune_livraison` | Commune de livraison | Commune pour la livraison |
| `quartier` | Quartier | Quartier de livraison |
| `user_id` | Utilisateur | Identifiant du demandeur |
| `agent_id` | Agent traitant | Identifiant de l'agent qui traite la demande |
| `livreur_id` | Livreur | Identifiant du livreur assigné |
| `agence_id` | Agence | Identifiant de l'agence |
| `livraison_id` | Point de retrait | Identifiant du point de retrait (poste) |
| `created_at` | Date de création | Date de soumission de la demande |
| `updated_at` | Dernière modification | Date de dernière mise à jour |

---

## Table MARIAGE (Demandes d'extrait de mariage)

| Champ Technique | Libellé Français | Description |
|-----------------|------------------|-------------|
| `id` | Identifiant | Identifiant unique de la demande |
| `nomEpoux` | Nom de l'époux | Nom de famille de l'époux |
| `prenomEpoux` | Prénom de l'époux | Prénom de l'époux |
| `dateNaissanceEpoux` | Date de naissance de l'époux | Date de naissance de l'époux |
| `lieuNaissanceEpoux` | Lieu de naissance de l'époux | Lieu de naissance de l'époux |
| `pieceIdentite` | Pièce d'identité | Document d'identité téléchargé (chemin fichier) |
| `extraitMariage` | Extrait de mariage | Document d'extrait de mariage (chemin fichier) |
| `reference` | Référence | Référence unique de la demande |
| `commune` | Commune | Commune d'enregistrement du mariage |
| `quantite` | Quantité | Nombre d'exemplaires demandés |
| `etat` | État de traitement | Statut de la demande (en attente, réçu, terminé, rejetée) |
| `motif_de_rejet` | Motif de rejet | Explication du rejet de la demande |
| `champs_a_modifier` | Champs à corriger | Liste des champs nécessitant une modification |
| `peut_modifier` | Peut modifier | Indicateur si l'utilisateur peut modifier la demande |
| `statut_livraison` | Statut de livraison | État de la livraison (en attente, en cours, livré) |
| `livraison_code` | Code de livraison | Code unique pour la livraison |
| `qr_code_path` | Chemin QR Code | Chemin vers l'image du QR code |
| `choix_option` | Option de récupération | Mode de récupération (livraison, retrait sur place) |
| `montant_timbre` | Montant timbre | Coût du timbre fiscal |
| `montant_livraison` | Frais de livraison | Coût de la livraison |
| `nom_destinataire` | Nom du destinataire | Nom pour la livraison |
| `prenom_destinataire` | Prénom du destinataire | Prénom pour la livraison |
| `email_destinataire` | Email du destinataire | Adresse email pour la livraison |
| `contact_destinataire` | Téléphone du destinataire | Numéro de téléphone pour la livraison |
| `adresse_livraison` | Adresse de livraison | Adresse complète de livraison |
| `code_postal` | Code postal | Code postal de livraison |
| `ville` | Ville | Ville de livraison |
| `commune_livraison` | Commune de livraison | Commune pour la livraison |
| `quartier` | Quartier | Quartier de livraison |
| `user_id` | Utilisateur | Identifiant du demandeur |
| `agent_id` | Agent traitant | Identifiant de l'agent qui traite la demande |
| `livreur_id` | Livreur | Identifiant du livreur assigné |
| `agence_id` | Agence | Identifiant de l'agence |
| `livraison_id` | Point de retrait | Identifiant du point de retrait (poste) |
| `dhl_id` | DHL | Identifiant DHL (si applicable) |
| `created_at` | Date de création | Date de soumission de la demande |
| `updated_at` | Dernière modification | Date de dernière mise à jour |

---

## Table DECES (Demandes d'extrait de décès)

| Champ Technique | Libellé Français | Description |
|-----------------|------------------|-------------|
| `id` | Identifiant | Identifiant unique de la demande |
| `name` | Nom du défunt | Nom et prénom du défunt |
| `numberR` | Numéro de registre | Numéro du registre d'état civil |
| `dateR` | Date de registre | Date d'enregistrement dans le registre |
| `CNIdfnt` | CNI du défunt | Carte Nationale d'Identité ou extrait de naissance du défunt (chemin fichier) |
| `CNIdcl` | Certificat médical de décès | Certificat médical attestant du décès (chemin fichier) |
| `documentMariage` | Document de mariage | Acte de mariage du défunt si applicable (chemin fichier) |
| `RequisPolice` | Réquisition de police | Réquisition de police si applicable (chemin fichier) |
| `reference` | Référence | Référence unique de la demande |
| `commune` | Commune | Commune d'enregistrement du décès |
| `quantite` | Quantité | Nombre d'exemplaires demandés |
| `etat` | État de traitement | Statut de la demande (en attente, réçu, terminé, rejetée) |
| `motif_de_rejet` | Motif de rejet | Explication du rejet de la demande |
| `champs_a_modifier` | Champs à corriger | Liste des champs nécessitant une modification |
| `peut_modifier` | Peut modifier | Indicateur si l'utilisateur peut modifier la demande |
| `statut_livraison` | Statut de livraison | État de la livraison (en attente, en cours, livré) |
| `livraison_code` | Code de livraison | Code unique pour la livraison |
| `qr_code_path` | Chemin QR Code | Chemin vers l'image du QR code |
| `choix_option` | Option de récupération | Mode de récupération (livraison, retrait sur place) |
| `montant_timbre` | Montant timbre | Coût du timbre fiscal |
| `montant_livraison` | Frais de livraison | Coût de la livraison |
| `nom_destinataire` | Nom du destinataire | Nom pour la livraison |
| `prenom_destinataire` | Prénom du destinataire | Prénom pour la livraison |
| `email_destinataire` | Email du destinataire | Adresse email pour la livraison |
| `contact_destinataire` | Téléphone du destinataire | Numéro de téléphone pour la livraison |
| `adresse_livraison` | Adresse de livraison | Adresse complète de livraison |
| `code_postal` | Code postal | Code postal de livraison |
| `ville` | Ville | Ville de livraison |
| `commune_livraison` | Commune de livraison | Commune pour la livraison |
| `quartier` | Quartier | Quartier de livraison |
| `user_id` | Utilisateur | Identifiant du demandeur |
| `agent_id` | Agent traitant | Identifiant de l'agent qui traite la demande |
| `livreur_id` | Livreur | Identifiant du livreur assigné |
| `agence_id` | Agence | Identifiant de l'agence |
| `livraison_id` | Point de retrait | Identifiant du point de retrait (poste) |
| `dhl_id` | DHL | Identifiant DHL (si applicable) |
| `created_at` | Date de création | Date de soumission de la demande |
| `updated_at` | Dernière modification | Date de dernière mise à jour |

---

## Résumé des champs spécifiques par table

### Champs Documents (avec chemins fichiers)

| Table | Champ | Libellé |
|-------|-------|---------|
| Naissance | `CNI` | Carte Nationale d'Identité |
| Mariage | `pieceIdentite` | Pièce d'identité |
| Mariage | `extraitMariage` | Extrait de mariage |
| Décès | `CNIdfnt` | CNI du défunt |
| Décès | `CNIdcl` | Certificat médical de décès |
| Décès | `documentMariage` | Document de mariage |
| Décès | `RequisPolice` | Réquisition de police |

### États possibles (`etat`)

| Valeur | Description |
|--------|-------------|
| `en attente` | Demande en attente de traitement |
| `réçu` | Demande reçue et en cours de traitement |
| `terminé` | Demande traitée avec succès |
| `rejetée` | Demande rejetée (nécessite correction) |

### Statuts de livraison (`statut_livraison`)

| Valeur | Description |
|--------|-------------|
| `null` | Pas de livraison ou livraison non initiée |
| `en attente` | Livraison en attente d'affectation |
| `en cours` | Livraison en cours |
| `livré` | Document livré au destinataire |

---

*Document généré le 09/01/2026 - Plateau App*
