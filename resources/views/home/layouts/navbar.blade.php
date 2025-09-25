<header id="header" class="header sticky-top">
    <div class="topbar d-flex align-items-center">
      <div class="container d-flex justify-content-center justify-content-md-between">
        <div class="contact-info d-flex align-items-center">
          <i class="bi bi-envelope d-flex align-items-center"><a href="mailto:contact@plateau.com">contact@plateau.com</a></i>
          <i class="bi bi-phone d-flex align-items-center ms-4"><span>+225 07 095 005 01</span></i>
        </div>
        <div class="social-links d-none d-md-flex align-items-center">
          <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
    </div><!-- End Top Bar -->

    <div class="branding d-flex align-items-center">

      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="{{route('home')}}" class="logo d-flex align-items-center me-auto">
          <!-- Uncomment the line below if you also wish to use an image logo -->
          <!-- <img src="assets/img/logo.png" alt=""> -->
          <h1 class="sitename">Plateau Apps</h1>
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="{{route('home')}}" class="active">Accueil<br></a></li>
            <li><a href="{{route('about.demande')}}">A propos de nous</a></li>
            <li><a href="{{route('service.demande')}}">Services</a></li>
            <li><a href="{{route('department.demande')}}">Departements</a></li>
            <li class="dropdown"><a href="#"><span>Actes civils</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="{{route('home.birth')}}">Acte de naissance</a></li>
                <li><a href="{{route('home.death')}}">Acte de décès</a></li>
                <li class="dropdown"><a href="#"><span>Mariage</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="{{route('home.wedding')}}">Acte de mariage</a></li>
                    <li><a href="{{route('home.rendezvous')}}">Rendez-vous</a></li>
                  </ul>
                </li>
              </ul>
            </li>
             <li><a href="{{route('home.contact')}}">Contactez-nous</a></li>
             <li><a href="{{route('recherche.demande')}}">Recherche </a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <a class="cta-btn d-none d-sm-block" href="{{route('login')}}">Se connecter</a>

      </div>

    </div>

  </header>