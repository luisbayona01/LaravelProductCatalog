<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LaravelProductCatalog</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Inter', sans-serif;
        }
        .nav-link.active {
            font-weight: 600;
            color: #0d6efd !important;
        }
    </style>
</head>
<body>

    <!-- NAVBAR GLOBAL -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">

            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                LaravelProductCatalog
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">

                <ul class="navbar-nav ms-auto" id="menuLinks">

             
                    

                    <li class="nav-item no-auth">
                        <a class="nav-link" href="{{ route('login') }}">Iniciar sesión</a>
                    </li>

                    <li class="nav-item no-auth">
                        <a class="nav-link" href="{{ route('register') }}">Registrarme</a>
                    </li>

                  
                  
                    <li class="nav-item auth-only d-none">
                        <button class="btn btn-outline-danger btn-sm ms-3" onclick="logout()">
                            Cerrar sesión
                        </button>
                    </li>

                </ul>

            </div>
        </div>
    </nav>

    <div>
      
        @yield('content')
          </div>

    <script>
    
        const token = localStorage.getItem("token");

        if (token) {
            document.querySelectorAll(".auth-only").forEach(el => el.classList.remove("d-none"));
            document.querySelectorAll(".no-auth").forEach(el => el.classList.add("d-none"));
        }

        function logout() {
            localStorage.removeItem("token");
            window.location.href = "/";
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>