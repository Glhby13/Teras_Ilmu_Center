<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login TIC</title>
    <link rel="shortcut icon" type="image/png" href="../img/logoTSII.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<style>
    .page-wrapper {
        background-color: #699ce82c;
    }

    .logo {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif
    }
</style>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div
            class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a class="text-nowrap logo-img text-center d-block py-3 w-100">
                                    <img src="../img/logoTSII.png" width="180" alt="">
                                </a>
                                <!-- <p class="text-center"><b>Gabung Dengan Kami!</b></p> -->


                                <form action="{{ route('login') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" value="{{ old('username') }}" name="username" autofocus>
                                        @error('username')
                                        <div style="color: red; font-size: 12px; position: absolute;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                        @error('password')
                                        <div style="color: red; font-size: 12px; position: absolute;">{{ $message }}</div>
                                        @enderror
                                        @if ($errors->has('login'))
                                        <div style="color: red; font-size: 12px; position: absolute;">{{ $errors->first('login') }}</div>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="form-check">

                                        </div>
                                        <!-- <a class="text-primary fw-bold" href="./index.html">Forgot Password ?</a> -->
                                    </div>
                                    <button name="submit" type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Log in</button>
                                </form>


                                <div class="d-flex align-items-center justify-content-center">
                                    <p class="fs-2 mb-0 fw-bold">Baru Menggunakan TIC Q&A?</p>
                                    <a class="text-primary fw-bold ms-2" href="{{ route('register.index') }}">Create an account</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>