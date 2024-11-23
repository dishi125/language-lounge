<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <!-- This site is optimized with the Yoast SEO Premium plugin v7.8.1 - https://yoast.com/wordpress/plugins/seo/ -->
  <meta name="description" content="Welcome to Laravel Community" />
  <meta property="og:locale" content="en_US" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Laravel Community" />
  <meta name="keywords" content="Laravel Community">
  <meta property="og:description" content="Welcome to Laravel Community:" />
  <meta property="og:site_name" content="Laravel Community" />
  <meta property="og:image" content="{{ asset('images/logo.svg') }}" />
  <meta property="og:image:alt" content="Laravel Community" />
  <link rel="icon" type="image/png" sizes="16x16" href="{!! asset('images/logo.svg') !!}">

  @yield('title')

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <!-- CSS Libraries -->

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('admin/css/components.css') }}">
</head>

<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
              <img src="{{ asset('admin/img/stisla-fill.svg') }}" alt="logo" width="100" class="shadow-light rounded-circle">
            </div>
            @if(session()->has('info'))
            <div class="alert alert-primary">
                {{ session()->get('info') }}
            </div>
            @endif
            @if(session()->has('status'))
            <div class="alert alert-info">
                {{ session()->get('status') }}
            </div>
            @endif
            @yield('content')
            <div class="simple-footer">
              Copyright &copy; Laravel {{ date('Y') }}
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- General JS Scripts -->
  <script src="{{ asset('admin/js/jquery-3.3.1.min.js') }}"></script>
  <script src="{{ asset('admin/js/popper.min.js') }}"></script>
  <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/js/jquery.nicescroll.min.js') }}"></script>
  <script src="{{ asset('admin/js/moment.min.js') }}"></script>
  <script src="{{ asset('admin/js/stisla.js') }}"></script>

  <!-- JS Libraies -->

  <!-- Template JS File -->
  <script src="{{ asset('admin/js/scripts.js') }}"></script>
  <script src="{{ asset('admin/js/custom.js') }}"></script>

  <!-- Page Specific JS File -->
</body>
</html>
