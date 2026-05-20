<header class="mdc-top-app-bar" style="background-color: #1f4083;">
        <div class="mdc-top-app-bar__row">
          <div class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
            <button class="material-icons mdc-top-app-bar__navigation-icon mdc-icon-button sidebar-toggler" style="color: white">menu</button>
          </div>
          <div class="mdc-top-app-bar__section mdc-top-app-bar__section--align-end mdc-top-app-bar__section-right">
            <div class="menu-button-container menu-profile d-none d-md-block">
              <button class="mdc-button mdc-menu-button">
                <span class="d-flex align-items-center">
                  <span class="figure">
                        <img src="{{ optional(Auth::guard('finance')->user())->profile_picture 
                          ? asset('storage/' . Auth::guard('finance')->user()->profile_picture) 
                          : asset('assets/assets/img/logo plateau.png') }}" alt="Logo plateau"  class="user" />
                  </span>
                  <span class="user-name"  style="color: white; font-size:20px; font-weight:bold">{{Auth::guard('finance')->user()->name_respo}} </span>
                  <span class="ml-2 text-white">&#9662;</span>
                  
                </span>
              </button>
              <div class="mdc-menu mdc-menu-surface" tabindex="-1">
                <ul class="mdc-list" role="menu" aria-hidden="true" aria-orientation="vertical">
                  <li>
                    <a href="" class="mdc-list-item" role="menuitem">
                      <div class="item-thumbnail item-thumbnail-icon-only">
                        <i class="mdi mdi-account-edit-outline text-primary"></i>
                      </div>
                      <div class="item-content d-flex align-items-start flex-column justify-content-center">
                        <h6 class="item-subject font-weight-normal">Profil</h6>
                      </div>
                    </a>
                  </li>
                  <li>
                    <a href="{{route('finance.logout')}}" class="mdc-list-item" role="menuitem">
                      <div class="item-thumbnail item-thumbnail-icon-only">
                        <i class="mdi mdi-logout text-primary"></i>                      
                      </div>
                      <div class="item-content d-flex align-items-start flex-column justify-content-center">
                        <h6 class="item-subject font-weight-normal">Déconnexion</h6>
                      </div>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </header>
      <style>
        .mdc-menu-button .ml-2 {
            font-size: 0.9rem; /* Ajustez la taille de la flèche ici */
            transition: transform 0.3s ease; /* Pour une animation lors de l'ouverture */
        }

        .mdc-menu-button:focus .ml-2 {
            transform: rotate(180deg); /* Optionnel : fait pivoter la flèche quand le menu est ouvert */
        }
      </style>
