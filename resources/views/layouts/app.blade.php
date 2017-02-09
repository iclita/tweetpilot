<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('title')

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    @if (Auth::check())
                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    @else
                        <!-- Website Name -->
                        @if (isset($website))
                        <a class="navbar-brand" href="{{ url('/') }}">
                            {{ $website->name }} &mdash; {{ $website->description }}
                        </a>
                        @endif                    
                    @endif
                </div>

                @if (Auth::check())
                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-left">
                        <li class="{{ (request()->is('*/home')) ? 'active' : '' }}"><a href="{{ route('home') }}"><i class="fa fa-tachometer" aria-hidden="true"></i> Dashboard</a></li>
                        <li class="{{ (request()->is('*/websites')) ? 'active' : '' }}"><a href="{{ route('websites.index') }}"><i class="fa fa-globe" aria-hidden="true"></i> Websites</a></li>
                        <li class="{{ (request()->is('*/videos')) ? 'active' : '' }}"><a href="{{ route('videos.index') }}"><i class="fa fa-video-camera" aria-hidden="true"></i> Videos</a></li>
                        <li class="{{ (request()->is('*/links')) ? 'active' : '' }}"><a href="{{ route('links.index') }}"><i class="fa fa-link" aria-hidden="true"></i> Links</a></li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                                <i class="fa fa-power-off" aria-hidden="true"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </div>
                @endif

            </div>
        </nav>

        @yield('content')
    </div>

    <!-- Modals -->
    <div class="modal fade delete-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Delete</h4>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete it?</p>
            <form id="delete-form" method="POST" action="">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
            </form>
          </div>
          <div class="modal-footer">
            <button style="float:left;" type="button" class="btn btn-danger" data-dismiss="modal">No</button>
            <button style="float:right;" type="button" class="btn btn-success delete-button">Yes</button>
          </div>
        </div>
      </div>
    </div>

    @if (Auth::guest() && isset($website))
    <nav class="navbar navbar-default navbar-static-bottom">
        <h4>{{ date('Y') }} &copy; {{ $website->name }}. All rights reserved.</h4>
    </nav>
    @endif

    <!-- Scripts -->
    <script src="/js/app.js"></script>
</body>
</html>
