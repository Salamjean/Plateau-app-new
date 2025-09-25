                <div class="topbar" >
                  <nav class="navbar navbar-expand-lg navbar-light" style="background-color:#1977cc">
                     <div class="full">
                        <button type="button" id="sidebarCollapse" class="sidebar_toggle"><i class="fa fa-bars"></i></button>
                        <div class="logo_section">
                           <a href="{{route('user.dashboard')}}"><img class="img-responsive" src="{{asset('assets/assets/img/logo plateau.png')}}" alt="#" /></a>
                        </div>
                        <div class="right_topbar">
                           <div class="icon_info" style="background-color:#1977cc">
                              {{-- <ul>
                                 <li><a href="#"><i class="fa fa-bell-o"></i><span class="badge">2</span></a></li>
                                 <li><a href="#"><i class="fa fa-question-circle"></i></a></li>
                                 <li><a href="#"><i class="fa fa-envelope-o"></i><span class="badge">3</span></a></li>
                              </ul> --}}
                              <ul class="user_profile_dd" >
                                 <li>
                                    <a class="dropdown-toggle" data-toggle="dropdown"> 
                                        <img src="{{ optional(Auth::user())->profile_picture 
                                                ? asset('storage/' . Auth::user()->profile_picture) 
                                                : asset('assets/images/profiles/useriii.jpeg') }}" 
                                        alt="Profile Picture">
                                        <span class="name_user" >{{ Auth::user()->name }} {{ Auth::user()->prenom }}</span>
                                    </a>
                                    <div class="dropdown-menu">
                                       <a class="dropdown-item" href="profile.html">Profil</a>
                                       <a class="dropdown-item" href="{{route('user.logout')}}"><i class="fa fa-sign-out"></i> <span>Déconnexion</span> </a>
                                    </div>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </nav>
               </div>