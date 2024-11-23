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

        <meta name="csrf-token" content="{{ csrf_token() }}" />
        @yield('title')

        <!-- General CSS Files -->
        <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

        <link href="{{ asset('css/iziToast.css') }}" rel="stylesheet">

        <!-- CSS Libraries -->
        @yield('styles')
        <!-- Template CSS -->
        <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/css/dev.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/css/components.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/css/dataTables.bootstrap4.css') }}">
        {{-- <link rel="stylesheet" href="{{ asset('admin/css/datatables.min.css') }}"> --}}
    </head>
    <body>
        <div id="app">
            <div class="main-wrapper">
                <div class="loading" id="main-loader" style="display: none;">{{ Lang::get('admin_message.loading') }}&#8230;</div>
            @include('admin.layouts.partials.navbar')
            @include('admin.layouts.partials.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                        <div class="section-header">
                            @yield('header-content')
                        </div>
                        <div class="section-body">
                            @yield('content')
                        </div>
                        @yield('models')
                </section>
            </div>
            @include('admin.layouts.partials.footer')

            </div>
        </div>

        <!-- General JS Scripts -->
        <script src="{{ asset('admin/js/jquery-3.3.1.min.js') }}"></script>
        <script src="{{ asset('admin/js/popper.min.js') }}"></script>
        <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('admin/js/jquery.nicescroll.min.js') }}"></script>
        <script src="{{ asset('admin/js/moment.min.js') }}"></script>
        <script src="{{ asset('admin/js/stisla.js') }}"></script>
        <script src="{{ asset('admin/izitoast/js/iziToast.js') }}"></script>
        <script src="{{ asset('admin/js/sweetalert2.js') }}"></script>
        <script src="{{ asset('admin/js/datatables.min.js') }}"></script>
        <script src="{{ asset('admin/js/dataTables.bootstrap4.min.js') }}"></script>
        <!-- JS Libraies -->
        @yield('scripts')

        <!-- Template JS File -->
        <script src="{{ asset('admin/js/scripts.js') }}"></script>
        <script src="{{ asset('admin/js/custom.js') }}"></script>

        @include('vendor.lara-izitoast.toast')
        <script>
        $('.iziToast-message').removeAttr('style');
        var baseUrl = "{{ url('/') }}";
        var laravel = {
            startLoader: function() {
                $("#main-loader").show();
                $("#main-loader").addClass("loading");
            },
            stopLoader: function() {
                $("#main-loader").hide();
                $("#main-loader").removeClass("loading");
            },
            success : function(messagestr){
                iziToast.success({
                    title: '',
                    message: messagestr,
                    position: 'topRight',
                    progressBar: false,
                    timeout: 2500,
                });
            },
            error : function(messagestr){
                iziToast.error({
                    title: '',
                    message: messagestr,
                    position: 'topRight',
                    progressBar: false,
                    timeout: 2500,
                });
            },
            startSubmitLoader : function(){
                $(".submit-loader").show();
            },
            stopSubmitLoader : function(){
                $(".submit-loader").hide();
            }
        }

        /* Set common message start */
        var commonMessages = {
            cancel : "{{ __('general.cancel') }}",
            deleteYes : "{{ __('general.delete_yes') }}",
            areYouSure : "{{ __('general.are_you_sure') }}",
            deleted : "{{ __('general.deleted') }}",
            error : "{{ __('general.error') }}",
            youWantToDeleteThisRecord : "{{ __('general.you_want_to_delete_this_record') }}",
            youWantToDeleteThisVersion : "{{ __('general.you_want_to_delete_this_version') }}",
        }
        /* Set common message end */
        </script>
        <script src="{{ asset('admin/js/common.js') }}"></script>

        <!-- Laravel Javascript Validation -->
        {{-- <script type="text/javascript" src="{{ asset('../vendor/jsvalidation/js/jsvalidation.js')}}"></script> --}}

        <!-- Page Specific JS File -->
        @yield('page-script')
    </body>
</html>
