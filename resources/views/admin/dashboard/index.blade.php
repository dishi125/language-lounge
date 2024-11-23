@extends('admin.layouts.app')
@section('title')
<title>{{ __('pages.dashboard.title') }} &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection
@section('header-content')
<h1>{{__("pages.dashboard.title")}}</h1>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
        <div class="card-icon bg-primary">
            <i class="far fa-user"></i>
        </div>
        <div class="card-wrap">
            <div class="card-header">
            <h4>Total Admin</h4>
            </div>
            <div class="card-body">
            10
            </div>
        </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
        <div class="card-icon bg-danger">
            <i class="far fa-newspaper"></i>
        </div>
        <div class="card-wrap">
            <div class="card-header">
            <h4>News</h4>
            </div>
            <div class="card-body">
            42
            </div>
        </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
        <div class="card-icon bg-warning">
            <i class="far fa-file"></i>
        </div>
        <div class="card-wrap">
            <div class="card-header">
            <h4>Reports</h4>
            </div>
            <div class="card-body">
            1,201
            </div>
        </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
        <div class="card-icon bg-success">
            <i class="fas fa-circle"></i>
        </div>
        <div class="card-wrap">
            <div class="card-header">
            <h4>Online Users</h4>
            </div>
            <div class="card-body">
            47
            </div>
        </div>
        </div>
    </div>
</div>

<div class="row">
    <img src="{{ asset($qr_code) }}" alt="Luke's bar">
</div>
<div class="row mt-2">
    <button type="button" class="btn btn-primary ml-2" id="download_svg_qr_btn" file-url="{{ asset($qr_code) }}">Download svg</button>
    <a href="javascript:void(0);" class="btn-sm mx-1 btn btn-primary copy_link_clipboard" data-link="{{ route('qr-code.deeplink') }}"><i class="fas fa-copy"></i></a>
</div>
@endsection

@section('page-script')
<script type="text/javascript">
    $(document).on('click', '#download_svg_qr_btn', function (){
        var url = $(this).attr('file-url');
        forceDownload2(url,"lukes_bar.svg","svg");
    })

    function forceDownload2(url, filename, type){
        console.log("url: "+url);
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.blob();
            })
            .then(data => {
                var blob;
                // Create a Blob from the fetched data
                if (type === "svg") {
                    blob = new Blob([data], { type: 'image/svg+xml' });
                }
                var tag = document.createElement('a');
                tag.href = URL.createObjectURL(blob);
                tag.download = filename;
                document.body.appendChild(tag);
                tag.click();
                document.body.removeChild(tag);
            })
            .catch(error => {
                console.error("Error fetching image:", error);
            });
    }
</script>
@endsection
