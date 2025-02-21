<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NthPHP App')</title>
    <!-- Bootstrap CSS -->
    <link href="@baseurl('/public/assets/bootstrap.min.css')" rel="stylesheet">
    @stack('styles')
    @canonical
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">NthPHP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/docs">Docs</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-5">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto sticky-bottom">
        <div class="container">
            <p>&copy; {{ date('Y') }} NthPHP. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="@baseurl('public/assets/bootstrap.bundle.min.js')"></script>
    @stack('scripts')
</body>
</html>
