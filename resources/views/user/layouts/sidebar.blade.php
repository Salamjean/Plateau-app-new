<nav id="sidebar">
   <div class="sidebar_blog_1">
      <div class="sidebar-header">
         <div class="logo_section">
            <a href="{{route('user.dashboard')}}"><img class="logo_icon img-responsive" src="{{ optional(Auth::user())->profile_picture
   ? asset('storage/' . Auth::user()->profile_picture)
   : asset('assets/assets/img/Avato.png') }}" alt="#" /></a>
         </div>
      </div>
      <div class="sidebar_user_info">
         <div class="icon_setting"></div>
         <div class="user_profle_side">
            <div class="user_img"><img class=" img-responsive" src="{{ optional(Auth::user())->profile_picture
   ? asset('storage/' . Auth::user()->profile_picture)
   : asset('assets/images/profiles/useriii.jpeg') }}"
                  alt="Profile Picture" style="width: 80%" /></div>
            <div class="user_info">
               <h6>{{ Auth::user()->name }} <br>{{ Auth::user()->prenom }}</h6>
               <p><span class="online_animation"></span> En ligne</p>
            </div>
         </div>
      </div>
   </div>
   <div class="sidebar_blog_2">
      <a href="{{route('user.dashboard')}}">
         <h4> <i class="fas fa-dashboard"></i> Menu général</h4>
      </a>
      <ul class="list-unstyled components">

         <li><a href="{{route('user.dashboard')}}"><i class="fas fa-gauge yellow_color"></i> <span>Tableau de
                  bord</span></a></li>
         <li>
            <a href="#element" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                  class="fas fa-baby purple_color"></i> <span>Acte de naissance</span></a>
            <ul class="collapse list-unstyled" id="element">
               <li style="text-align: center">Demande :</li>
               <li><a href="{{route('user.extrait.create')}}">> <span>Faire la demande</span></a></li>
               <li><a href="{{route('user.extrait.index')}}">> <span>Liste des demandes</span></a></li>
            </ul>
         </li>
         <li>
            <a href="#apps" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                  class="fas fa-cross blue2_color"></i> <span>Acte de décès</span></a>
            <ul class="collapse list-unstyled" id="apps">
               <li style="text-align: center">Demande :</li>
               <li><a href="{{route('user.extrait.deces.create')}}">> <span>Faire la demande</span></a></li>
               <li><a href="{{route('user.extrait.deces.index')}}">> <span>Liste des demandes</span></a></li>
            </ul>
         </li>
         <li class="active">
            <a href="#additional_page" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                  class="fas fa-ring yellow_color"></i> <span>Acte de mariage</span></a>
            <ul class="collapse list-unstyled" id="additional_page">
               <li style="text-align: center">Demande :</li>
               <li><a href="{{route('user.extrait.mariage.create')}}">> <span>Faire la demande</span></a></li>
               <li><a href="{{route('user.extrait.mariage.index')}}">> <span>Liste des demandes</span></a></li>
            </ul>
         </li>

         <li class="active">
            <a href="#additional_rend" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                  class="fas fa-calendar-check yellow_color"></i> <span>Rendez-vous de mariage</span></a>
            <ul class="collapse list-unstyled" id="additional_rend">
               <li style="text-align: center">Rendez-vous:</li>
               <li><a href="{{route('user.rendezvous.create')}}">> <span>Prendre un rendez-vous</span></a></li>
               <li><a href="{{route('user.rendezvous.index')}}">> <span>Liste des rendez-vous</span></a></li>
            </ul>
         </li>
         <li><a href="{{route('user.history')}}"><i class="fas fa-history yellow_color"></i> <span>Historique</span></a>
         </li>
      </ul>
   </div>

</nav>