    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Helvetica, Arial, sans-serif !important;
        }

        html,
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000000;
            background-color: #fff;
        }

        @page {
            size: 80mm 210mm;
            margin: 4mm 4mm;
        }

        .etiquette-content {
            width: 100%;
            height: 100%;
        }

        /* Modern header */
        .etiquette-header {
            background-color: #ffffff;
            color: #000000;
            border: 2px solid #000000;
            text-align: center;
            padding: 7px 0;
            font-size: 11pt;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 4mm;
            border-radius: 4px;
        }

        /* Information Grid */
        .grid-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 4mm;
        }

        .grid-card {
            border: 1px solid #000000;
            border-radius: 4px;
            padding: 8px 10px;
            background: #ffffff;
        }

        .card-label {
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #444444;
            display: block;
            margin-bottom: 4px;
            border-bottom: 0.5px solid #dddddd;
            padding-bottom: 2px;
        }

        .card-value {
            font-size: 10pt;
            color: #000000;
            line-height: 1.4;
        }

        .card-value-large {
            font-size: 12pt;
            color: #000000;
            margin-bottom: 4px;
        }

        .sub-info {
            font-size: 10pt;
            color: #111111;
        }

        /* Large Reference Footer Box */
        .footer-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 2mm;
        }

        .ref-card {
            border: 2px solid #000000;
            border-radius: 4px;
            padding: 10px 12px;
            background-color: #ffffff;
        }

        .reference-number {
            font-size: 21pt;
            letter-spacing: 1px;
            line-height: 1.1;
            color: #000000;
            text-align: center;
            margin-top: 3px;
        }

        .type-colis-badge {
            font-size: 8.5pt;
            color: #333333;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .counter-card {
            border: 1px solid #000000;
            border-radius: 4px;
            background-color: #ffffff;
            text-align: center;
            padding: 8px 4px;
        }

        .counter-text {
            font-size: 16pt;
            line-height: 1.1;
        }

        .counter-label {
            font-size: 6.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #555555;
        }

        .custom-logo {
            max-height: 14mm;
            max-width: 100%;
            display: block;
            margin: 0 auto;
        }

        .qr-img {
            max-height: 17mm;
            max-width: 100%;
            display: block;
            margin: 0 auto;
        }

        .qr-placeholder {
            font-size: 8pt;
            color: #888888;
            border: 1px dashed #cccccc;
            padding: 6px;
            text-align: center;
            border-radius: 3px;
        }

        .bw-logo {
            filter: grayscale(100%);
        }

        .etiquette-page:not(.last-page) {
            page-break-after: always !important;
        }
    </style>
    </head>

    <body>
        <div class="etiquette-page last-page">
            <div class="etiquette-content">
                <!-- Sleek Badge Header -->
                <div class="etiquette-header">
                    ACTE DE MARIAGE • LIVRAISON
                </div>

                <!-- Block 1: Municipal Identity & Logos -->
                <table class="grid-table" style="margin-bottom: 3mm;">
                    <tr>
                        <td class="grid-card">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 25%; text-align: center; vertical-align: middle;">
                                        @if (file_exists(public_path('assets/assets/img/logo plateau.png')))
                                            <img src="{{ public_path('assets/assets/img/logo plateau.png') }}"
                                                class="custom-logo" alt="Logo">
                                        @else
                                            <span class="qr-placeholder"
                                                style="display: block; padding: 4px 0;">Logo</span>
                                        @endif
                                    </td>
                                    <td style="width: 50%; text-align: center; vertical-align: middle; padding: 0 5px;">
                                        <div style="font-size: 9.5pt; letter-spacing: 0.5px;">MAIRIE DE PLATEAU</div>
                                        <div class="etat-civil-title"
                                            style="font-size: 9.5pt; color: #000000; margin-top: 1px;">État Civil •
                                            Mariages</div>
                                    </td>
                                    <td style="width: 25%; text-align: center; vertical-align: middle;">
                                        @if ($naissance->qr_code_path && file_exists(public_path('storage/' . $naissance->qr_code_path)))
                                            <img src="{{ public_path('storage/' . $naissance->qr_code_path) }}"
                                                class="qr-img" style="max-height: 14mm;" alt="QR Code">
                                        @else
                                            <div class="qr-placeholder" style="padding: 4px 0;">QR</div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Block 2: Date and Expéditeur (Side by Side in 1 box) -->
                <table class="grid-table" style="margin-bottom: 3mm;">
                    <tr>
                        <td class="grid-card">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 50%; border-right: 1px solid #dddddd; padding-right: 5px;">
                                        <span
                                            style="font-size: 9.5pt; letter-spacing: 0.5px; display: block; color: #000000; text-transform: uppercase;">Date
                                            Demande</span>
                                        <div class="card-value">
                                            {{ $naissance->created_at->format('d/m/Y') }}
                                            <span style="font-size: 8.5pt; color: #00000; margin-left: 3px;">
                                                {{ $naissance->created_at->format('H:i') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td style="width: 50%; padding-left: 8px;">
                                        <span
                                            style="font-size: 9.5pt; letter-spacing: 0.5px; display: block; color: #000000; text-transform: uppercase;">Expéditeur</span>
                                        <div class="card-value">Mairie de {{ $naissance->commune ?? 'Plateau' }}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Block 3: Destinataire (Main big block) -->
                @php
                    $destinataireNom = trim(
                        (string) ($naissance->nom_destinataire ?? '') .
                            ' ' .
                            (string) ($naissance->prenom_destinataire ?? ''),
                    );
                    $destinataireNom =
                        $destinataireNom !== ''
                            ? $destinataireNom
                            : trim(
                                (string) ($naissance->user->name ?? '') .
                                    ' ' .
                                    (string) ($naissance->user->prenom ?? ''),
                            );
                    $destinataireNom = $destinataireNom !== '' ? $destinataireNom : 'Non spécifié';

                    $telephoneDestinataire = !empty($naissance->contact_destinataire)
                        ? $naissance->contact_destinataire
                        : (!empty($naissance->contact)
                            ? $naissance->contact
                            : $naissance->user->contact ?? 'Non spécifié');

                    $adresseLivraison = trim((string) ($naissance->adresse_livraison ?? ($naissance->adresse ?? '')));
                @endphp
                <table class="grid-table" style="margin-bottom: 3mm;">
                    <tr>
                        <td class="grid-card" style="padding: 10px 12px;">
                            <span
                                style="font-size: 9pt; letter-spacing: 0.5px; display: block; color: #000000; text-transform: uppercase;">Destinataire
                                & Adresse de Livraison</span>
                            <div class="card-value-large" style="font-size: 10pt; margin-bottom: 6px;">
                                Dest. : {{ $destinataireNom }}
                            </div>
                            <div class="sub-info" style="font-size: 10pt; line-height: 1.5;">
                                Téléphone : {{ $telephoneDestinataire }} <br>
                                Adresse : {{ $adresseLivraison !== '' ? $adresseLivraison : 'Adresse non spécifiée' }}
                                <br>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Block 4: Reference & Tracking (Barcode/QR & Counter) -->
                <table class="footer-table">
                    <tr>
                        <td class="ref-card" style="width: 75%; vertical-align: middle;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 15%; vertical-align: middle; text-align: left;">
                                        @if ($naissance->qr_code_path && file_exists(public_path('storage/' . $naissance->qr_code_path)))
                                            <img src="{{ public_path('storage/' . $naissance->qr_code_path) }}"
                                                style="max-height: 12mm; display: block;" alt="QR Ref">
                                        @endif
                                    </td>
                                    <td style="width: 85%; vertical-align: middle; padding-left: 8px;">
                                        <span class="card-label"
                                            style="font-size: 9pt; letter-spacing: 0.5px; display: block; color: #000000; text-transform: uppercase;">RÉFÉRENCE
                                            DE LIVRAISON</span>
                                        <div class="reference-number" style="text-align: left; font-size: 18pt;">
                                            {{ $naissance->livraison_code }}</div>
                                        <div class="type-colis-badge"
                                            style="font-size: 7pt; letter-spacing: 0.5px; display: block; color: #000000; text-transform: uppercase;">
                                            Extrait de Mariage
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </body>

    </html>
