<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Organic Produce Finder</title>
    <meta content="Admin Dashboard" name="description" />
    <meta content="Mannatthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet">

    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
</head>


<body class="fixed-left">

    <!-- Loader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner"></div>
        </div>
    </div>

    <!-- Begin page -->
    <div id="wrapper">

        <!-- ========== Left Sidebar Start ========== -->
        <div class="left side-menu">
            <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
                <i class="ion-close"></i>
            </button>

            <!-- LOGO -->
            <div class="topbar-left">
                <div class="text-center bg-logo">
                    <a href="index.html" class="logo logo-admin"><img src="{{ asset('assets/images/logo-mini.png') }}" height="100" alt="logo"></a>
                    <!-- <a href="index.html" class="logo"><img src="assets/images/logo.png" height="24" alt="logo"></a> -->
                </div>
            </div>
            <div class="sidebar-user">
                <img src="{{ asset('images/user/'.auth()->user()->image) }}" alt="user" class="rounded-circle img-thumbnail mb-1">
                <h6 class="">{{ auth()->user()->name }}</h6>
                <p class=" online-icon text-dark"><i class="mdi mdi-record text-success"></i>online</p>
                <ul class="list-unstyled list-inline mb-0 mt-2">
                    {{-- <li class="list-inline-item">
                        <a href="#" class="" data-toggle="tooltip" data-placement="top" title="Profile"><i class="dripicons-user text-purple"></i></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#" class="" data-toggle="tooltip" data-placement="top" title="Settings"><i class="dripicons-gear text-dark"></i></a>
                    </li> --}}
                    <li class="list-inline-item">
                        <a href="{{ route('logout') }}" class="" data-toggle="tooltip" data-placement="top" title="Log out"><i class="dripicons-power text-danger"></i></a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-inner slimscrollleft">

                <div id="sidebar-menu">
                    <ul>
                        <li class="menu-title">Main</li>

                        {{-- <li>
                            <a href="index.html" class="waves-effect">
                                <i class="dripicons-device-desktop"></i>
                                <span> Dashboard <span class="badge badge-pill badge-primary float-right">7</span></span>
                            </a>
                        </li> --}}

                        <li>
                            <a href="{{ route('admin.category.index') }}" class="waves-effect 
                            {{ Route::is('admin.category.index') ? 'active' : ''  }} 
                            {{ Route::is('admin.category.create') ? 'active' : ''  }} 
                            {{ Route::is('admin.category.edit') ? 'active' : ''  }}">
                                <i class="fas fa-solid fa-list"></i>
                                <span> Categories </span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.payment.index') }}" class="waves-effect 
                            {{ Route::is('admin.payment.index') ? 'active' : ''  }} 
                            {{ Route::is('admin.payment.create') ? 'active' : ''  }} 
                            {{ Route::is('admin.payment.edit') ? 'active' : ''  }}">
                                <i class="fas fa-money-check-alt"></i>
                                <span> Payments </span>
                            </a>
                        </li>
                        
                        <li>
                            <a href="{{ route('admin.delivery-option.index') }}" class="waves-effect 
                            {{ Route::is('admin.delivery-option.index') ? 'active' : ''  }} 
                            {{ Route::is('admin.delivery-option.create') ? 'active' : ''  }} 
                            {{ Route::is('admin.delivery-option.edit') ? 'active' : ''  }}">
                                {{-- <i class="fas fa-money-check-alt"></i> --}}
                                <i class="fas fa-solid fa-truck"></i>
                                <span> Delivery Options </span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.user.index') }}" class="waves-effect 
                            {{ Route::is('admin.user.index') ? 'active' : ''  }} 
                            {{ Route::is('admin.user.create') ? 'active' : ''  }} 
                            {{ Route::is('admin.user.edit') ? 'active' : ''  }}">
                                <i class="fas fa-user-alt"></i>
                                <span> Users </span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.farm.index') }}" class="waves-effect 
                            {{ Route::is('admin.farm.index') ? 'active' : ''  }} 
                            {{ Route::is('admin.farm.create') ? 'active' : ''  }} 
                            {{ Route::is('admin.farm.edit') ? 'active' : ''  }}">
                                <i class="fas fa-solid fa-tractor"></i>
                                <span>Farms </span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.service.index') }}" class="waves-effect 
                            {{ Route::is('admin.service.index') ? 'active' : ''  }} 
                            {{ Route::is('admin.service.create') ? 'active' : ''  }} 
                            {{ Route::is('admin.service.edit') ? 'active' : ''  }}">
                            <i class="fab fa-servicestack"></i>
                                <span>Services </span>
                            </a>
                        </li>

                        {{-- <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-jewel"></i> <span> UI Elements </span> <span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="list-unstyled">
                                <li><a href="ui-alerts.html">Alerts</a></li>
                            </ul>
                        </li> --}}


                    </ul>
                </div>
                <div class="clearfix"></div>
            </div> <!-- end sidebarinner -->
        </div>
        <!-- Left Sidebar End -->

        <!-- Start right Content here -->

        <div class="content-page">
            <!-- Start content -->
            <div class="content">

                <!-- Top Bar Start -->
                <div class="topbar">

                    <nav class="navbar-custom">

                        <ul class="list-inline float-right mb-0">

                            <li class="list-inline-item dropdown notification-list">
                                <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <img src="{{ asset('images/user/'.auth()->user()->image) }}" alt="user" class="rounded-circle" height="100">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                    <!-- item-->
                                    <div class="dropdown-item noti-title">
                                        <h5>Welcome</h5>
                                    </div>
                                    {{-- <a class="dropdown-item" href="#"><i class="mdi mdi-account-circle m-r-5 text-muted"></i> Profile</a>
                                    <a class="dropdown-item" href="#"><i class="mdi mdi-wallet m-r-5 text-muted"></i> My Wallet</a>
                                    <a class="dropdown-item" href="#"><span class="badge badge-success float-right">5</span><i class="mdi mdi-settings m-r-5 text-muted"></i> Settings</a>
                                    <a class="dropdown-item" href="#"><i class="mdi mdi-lock-open-outline m-r-5 text-muted"></i> Lock screen</a>
                                    <div class="dropdown-divider"></div> --}}
                                    <a class="dropdown-item" href="{{ route('logout') }}"><i class="mdi mdi-logout m-r-5 text-muted"></i> Logout</a>
                                    
                                </div>
                            </li>
                        </ul>

                        {{-- <ul class="list-inline menu-left mb-0">
                            <li class="float-left">
                                <button class="button-menu-mobile open-left waves-light waves-effect">
                                    <i class="mdi mdi-menu"></i>
                                </button>
                            </li>
                            <li class="hide-phone app-search">
                                <form role="search" class="">
                                    <input type="text" placeholder="Search..." class="form-control">
                                    <a href=""><i class="fas fa-search"></i></a>
                                </form>
                            </li>
                        </ul> --}}

                        <div class="clearfix"></div>
                    </nav>
                </div>
                <!-- Top Bar End -->
                <div class="page-content-wrapper ">

                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </div>

            </div> <!-- content -->

            <footer class="footer">
                © 2025 Copyright by Koderspedia.
            </footer>

        </div>
        <!-- End Right content here -->

    </div>
    <!-- END wrapper -->


    <!-- jQuery  -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/modernizr.min.js') }}"></script>
    <script src="{{ asset('assets/js/detect.js') }}"></script>
    <script src="{{ asset('assets/js/fastclick.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.blockUI.js') }}"></script>
    <script src="{{ asset('assets/js/waves.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.nicescroll.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.scrollTo.min.js') }}"></script>

    {{-- <script src="{{ asset('assets/plugins/chart.js/chart.min.js') }}"></script> --}}
    <script src="{{ asset('assets/pages/dashboard.js') }}"></script>

    <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/pages/dropify.init.js') }}"></script>

    <!-- Required datatable js -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Buttons examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons.colVis.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ asset('assets/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/pages/form-advanced.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @stack('scripts')

</body>
</html>
