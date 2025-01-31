<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script src="https://unpkg.com/htmx.org@2.0.4"></script>
    @canonical
    <style>
        nav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 0.3rem;
            margin: 0 auto;
        }
    </style>
</head>

<body hx-boost="true">
    <nav>
        <h1>
            @base('http://localhost:9000')
            <a href="@b('htmx/')" hx-target="#content">NthPHP + HTMX</a>
        </h1>
        <ul>
            <li><a href="@b('htmx/')" hx-target="#content">Home</a></li>
            <li><a href="@b('htmx/about')" hx-target="#content">About</a></li>
        </ul>

        <div>
            Loaded at {{date('Y-m-d H:i:s')}}
        </div>
    </nav>

    <div id="content">
        @yield('content')
    </div>
</body>

</html>