
<!DOCTYPE html>
<html lang="en">
   <head>
      <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <!-- site metas -->
      <title>User connect</title>
      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">
      <!-- site icon -->
       <link rel="shortcut icon" href="{{asset('assets/assets/img/logo plateau.png')}}" />
      <!-- bootstrap css -->
      <link rel="stylesheet" href="{{asset('userAssets/css/bootstrap.min.css')}}" />
      <!-- site css -->
      <link rel="stylesheet" href="{{asset('userAssets/style.css')}}" />
      <!-- responsive css -->
      <link rel="stylesheet" href="{{asset('userAssets/css/responsive.css')}}" />
      <!-- color css -->
      <link rel="stylesheet" href="{{asset('userAssets/css/colors.css')}}" />
      <!-- select bootstrap -->
      <link rel="stylesheet" href="{{asset('userAssets/css/bootstrap-select.css')}}" />
      <!-- scrollbar css -->
      <link rel="stylesheet" href="{{asset('userAssets/css/perfect-scrollbar.css')}}" />
      <!-- custom css -->
      <link rel="stylesheet" href="{{asset('userAssets/css/custom.css')}}" />
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
   </head>
   <body class="dashboard dashboard_1">
      <div class="full_container">
         <div class="inner_container">
            <!-- Sidebar  -->
            @include('user.layouts.sidebar')
            <!-- end sidebar -->
            <!-- right content -->
            <div id="content">
               <!-- topbar -->
               @include('user.layouts.navbar')
               <!-- end topbar -->
               <!-- dashboard inner -->
               @yield('content')
               <!-- end dashboard inner -->
            </div>
         </div>
      </div>
      <!-- jQuery -->
      <script src="{{asset('userAssets/js/jquery.min.js')}}"></script>
      <script src="{{asset('userAssets/js/popper.min.js')}}"></script>
      <script src="{{asset('userAssets/js/bootstrap.min.js')}}"></script>
      <!-- wow animation -->
      <script src="{{asset('userAssets/js/animate.js')}}"></script>
      <!-- select country -->
      <script src="{{asset('userAssets/js/bootstrap-select.js')}}"></script>
      <!-- owl carousel -->
      <script src="{{asset('userAssets/js/owl.carousel.js')}}"></script> 
      <!-- chart js -->
      <script src="{{asset('userAssets/js/Chart.min.js')}}"></script>
      <script src="{{asset('userAssets/js/Chart.bundle.min.js')}}"></script>
      <script src="{{asset('userAssets/js/utils.js')}}"></script>
      <script src="{{asset('userAssets/js/analyser.js')}}"></script>
      <!-- nice scrollbar -->
      <script src="{{asset('userAssets/js/perfect-scrollbar.min.js')}}"></script>
      <script>
         var ps = new PerfectScrollbar('#sidebar');
      </script>
      <!-- custom js -->
      <script src="{{asset('userAssets/js/chart_custom_style1.js')}}"></script>
      <script src="{{asset('userAssets/js/custom.js')}}"></script>
   </body>
</html>