<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }
        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .content {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="content">
            <div class="container-fluid py-5">
                <div class="row d-flex justify-content-center align-items-center">
                    <div class="col-md-9 col-lg-6 col-xl-5">
                        <img src="{{ asset('img/login.png') }}" alt="Register Background" class="img-fluid">
                    </div>
                    <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('register.submit') }}" method="POST">
                            @csrf
                            <div class="divider d-flex align-items-center my-4">
                                <p class="text-center fw-bold mb-0 fs-4">Registro</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Ingrese su nombre">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Ingrese su correo">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" name="birthdate" id="birthdate" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese su contraseña">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Registrarme</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer siempre en la parte inferior -->
        <footer class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
            <div class="text-white mb-3 mb-md-0">
                VisionIA 2025
            </div>
        </footer>
    </div>
</body>
</html>
