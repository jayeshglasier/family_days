<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
 <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>{{ $systemInformation->sys_name ? $systemInformation->sys_name : 'ChoreUp' }}</title>
        <link rel="icon" type="image/ico" href="{{ asset('public/images/logo/family-days-logo.jpg') }}" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <link href="{{ asset('public/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('public/css/metisMenu.min.css') }}" rel="stylesheet">
        <link href="{{ asset('public/css/timeline.css') }}" rel="stylesheet">
        <link href="{{ asset('public/css/startmin.css') }}" rel="stylesheet">
        <link href="{{ asset('public/css/morris.css') }}" rel="stylesheet">
        <link href="{{ asset('public/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('public/css/style.css') }}" rel="stylesheet">
    </head>
    <body>
        <div id="wrapper">
            <!-- Navigation -->
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="background-image: linear-gradient(to left,#DF920B,#FDD247,#DF920B) !important;">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <ul class="nav navbar-nav navbar-left navbar-top-links">
                    <li><a href="#"><i class="fa fa-users" style="color: white;"></i><b style="color: white;"> {{ $systemInformation->sys_name ? $systemInformation->sys_name : 'ChoreUp' }}</b> </a></li>
                </ul>
                <ul class="nav navbar-right navbar-top-links">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw" style="color: white;"></i> <span style="color: white;">{{ Auth::user()->use_full_name }}  <b class="caret"></b></span>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="{{ url('user-profile') }}"><i class="fa fa-user fa-fw"></i> User Profile</a>
                            </li>
                            <li class="divider"></li>
                            <li><a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    </form>
                </nav>