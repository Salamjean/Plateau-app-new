                <div class="topbar" >
                  <nav class="navbar navbar-expand-lg navbar-light" style="background-color:#1977cc">
                     <div class="full">
                        <button type="button" id="sidebarCollapse" class="sidebar_toggle"><i class="fa fa-bars"></i></button>
                        <div class="logo_section">
                           <a href="{{route('user.dashboard')}}"><img class="img-responsive" src="{{asset('assets/assets/img/logo plateau.png')}}" alt="#" /></a>
                        </div>
                        <div class="right_topbar">
                           <div class="icon_info" style="background-color:#1977cc">
                              <ul>
                                 <!-- Icône de notification -->
                                 <li class="notification-item">
                                    <a href="#" id="notificationBell" class="notification-bell">
                                       <i class="fa fa-bell-o"></i>
                                       <span class="badge notification-badge" id="notificationCount" style="display: none;">0</span>
                                    </a>
                                    <!-- Dropdown des notifications -->
                                    <div class="notification-dropdown" id="notificationDropdown">
                                       <div class="notification-header">
                                          <h6><i class="fa fa-bell"></i> Notifications</h6>
                                          <a href="#" id="markAllRead" class="mark-all-read">Tout marquer comme lu</a>
                                       </div>
                                       <div class="notification-list" id="notificationList">
                                          <div class="notification-loading">
                                             <i class="fa fa-spinner fa-spin"></i> Chargement...
                                          </div>
                                       </div>
                                       <div class="notification-footer">
                                          <a href="{{route('user.history')}}">Voir l'historique</a>
                                          <a href="#" id="deleteAllNotifications" class="delete-all-notifications"><i class="fa fa-trash"></i> Tout supprimer</a>
                                       </div>
                                    </div>
                                 </li>
                              </ul>
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
                                       <a class="dropdown-item" href="{{route('user.profile.show')}}"><i class="fa fa-user"></i> Profil</a>
                                       <a class="dropdown-item" href="{{route('user.logout')}}"><i class="fa fa-sign-out"></i> <span>Déconnexion</span> </a>
                                    </div>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </nav>
               </div>

<style>
/* Styles pour les notifications - sélecteurs universels avec haute priorité */
.notification-item {
   position: relative !important;
   display: inline-block !important;
   list-style: none !important;
   z-index: 1000 !important;
}

#notificationBell,
.notification-bell {
   color: white !important;
   font-size: 22px !important;
   padding: 10px 15px !important;
   display: inline-flex !important;
   align-items: center !important;
   justify-content: center !important;
   position: relative !important;
   cursor: pointer !important;
   transition: all 0.3s ease !important;
   text-decoration: none !important;
   background: transparent !important;
   border: none !important;
   min-width: 44px !important;
   min-height: 44px !important;
}

/* Forcer l'affichage de l'icône cloche sur TOUTES les pages */
#notificationBell i,
#notificationBell .fa,
#notificationBell .fa-bell-o,
.notification-bell i,
.notification-bell .fa,
.notification-bell .fa-bell-o {
   font-size: 22px !important;
   color: white !important;
   display: inline-block !important;
   visibility: visible !important;
   opacity: 1 !important;
   line-height: 1 !important;
   font-family: 'FontAwesome' !important;
   font-style: normal !important;
   font-weight: normal !important;
}

#notificationBell:hover,
.notification-bell:hover {
   opacity: 0.8 !important;
   color: white !important;
}

.notification-badge {
   position: absolute;
   top: 2px;
   right: 5px;
   background-color: #ff0854;
   color: white;
   font-size: 10px;
   padding: 2px 6px;
   border-radius: 50%;
   min-width: 18px;
   text-align: center;
   animation: pulse 2s infinite;
}

@keyframes pulse {
   0% { transform: scale(1); }
   50% { transform: scale(1.1); }
   100% { transform: scale(1); }
}

.notification-dropdown {
   position: absolute;
   top: 100%;
   right: 0;
   width: 350px;
   max-height: 450px;
   background: white;
   border-radius: 12px;
   box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
   z-index: 1000;
   display: none;
   overflow: hidden;
}

