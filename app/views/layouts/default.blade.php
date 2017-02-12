<?
$currentUser = isset($currentUser) ? $currentUser : null;
$rightSidebarExists = false;
?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>Northgate Digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <!-- BEGIN PLUGIN CSS -->
    <link href="/AdminPanel/assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="/AdminPanel/assets/plugins/jquery-slider/css/jquery.sidr.light.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="/AdminPanel/assets/plugins/jquery-datatable/css/jquery.dataTables.css" rel="stylesheet" type="text/css"/>
    <link href="/AdminPanel/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
    <!-- END PLUGIN CSS -->
    <!-- BEGIN CORE CSS FRAMEWORK -->
    <link href="/AdminPanel/assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/AdminPanel/assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
    <link href="/AdminPanel/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <link href="/AdminPanel/assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
    <!-- END CORE CSS FRAMEWORK -->
    <!-- BEGIN CSS TEMPLATE -->
    <link href="/AdminPanel/assets/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="/AdminPanel/assets/css/responsive.css" rel="stylesheet" type="text/css"/>
    <link href="/AdminPanel/assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>
    <link href="/AdminPanel/assets/css/print.css" rel="stylesheet" type="text/css"/>
        <!-- END CSS TEMPLATE -->
    <script src="/AdminPanel/assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
    @yield('scripts')


</head>
<!-- BEGIN BODY -->
<body class="">
    <!-- BEGIN HEADER -->
    <div class="header navbar navbar-inverse "> 
        <!-- BEGIN TOP NAVIGATION BAR -->
        <div class="navbar-inner">
            <div class="header-seperation"> 
                <ul class="nav pull-left notifcation-center" id="main-menu-toggle-wrapper" style="display:none">	
                    <li class="dropdown"> <a id="main-menu-toggle" href="#main-menu"  class="" > <div class="iconset top-menu-toggle-white"></div> </a> </li>		 
                </ul>
                <!-- BEGIN LOGO -->	
                <a href="index.html"><img src="/images/admin/logo.png" class="logo" alt=""  data-src="/images/admin/logo.png" data-src-retina="/images/admin/logo.png"/></a>
                <!-- END LOGO --> 
            </div>
            <!-- END RESPONSIVE MENU TOGGLER --> 
            <? if (Auth::check()) { ?>

            <div class="header-quick-nav" > 
                <!-- BEGIN TOP NAVIGATION MENU -->
                <div class="pull-left"> 
                    <ul class="nav quick-section">
                        <li class="quicklinks"> <a href="#" class="" id="layout-condensed-toggle" >
                            <div class="iconset top-menu-toggle-dark"></div>
                        </a> </li>
                    </ul>
                    <ul class="nav quick-section">
                        <li class="m-r-10 input-prepend inside search-form no-boarder">
                            <span class="add-on"> <span class="iconset top-search"></span></span>
                            <input name="" type="text"  class="no-boarder " placeholder="Search for..." style="width:250px;">
                        </li>
                    </ul>
                </div>
                <!-- END TOP NAVIGATION MENU -->
                <!-- BEGIN CHAT TOGGLER -->
                <div class="pull-right" style="text-align:right"> 
                    <div class="chat-toggler">	
                        <a href="#" class="dropdown-toggle" id="my-task-list" data-placement="bottom"  data-content='' data-toggle="dropdown" data-original-title="Notifications">
                            <div class="user-details"> 
                                <div class="username">
                                            <!-- Uncomment if you want to have user alerts
                                            <span class="badge badge-important">3</span> 
                                        -->
				    @if (Auth::check() && isset(Auth::user()->employee))
                                        {{Auth::user()->employee->profile->first_name}} <span class="bold">{{Auth::user()->employee->profile->last_name}}</span>									
                                        @endif
                                    </div>						
                                </div> 
                            </a>	
                        </div>
                        <ul class="nav quick-section" style="float:right">
                            <li class="quicklinks"> 
                                <a data-toggle="dropdown" class="dropdown-toggle  pull-right " href="#" id="user-options">						
                                    <div class="iconset top-settings-dark "></div> 	
                                </a>
                                <ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="user-options">                                    
                                    <li><a href="/logout"><i class="fa fa-power-off"></i>&nbsp;&nbsp;Log Out</a></li>
                                </ul>
                            </li> 
                            <? if ($rightSidebarExists) { ?>
                            <li class="quicklinks"> <span class="h-seperate"></span></li> 
                            <li class="quicklinks"> 	
                                <a id="chat-menu-toggle" href="#sidr" class="chat-menu-toggle" >
                                    <div class="iconset top-chat-dark ">
                                    </div>
                                </a> 
                            </li> 
                            <? } else { ?>
                            <!-- For a right sidebar, add the file Elements/AdminPanel/right-sidebar.ctp -->
                            <? } ?>
                        </ul>
                    </div>
                    <!-- END CHAT TOGGLER -->
                </div> 
                <!-- END TOP NAVIGATION MENU --> 
                <? 
            } ?>
        </div>
        <!-- END TOP NAVIGATION BAR --> 
    </div>

    <!-- END HEADER --> 
    <!-- BEGIN CONTAINER -->
    <div class="page-container row"> 
        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar" id="main-menu">
            <!-- BEGIN MINI-PROFILE -->
            <div class="page-sidebar-wrapper" id="main-menu-wrapper">
                <!-- END MINI-PROFILE -->
                <!-- BEGIN SIDEBAR MENU -->
                @yield('nav-top')
                @include('partials.left-nav.main')
                @include('partials.left-nav.timesheets')
                @include('partials.left-nav.admin')
                @yield('nav-bottom')
                <!-- LEFT ACTIONS PARTIAL HERE -->

                <div class="clearfix"></div>
                <!-- END SIDEBAR MENU -->
            </div>
        </div>
