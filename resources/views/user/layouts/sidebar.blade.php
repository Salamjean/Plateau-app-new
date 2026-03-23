<nav id="sidebar">
   <div class="sidebar-header">
      <button type="button" id="sidebarClose" class="d-lg-none border-0 bg-transparent" style="position: absolute; top: 15px; right: 15px; font-size: 20px; color: var(--primary);">
         <i class="fas fa-times"></i>
      </button>
      <a href="{{route('user.dashboard')}}">
         <img src="{{asset('assets/assets/img/logo plateau.png')}}" alt="Logo" />
      </a>
   </div>
   
   <ul class="list-unstyled components">
      <li class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }} item-dashboard">
         <a href="{{route('user.dashboard')}}">
            <i class="fas fa-th-large"></i> <span>Tableau de bord</span>
         </a>
      </li>
      
      <li class="item-birth @if(request()->routeIs(['user.extrait.create', 'user.extrait.index'])) active @endif">
         <a href="#acteNaissance" data-toggle="collapse" 
            aria-expanded="{{ request()->routeIs(['user.extrait.create', 'user.extrait.index']) ? 'true' : 'false' }}" 
            class="dropdown-toggle {{ request()->routeIs(['user.extrait.create', 'user.extrait.index']) ? '' : 'collapsed' }}">
            <i class="fas fa-baby"></i> <span>Naissance</span>
         </a>
         <ul class="collapse list-unstyled {{ request()->routeIs(['user.extrait.create', 'user.extrait.index']) ? 'show' : '' }}" id="acteNaissance">
            <li class="{{ request()->routeIs('user.extrait.create') ? 'active-link' : '' }}"><a href="{{route('user.extrait.create')}}">Nouvelle demande</a></li>
            <li class="{{ request()->routeIs('user.extrait.index') ? 'active-link' : '' }}"><a href="{{route('user.extrait.index')}}">Mes demandes</a></li>
         </ul>
      </li>

      <li class="item-death @if(request()->routeIs(['user.extrait.deces.create', 'user.extrait.deces.index'])) active @endif">
         <a href="#acteDeces" data-toggle="collapse" 
            aria-expanded="{{ request()->routeIs(['user.extrait.deces.create', 'user.extrait.deces.index']) ? 'true' : 'false' }}" 
            class="dropdown-toggle {{ request()->routeIs(['user.extrait.deces.create', 'user.extrait.deces.index']) ? '' : 'collapsed' }}">
            <i class="fas fa-skull-crossbones"></i> <span>Décès</span>
         </a>
         <ul class="collapse list-unstyled {{ request()->routeIs(['user.extrait.deces.create', 'user.extrait.deces.index']) ? 'show' : '' }}" id="acteDeces">
            <li class="{{ request()->routeIs('user.extrait.deces.create') ? 'active-link' : '' }}"><a href="{{route('user.extrait.deces.create')}}">Nouvelle demande</a></li>
            <li class="{{ request()->routeIs('user.extrait.deces.index') ? 'active-link' : '' }}"><a href="{{route('user.extrait.deces.index')}}">Mes demandes</a></li>
         </ul>
      </li>

      <li class="item-marriage @if(request()->routeIs(['user.extrait.mariage.create', 'user.extrait.mariage.index'])) active @endif">
         <a href="#acteMariage" data-toggle="collapse" 
            aria-expanded="{{ request()->routeIs(['user.extrait.mariage.create', 'user.extrait.mariage.index']) ? 'true' : 'false' }}" 
            class="dropdown-toggle {{ request()->routeIs(['user.extrait.mariage.create', 'user.extrait.mariage.index']) ? '' : 'collapsed' }}">
            <i class="fas fa-heart"></i> <span>Mariage</span>
         </a>
         <ul class="collapse list-unstyled {{ request()->routeIs(['user.extrait.mariage.create', 'user.extrait.mariage.index']) ? 'show' : '' }}" id="acteMariage">
            <li class="{{ request()->routeIs('user.extrait.mariage.create') ? 'active-link' : '' }}"><a href="{{route('user.extrait.mariage.create')}}">Nouvelle demande</a></li>
            <li class="{{ request()->routeIs('user.extrait.mariage.index') ? 'active-link' : '' }}"><a href="{{route('user.extrait.mariage.index')}}">Mes demandes</a></li>
         </ul>
      </li>

      <li class="item-appointment @if(request()->routeIs(['user.rendezvous.create', 'user.rendezvous.index'])) active @endif">
         <a href="#rvMariage" data-toggle="collapse" 
            aria-expanded="{{ request()->routeIs(['user.rendezvous.create', 'user.rendezvous.index']) ? 'true' : 'false' }}" 
            class="dropdown-toggle {{ request()->routeIs(['user.rendezvous.create', 'user.rendezvous.index']) ? '' : 'collapsed' }}">
            <i class="fas fa-calendar-alt"></i> <span>Rendez-vous</span>
         </a>
         <ul class="collapse list-unstyled {{ request()->routeIs(['user.rendezvous.create', 'user.rendezvous.index']) ? 'show' : '' }}" id="rvMariage">
            <li class="{{ request()->routeIs('user.rendezvous.create') ? 'active-link' : '' }}"><a href="{{route('user.rendezvous.create')}}">Prendre RDV</a></li>
            <li class="{{ request()->routeIs('user.rendezvous.index') ? 'active-link' : '' }}"><a href="{{route('user.rendezvous.index')}}">Mes RDV</a></li>
         </ul>
      </li>

      <li class="{{ request()->routeIs('user.history') ? 'active' : '' }} item-history">
         <a href="{{route('user.history')}}">
            <i class="fas fa-history"></i> <span>Historique</span>
         </a>
      </li>
   </ul>
</nav>