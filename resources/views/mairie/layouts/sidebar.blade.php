<aside class="mdc-drawer mdc-drawer--dismissible mdc-drawer--open" style="background-color: red">
      <div class="mdc-drawer__header" >
        <a href="{{route('mairie.dashboard')}}" class="brand-logo">
           <img src="{{asset('assets/assets/img/logo plateau.png')}}" alt="Logo par défaut" style="width: 50%; padding-top:50px" />
        </a>
      </div>
      <div class="mdc-drawer__content">
        <div class="user-info">
          <p class="name text-center">Mairie : {{Auth::guard('mairie')->user()->name}} </p>
          <p class="email text-center">{{Auth::guard('mairie')->user()->email}}</p>
        </div>
        <div class="mdc-list-group">
          <nav class="mdc-list mdc-drawer-menu">
            <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="{{route('mairie.dashboard')}}">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">home</i>
                Tableau de bord
              </a>
            </div>
            <hr style="color: white">
            <hr style="color: white">
            <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-menu">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">home</i>
                Etat civil
                <i class="mdc-drawer-arrow material-icons">chevron_right</i>
              </a>
              <div class="mdc-expansion-panel" id="ui-sub-menu">
                <nav class="mdc-list mdc-drawer-submenu">
                  <div class="mdc-list-item mdc-drawer-item">
                    @php
                      $hasEtatCivil = \App\Models\EtatCivil::where('mairie_id', Auth::guard('mairie')->user()->id)->exists();
                    @endphp
                    
                    @if($hasEtatCivil)
                      <a class="mdc-drawer-link disabled" href="javascript:void(0)" style="color: #ccc; pointer-events: none; cursor: not-allowed;">
                        Ajout du service
                      </a>
                    @else
                      <a class="mdc-drawer-link" href="{{route('mairie.state.create')}}">
                        Ajout du service
                      </a>
                    @endif
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{route('mairie.state.index')}}">
                     Informations
                    </a>
                  </div>
                </nav>
              </div>
            </div>

             <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="sample-page-submenu">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">monetization_on</i>
                Finance
                <i class="mdc-drawer-arrow material-icons">chevron_right</i>
              </a>
              <div class="mdc-expansion-panel" id="sample-page-submenu">
                <nav class="mdc-list mdc-drawer-submenu">
                  <div class="mdc-list-item mdc-drawer-item">
                    @php
                      $hasFinance = \App\Models\Finance::where('mairie_id', Auth::guard('mairie')->user()->id)->exists();
                    @endphp
                    
                    @if($hasFinance)
                      <a class="mdc-drawer-link disabled" href="javascript:void(0)" style="color: #ccc; pointer-events: none; cursor: not-allowed;">
                        Ajout du service
                      </a>
                    @else
                      <a class="mdc-drawer-link" href="{{route('mairie.finance.create')}}">
                        Ajout du service
                      </a>
                    @endif
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{route('mairie.finance.index')}}">
                      Informations
                    </a>
                  </div>
                </nav>
              </div>
            </div>

             <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="sample-page-ecole">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">local_shipping</i>
                Livraison
                <i class="mdc-drawer-arrow material-icons">chevron_right</i>
              </a>
              <div class="mdc-expansion-panel" id="sample-page-ecole">
                <nav class="mdc-list mdc-drawer-ecole">
                  <div class="mdc-list-item mdc-drawer-item">
                    @php
                      $hasPoste = \App\Models\Poste::exists();
                    @endphp
                    
                    @if($hasPoste)
                      <a class="mdc-drawer-link disabled" href="javascript:void(0)" style="color: #ccc; pointer-events: none; cursor: not-allowed;">
                        Ajout du service
                      </a>
                    @else
                      <a class="mdc-drawer-link" href="{{route('post.create')}}">
                        Ajout du service
                      </a>
                    @endif
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{route('post.index')}}">
                      Informations
                    </a>
                  </div>
                </nav>
              </div>
            </div>
              <hr style="color: white">
              <hr style="color: white">
            <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="{{route('mairie.request.birth')}}">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">child_care</i>
                Extrait Naissance
              </a>
            </div>
            <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="{{route('mairie.request.death')}}">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">spa</i>
                Extrait Décès
              </a>
            </div>
            <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="{{route('mairie.request.wedding')}}">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">favorite</i>
                Extrait Mariage
              </a>
            </div>
            <hr style="color: white">
            <hr style="color: white">
             <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="{{route('mairie.rendezvous.index')}}">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">favorite</i>
                Rendez-vous Mariage
              </a>
          </div>
    </aside>