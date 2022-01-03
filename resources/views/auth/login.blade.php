<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Login - ChoreUp</title>
    <link rel="icon" type="image/ico" href="{{ asset('public/images/logo/family-days-logo.jpg') }}" />
    <link href="{{ asset('public/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="{{ asset('public/css/metisMenu.min.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('public/css/startmin.css') }}" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="{{ asset('public/css/font-awesome.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 70%;
        }
        .alert{
            padding:5px !important;
            margin-bottom:10px !important;
        }
        .login-panel {
            margin-top: 0%;
        }
        .btn-primary:hover{
            color: black;
        }
    </style>
</head>

<body style="background-image: url('./public/images/login/login-page-background.jpg');">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4" style="top:50px;">
                <img src="{{ asset('public/images/logo').'/'.$systemInformation->sys_logo }}" align="center" class="center" style="border-radius:50%;margin-bottom: 10px;width: 200px;height: 202px;">
                <div class="login-panel panel panel-default" style="border-top-left-radius: 50px;border-top-right-radius: 50px;border-radius:15px;">
                    <div class="panel-heading">
                        <h3 class="panel-title" style="text-align: center;color: orange;">Sign In</h3>
                    </div>
                    <div class="panel-body">
                        @if(session('error'))
                        <div class="flash-message" style="padding-top: 5px;">
                            <div class="alert alert-danger" style="text-align: center;"> 
                                <span class="error-message"><big>{{ session('error') }}</big></span>
                            </div>
                        </div>
                        @endif
                        <form role="form" method="POST" action="{{ url('user-login') }}">{{ csrf_field() }}
                            <fieldset>
                                <div class="form-group">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email Id" required autofocus>
                                </div>
                                <div class="form-group">
                                    <input id="password" type="password" class="form-control" name="password" required placeholder="********">
                                </div>
                                <div class="checkbox">
                                    <label><input name="remember" type="checkbox" value="Remember Me">Remember Me</label>
                                </div>
                                <button type="submit" class="btn btn-lg btn-primary btn-block" style="background-color: orange;">Login</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
