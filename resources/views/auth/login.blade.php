<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <style>
            .divider:after,
            .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
            }
            .h-custom {
            height: calc(100% - 73px);
            }
            @media (max-width: 450px) {
            .h-custom {
            height: 100%;
            }
            }
        </style>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </head>
    <body>
        <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <!--img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp" class="img-fluid" alt="Sample image"-->
                <img src="{{ asset('img/login.png') }}" alt="Login Background" class="img-fluid">

            </div>
            <div class="col-md-8 col-lg-8 col-xl-4 offset-xl-1">
            @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('login.submit') }}" method="POST">
                @csrf                
                <div class="divider d-flex align-items-center my-4 justify-content-lg-start">
                    <p class="text-center fw-bold mb-4" style='font-size:20px !important;'>Iniciar sesión</p>
                </div>

                <!-- Email input -->
                <div class="mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Ingrese su correo">
                </div>
                <!-- Password input -->
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name='password' id="password" class="form-control" placeholder="Ingrese su contraseña">
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <!-- Checkbox -->
                    <div class="form-check mb-0">
                    <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3" />
                    <label class="form-check-label" for="form2Example3">
                       Recordarme
                    </label>
                    </div>
                    <a href="#!" class="text-body">Olvidó su contraseña?</a>
                </div>

                <div class="text-center text-lg-start mt-4 pt-2">
                    <button  type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary">Ingresar</button>
                    <p class="small fw-bold mt-2 pt-1 mb-0">No tiene una cuenta? <a href="{{ route('register.submit') }}"
                        class="link-danger">Registrarme</a></p>
                </div>

                </form>
            </div>
            </div>
        </div>
        <div
            class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
            <!-- Copyright -->
            <div class="text-white mb-3 mb-md-0">
            VisionIA 2025
            </div>
            <!-- Copyright -->

            <!-- Right -->
            <div>
            <a href="#!" class="text-white me-4">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#!" class="text-white me-4">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="#!" class="text-white me-4">
                <i class="fab fa-google"></i>
            </a>
            <a href="#!" class="text-white">
                <i class="fab fa-linkedin-in"></i>
            </a>
            </div>
            <!-- Right -->
        </div>
        </section>
    </body>
</html>