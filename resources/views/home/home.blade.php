@extends('home.layouts.tamplate')
@section('content')

 <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section light-background">

      <img src="{{asset('assets/assets/img/C.jpg')}}"  alt="" data-aos="fade-in">

      <div class="container position-relative">

        <div class="welcome position-relative" data-aos="fade-down" data-aos-delay="100">
          <h2>BIENVENUE A LA MAIRIE DU PLATEAU</h2>
          <p>Vous pouvez désormais faire votre demandes d'actes civils à la mairie du plateau en ligne </p>
        </div><!-- End Welcome -->

        <div class="content row gy-4">
          <div class="col-lg-4 d-flex align-items-stretch">
            <div class="why-box" data-aos="zoom-out" data-aos-delay="200">
              <h3>Comment faire une demande d'acte civil</h3>
              <p>
                Pour faire une demande d'acte civil vous devez être en possession des informations suivantes : 
              </p>
            </div>
          </div><!-- End Why Box -->

          <div class="col-lg-8 d-flex align-items-stretch">
            <div class="d-flex flex-column justify-content-center">
              <div class="row gy-4">

                <div class="col-xl-4 d-flex align-items-stretch">
                  <div class="icon-box" data-aos="zoom-out" data-aos-delay="300">
                    <i class="bi bi-clipboard-data"></i>
                    <h4>Acte de naissance</h4>
                    <p>- Connaitre le numéro de registre</p>
                    <p>- Connaitre la date de registre</p>
                    <p>- Avoir une copie de l'extrait</p>
                  </div>
                </div><!-- End Icon Box -->

                <div class="col-xl-4 d-flex align-items-stretch">
                  <div class="icon-box" data-aos="zoom-out" data-aos-delay="400">
                    <i class="bi bi-gem"></i>
                    <h4>Acte de décès</h4>
                    <p>- Connaitre le numéro de registre</p>
                    <p>- Connaitre la date de registre</p>
                    <p>- Avoir une copie de l'extrait</p>
                  </div>
                </div><!-- End Icon Box -->

                <div class="col-xl-4 d-flex align-items-stretch">
                  <div class="icon-box" data-aos="zoom-out" data-aos-delay="500">
                    <i class="bi bi-inboxes"></i>
                    <h4>Acte de mariage</h4>
                    <p>- Connaitre le numéro de registre</p>
                    <p>- Connaitre la date de registre</p>
                    <p>- Avoir une copie de l'extrait</p>
                  </div>
                </div><!-- End Icon Box -->

              </div>
            </div>
          </div>
        </div><!-- End  Content-->

      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">

      <div class="container">

        <div class="row gy-4 gx-5">

          <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="200">
            <img src="{{asset('assets/assets/img/Plateau-immeuble.jpg')}}" class="img-fluid" alt="">
            <a href="{{asset('assets/assets/img/CÔTE D IVOIRE _ LE QUARTIER DES AFFAIRES, Plateau, Abidjan.mp4')}}" class="glightbox pulsating-play-btn"></a>
          </div>

          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
            <h3>Présentation</h3>
            <p>
              Entouré d'eau, Le Plateau est le quartier d'affaires animé de la ville, où se trouvent des bâtiments gouvernementaux et des immeubles de bureaux, ainsi que la cathédrale Saint-Paul, qui comprend une tour moderne en forme de croix. Des sculptures, des céramiques et des statues sont exposées au sein du musée des Civilisations de Côte d'Ivoire, et le stade Félix-Houphouët-Boigny accueille des meetings d'athlétisme et des matchs de football internationaux. Quelques boulangeries et restaurants haut de gamme sont également parsemés dans le quartier.
            </p>
            <ul>
              <li>
                <i class="fa-solid fa-vial-circle-check"></i>
                <div>
                  <h5>Etat Civil</h5>
                  <p>Retrouvez toutes les démarches liées à l'état civil : naissance, mariage, décès…</p>
                </div>
              </li>
              <li>
                <i class="fa-solid fa-pump-medical"></i>
                <div>
                  <h5>Légalisation et Certification</h5>
                  <p>Il existe des imprimer à prendre sur place pour vous aider</p>
                </div>
              </li>
              <li>
                <i class="fa-solid fa-home"></i>
                <div>
                  <h5>Réservation de Salle</h5>
                  <p>Vous pouvez avoir des salles pour vos evenements</p>
                </div>
              </li>
            </ul>
          </div>

        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Services Section -->
    <section id="services" class="services section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Services</h2>
        <p>Vous pouvez avoir une aperçu de nous services</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-heartbeat"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Etat Civil</h3>
              </a>
              <p>Obtenez vos documents d'état civil (acte de naissance, mariage, décès) et effectuez toutes les démarches administratives liées à votre situation familiale.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-pills"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Légalisation et Certification</h3>
              </a>
              <p>Authentification et légalisation de vos documents officiels pour leur donner valeur légale à l'étranger ou dans les administrations concernées.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-hospital-user"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Certificat de Vie et d'Entretien</h3>
              </a>
              <p>Attestation officielle prouvant votre existence et votre situation financière, souvent requise pour les pensions ou allocations.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-dna"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Certificat de non remariage et Non divorce</h3>
              </a>
              <p>Document attestant de votre situation matrimoniale actuelle, nécessaire pour certaines procédures administratives ou juridiques.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-notes-medical"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Couverture Maladie Universelle</h3>
              </a>
              <p>Accédez à la protection sociale et aux soins médicaux grâce à la CMU. Inscription et renouvellement de votre couverture santé.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
              <a href="#" class="stretched-link">
                <h3>Déclaration d'une Activité</h3>
              </a>
              <p>Enregistrement officiel de votre activité professionnelle, qu'il s'agisse d'une entreprise, d'un commerce ou d'une profession libérale.</p>
            </div>
          </div><!-- End Service Item -->

        </div>

      </div>

    </section><!-- /Services Section -->

    <!-- Gallery Section -->
    <section id="gallery" class="gallery section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Gallery</h2>
        <p>Plateau en quelques images.</p>
      </div><!-- End Section Title -->

      <div class="container-fluid" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-0">

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="{{asset('assets/assets/img/im1.jpg')}}" class="glightbox" data-gallery="images-gallery">
                <img src="{{asset('assets/assets/img/im1.jpg')}}" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="{{asset('assets/assets/img/im2.jpg')}}" class="glightbox" data-gallery="images-gallery">
                <img src="{{asset('assets/assets/img/im2.jpg')}}" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="{{asset('assets/assets/img/im10.png')}}" class="glightbox" data-gallery="images-gallery">
                <img src="{{asset('assets/assets/img/im10.png')}}" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="{{asset('assets/assets/img/im16.jpg')}}" class="glightbox" data-gallery="images-gallery">
                <img src="{{asset('assets/assets/img/im16.jpg')}}" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="{{asset('assets/assets/img/im15.jpg')}}" class="glightbox" data-gallery="images-gallery">
                <img src="{{asset('assets/assets/img/im15.jpg')}}" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="{{asset('assets/assets/img/im12.jpg')}}" class="glightbox" data-gallery="images-gallery">
                <img src="{{asset('assets/assets/img/im12.jpg')}}" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="{{asset('assets/assets/img/im13.jpg')}}" class="glightbox" data-gallery="images-gallery">
                <img src="{{asset('assets/assets/img/im13.jpg')}}" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="{{asset('assetsMairie/images/samples/1280x768/4.jpg')}}" class="glightbox" data-gallery="images-gallery">
                <img src="{{asset('assetsMairie/images/samples/1280x768/4.jpg')}}" alt="" class="img-fluid">
              </a>
            </div>
          </div><!-- End Gallery Item -->

        </div>

      </div>

    </section><!-- /Gallery Section -->

  </main>

@endsection