<!doctype html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> @yield('page_title') | {{ APPNAME }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- <link href="https://bootswatch.com/5/yeti/bootstrap.min.css" rel="stylesheet"> -->
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pace-js@latest/pace-theme-default.min.css">
    <script src="{{BASEURL}}/public/nthajax.js"></script>
    <script src="{{BASEURL}}/public/refid.js"></script>
    <style>
        .disabled_form {
            pointer-events: none;
            opacity: .4;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row d-flex min-vh-100 align-items-center w-100">
            <div class="col-12 col-md-6 d-flex flex-column justify-content-center align-items-center">
                <div class="my-4">
                    <img src="{{BASEURL}}/public/logo.png" class="rounded-circle" height="200px" alt="">
                </div>

            </div>
            <div class="col-12 col-md-6">
                <div>
                    <h1 class="display-6 text-center">@yield('heading')</h1>
                    <p class="lead text-center"> @yield('description') </p>
                </div>
                <div class="card border shadow rounded">
                    <div class="card-body">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>