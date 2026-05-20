<div class="topbar">
   <div class="d-flex align-items-center">
      <button type="button" id="sidebarCollapse" class="sidebar_toggle">
         <i class="fa fa-bars"></i>
      </button>
      <h5 class="ml-3 mb-0 d-none d-md-block" style="font-weight: 700;">Tableau de bord</h5>
   </div>
   
   <div class="d-flex align-items-center">
      <div class="notification-item mr-3">
         <a href="#" class="notification-bell">
            <i class="fa fa-bell"></i>
            <span class="badge notification-badge">0</span>
         </a>
      </div>
      
      <div class="dropdown">
         <a class="dropdown-toggle d-flex align-items-center" data-toggle="dropdown" style="cursor:pointer; text-decoration:none;"> 
            @php
                $user = Auth::user();
                $navPic = optional($user)->profile_picture;
                $initials = strtoupper(substr($user->name ?? '', 0, 1) . substr($user->prenom ?? '', 0, 1));
            @endphp
            
            @if($navPic)
               <img src="{{ \Illuminate\Support\Str::startsWith($navPic, ['http://', 'https://']) ? $navPic : asset('storage/' . $navPic) }}"
               style="width:40px; height:40px; border-radius:50%; border: 2px solid rgba(255,255,255,0.5); object-fit: cover;" alt="Profile">
            @else
               <div class="d-flex align-items-center justify-content-center bg-white text-primary font-weight-bold" 
                    style="width:40px; height:40px; border-radius:50%; border: 2px solid rgba(255,255,255,0.5); font-size: 14px;">
                  {{ $initials }}
               </div>
            @endif
            <span class="name_user ml-2 d-none d-md-inline">{{ Auth::user()->name.' '.Auth::user()->prenom }}</span>
         </a>
         <div class="dropdown-menu dropdown-menu-right mt-3 border-0 shadow-sm" style="border-radius:12px;">
            <a class="dropdown-item" href="{{route('user.profile.show')}}"><i class="fa fa-user-circle mr-2 text-primary"></i> Profil</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="{{route('user.logout')}}"><i class="fa fa-sign-out-alt mr-2"></i> Déconnexion</a>
         </div>
      </div>
   </div>
</div>
