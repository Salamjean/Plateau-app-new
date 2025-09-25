<header class="mdc-top-app-bar" style="background-color: #ffd83e">
        <div class="mdc-top-app-bar__row">
          <div class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
            <button class="material-icons mdc-top-app-bar__navigation-icon mdc-icon-button sidebar-toggler">menu</button>
          </div>
          <div class="mdc-top-app-bar__section mdc-top-app-bar__section--align-end mdc-top-app-bar__section-right">
            <div class="menu-button-container menu-profile d-none d-md-block">
              <button class="mdc-button mdc-menu-button">
                <span class="d-flex align-items-center">
                  <span class="figure">
                    <img src="{{asset('assets/images/profiles/dhl.png')}}" alt="user" class="user">
                  </span>
                  <span class="user-name"> {{Auth::guard('dhl')->user()->name.' '.Auth::guard('dhl')->user()->prenom}} </span>
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
                        <h6 class="item-subject font-weight-normal">Mon compte</h6>
                      </div>
                    </a>
                  </li>
                  <li>
                    <a href="{{route('dhl.logout')}}" class="mdc-list-item" role="menuitem">
                      <div class="item-thumbnail item-thumbnail-icon-only">
                        <i class="mdi mdi-settings-outline text-primary"></i>                      
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