.notification-dropdown.active {
   display: block;
   animation: slideDown 0.3s ease;
}

@keyframes slideDown {
   from { opacity: 0; transform: translateY(-10px); }
   to { opacity: 1; transform: translateY(0); }
}

.notification-header {
   display: flex !important;
   justify-content: space-between !important;
   align-items: center !important;
   padding: 12px 15px !important;
   background: linear-gradient(135deg, #1977cc, #1565b8) !important;
   color: white !important;
   flex-wrap: nowrap !important;
   gap: 10px !important;
}

.notification-header h6 {
   margin: 0 !important;
   font-weight: 600 !important;
   font-size: 14px !important;
   white-space: nowrap !important;
   flex-shrink: 0 !important;
   display: flex !important;
   align-items: center !important;
}

.notification-header h6 i {
   margin-right: 8px !important;
}

.mark-all-read {
   color: rgba(255, 255, 255, 0.9) !important;
   font-size: 11px !important;
   text-decoration: none !important;
   transition: all 0.3s ease !important;
   white-space: nowrap !important;
   flex-shrink: 0 !important;
}

.mark-all-read:hover {
   color: white;
   text-decoration: underline;
}

.notification-list {
   max-height: 320px;
   overflow-y: auto;
}

.notification-list::-webkit-scrollbar {
   width: 5px;
}

.notification-list::-webkit-scrollbar-track {
   background: #f1f1f1;
}

.notification-list::-webkit-scrollbar-thumb {
   background: #1977cc;
   border-radius: 10px;
}

.notification-loading {
   padding: 30px;
   text-align: center;
   color: #666;
}

.notification-empty {
   padding: 40px 20px;
   text-align: center;
   color: #999;
}

.notification-empty i {
   font-size: 40px;
   margin-bottom: 10px;
   opacity: 0.3;
}

.notification-entry {
   display: flex;
   padding: 12px 15px;
   border-bottom: 1px solid #f0f0f0;
   cursor: pointer;
   transition: all 0.3s ease;
   text-decoration: none;
}

.notification-entry:hover {
   background-color: #f8f9fa;
}

.notification-entry.unread {
   background-color: #e8f4fd;
}

.notification-entry.unread:hover {
   background-color: #d4ebfa;
}

.notification-icon {
   width: 40px;
   height: 40px;
   border-radius: 50%;
   display: flex;
   align-items: center;
   justify-content: center;
   margin-right: 12px;
   flex-shrink: 0;
}

.notification-content {
   flex: 1;
   min-width: 0;
}

.notification-content p {
   margin: 0;
   font-size: 13px;
   color: #333;
   line-height: 1.4;
   word-wrap: break-word;
}

.notification-content .notification-time {
   font-size: 11px;
   color: #999;
   margin-top: 4px;
}

.notification-content .notification-status {
   display: inline-block;
   padding: 2px 8px;
   border-radius: 12px;
   font-size: 10px;
   font-weight: 600;
   margin-top: 5px;
}

.notification-footer {
   padding: 12px;
   display: flex;
   justify-content: space-between;
   align-items: center;
   background: #f8f9fa;
   border-top: 1px solid #eee;
}

.notification-footer a {
   color: #1977cc;
   text-decoration: none;
   font-size: 12px;
   font-weight: 500;
}

.notification-footer a:hover {
   text-decoration: underline;
}

.delete-all-notifications {
   color: #dc3545 !important;
   background-color: #ffebee !important;
   padding: 5px 10px !important;
   border-radius: 5px !important;
   font-weight: 600 !important;
   display: inline-flex !important;
   align-items: center !important;
   gap: 5px !important;
}

.delete-all-notifications:hover {
   color: #fff !important;
   background-color: #dc3545 !important;
   text-decoration: none !important;
}

/* Style responsive */
@media (max-width: 576px) {
   .notification-dropdown {
      width: 300px;
      right: -50px;
   }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
   const notificationBell = document.getElementById('notificationBell');
   const notificationDropdown = document.getElementById('notificationDropdown');
   const notificationCount = document.getElementById('notificationCount');
   const notificationList = document.getElementById('notificationList');
   const markAllRead = document.getElementById('markAllRead');

   // Toggle dropdown
   notificationBell.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      notificationDropdown.classList.toggle('active');
      if (notificationDropdown.classList.contains('active')) {
         loadNotifications();
      }
   });

   // Fermer dropdown en cliquant ailleurs
   document.addEventListener('click', function(e) {
      if (!notificationDropdown.contains(e.target) && e.target !== notificationBell) {
         notificationDropdown.classList.remove('active');
      }
   });

   // Charger les notifications
   function loadNotifications() {
      notificationList.innerHTML = '<div class="notification-loading"><i class="fa fa-spinner fa-spin"></i> Chargement...</div>';
      
      fetch('{{ route("user.notifications") }}')
         .then(response => response.json())
         .then(data => {
            if (data.success && data.notifications.length > 0) {
               notificationList.innerHTML = '';
               data.notifications.forEach(notification => {
                  const entry = document.createElement('a');
                  entry.href = notification.url;
                  entry.className = 'notification-entry' + (notification.is_read ? '' : ' unread');
                  entry.innerHTML = `
                     <div class="notification-icon" style="background-color: ${notification.color}20; color: ${notification.color}">
                        <i class="fa ${notification.icon}"></i>
                     </div>
                     <div class="notification-content">
                        <p>${notification.message}</p>
                        <span class="notification-time">${notification.created_at}</span>
                     </div>
                  `;
                  
                  entry.addEventListener('click', function() {
                     markAsRead(notification.id);
                  });
                  
                  notificationList.appendChild(entry);
               });
            } else {
               notificationList.innerHTML = `
                  <div class="notification-empty">
                     <i class="fa fa-bell-slash-o"></i>
                     <p>Aucune notification</p>
                  </div>
               `;
            }
         })
         .catch(error => {
            notificationList.innerHTML = '<div class="notification-empty"><p>Erreur de chargement</p></div>';
            console.error('Error:', error);
         });
   }

   // Charger le compteur
   function loadUnreadCount() {
      fetch('{{ route("user.notifications.count") }}')
         .then(response => response.json())
         .then(data => {
            if (data.success && data.count > 0) {
               notificationCount.textContent = data.count > 99 ? '99+' : data.count;
               notificationCount.style.display = 'block';
            } else {
               notificationCount.style.display = 'none';
            }
         })
         .catch(error => console.error('Error:', error));
   }

   // Marquer comme lu
   function markAsRead(id) {
      fetch(`{{ url('user/notifications') }}/${id}/read`, {
         method: 'POST',
         headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
         }
      }).then(() => loadUnreadCount());
   }

   // Marquer tout comme lu
   markAllRead.addEventListener('click', function(e) {
      e.preventDefault();
      fetch('{{ route("user.notifications.readAll") }}', {
         method: 'POST',
         headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
         }
      })
      .then(response => response.json())
      .then(data => {
         if (data.success) {
            loadUnreadCount();
            loadNotifications();
         }
      });
   });

   // Supprimer toutes les notifications
   const deleteAllBtn = document.getElementById('deleteAllNotifications');
   if (deleteAllBtn) {
      deleteAllBtn.addEventListener('click', function(e) {
         e.preventDefault();
         if (confirm('Êtes-vous sûr de vouloir supprimer toutes les notifications ?')) {
            fetch('{{ route("user.notifications.deleteAll") }}', {
               method: 'DELETE',
               headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
               }
            })
            .then(response => response.json())
            .then(data => {
               if (data.success) {
                  loadUnreadCount();
                  loadNotifications();
               }
            });
         }
      });
   }

   // Charger le compteur au démarrage et rafraîchir toutes les 30 secondes
   loadUnreadCount();
   setInterval(loadUnreadCount, 30000);
});
</script>