<!--        <a href="#" class="scrollup">Scroll</a>-->
        <div class="footer-widget">		
            <div class="pull-right">
                <a href="/logout" title="Logout"><i class="fa fa-power-off"></i></a>
            </div>
        </div>   
        <!-- END SIDEBAR --> 
        <!-- BEGIN PAGE CONTAINER-->
        <div class="page-content">
            <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <div id="portlet-config" class="modal hide">
                <div class="modal-header">
                    <button data-dismiss="modal" class="close" type="button"></button>
                    <h3>Widget Settings</h3>
                </div>
                <div class="modal-body"> Widget settings form goes here </div>
            </div>
            <div class="clearfix"></div>
            <div class="content">
                @yield('content')
            </div>
            <!-- END PAGE -->
        </div>
    </div>
    <!-- BEGIN RIGHT SIDEBAR --> 
    <?
/*FIXME:        if ($rightSidebarExists) {
            echo $this->element("AdminPanel/right-sidebar");
        }
        */
        ?>
        <!-- END RIGHT SIDEBAR --> 
        <!-- END CONTAINER -->
        <!-- BEGIN CORE JS FRAMEWORK-->
        <script src="/AdminPanel/assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
        <script src="/AdminPanel/assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="/AdminPanel/assets/plugins/breakpoints.js" type="text/javascript"></script>
        <script src="/AdminPanel/assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
        <script src="/AdminPanel/assets/plugins/jquery-block-ui/jqueryblockui.js" type="text/javascript"></script> 
        <!-- END CORE JS FRAMEWORK -->
        <!-- BEGIN PAGE LEVEL JS -->
        <script src="/AdminPanel/assets/plugins/pace/pace.min.js" type="text/javascript"></script>
        <script src="/AdminPanel/assets/plugins/jquery-slider/jquery.sidr.min.js" type="text/javascript"></script>
        <script src="/AdminPanel/assets/plugins/jquery-numberAnimate/jquery.animateNumbers.js" type="text/javascript"></script>
        <script src="/AdminPanel/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script> 
        <script src="/AdminPanel/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
        <script src="/AdminPanel/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- PAGE JS -->
        <script src="/AdminPanel/assets/js/tabs_accordian.js" type="text/javascript"></script>
        <!-- BEGIN CORE TEMPLATE JS -->
        <script src="/AdminPanel/assets/js/core.js" type="text/javascript"></script>
        <script src="/AdminPanel/assets/js/chat.js" type="text/javascript"></script> 
        <script src="/AdminPanel/assets/js/demo.js" type="text/javascript"></script>
        <!-- END CORE TEMPLATE JS -->
    </body>
    </html>



