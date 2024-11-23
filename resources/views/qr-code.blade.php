<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}">
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .center-image {
            margin: auto;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="row">
        <img src="{{ asset($qr_code) }}" alt="Luke's bar" class="center-image">
    </div>
    <div class="row center-button">
        <button type="button" class="btn btn-primary" id="download_svg_qr_btn" file-url="{{ asset($qr_code) }}">Download</button>
    </div>
</div>

<script src="{{ asset('admin/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('admin/js/popper.min.js') }}"></script>
<script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('admin/js/jquery.nicescroll.min.js') }}"></script>
<script src="{{ asset('admin/js/dataTables.bootstrap4.min.js') }}"></script>
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

</body>
</html>
