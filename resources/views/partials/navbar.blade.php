<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content=".">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TIC</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/logoTSII.png') }}" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css') }}">
    <link href="{{ asset('assets/css/navbar.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12/dist/sweetalert2.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body>
    <div class="page">
        <header class="navbar navbar-expand-md navbar-light sticky-top d-print-none">
            <div class="container">
                <h1 class="navbar-brand text-info navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="{{ route(auth()->user()->role . '.home') }}">
                        Teras Ilmu Center
                    </a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item me-2" style="display: flex; justify-content: center;">
                        <div class="btn-items">
                            <!-- Home -->
                            <a href="{{ route(auth()->user()->role . '.home') }}"
                                class="btn-item btn btn-outline-info border-info {{ request()->routeIs(auth()->user()->role . '.home*') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin: 0;">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"></path>
                                </svg>
                            </a>
                            <!-- Kitab -->
                            <a href="{{ route(auth()->user()->role . '.kitab') }}"
                                class="btn-item btn btn-outline-info border-info {{ request()->routeIs(auth()->user()->role . '.kitab') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-book"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin: 0;">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" />
                                    <path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" />
                                    <path d="M3 6l0 13" />
                                    <path d="M12 6l0 13" />
                                    <path d="M21 6l0 13" />
                                </svg>
                            </a>
                            <!-- Materi Video -->
                            <a href="{{ route(auth()->user()->role . '.video') }}"
                                class="btn-item btn btn-outline-info border-info {{ request()->routeIs(auth()->user()->role . '.video') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icon-tabler-movie" style="margin: 0;">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                    <path d="M8 4l0 16" />
                                    <path d="M16 4l0 16" />
                                    <path d="M4 8l4 0" />
                                    <path d="M4 16l4 0" />
                                    <path d="M4 12l16 0" />
                                    <path d="M16 8l4 0" />
                                    <path d="M16 16l4 0" />
                                </svg>
                            </a>
                            <!-- Live Chat -->
                            <a href="{{ route(auth()->user()->role . '.livechat') }}"
                                class="btn-item btn btn-outline-info border-info {{ request()->routeIs(auth()->user()->role . '.livechat') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icon-tabler-message" style="margin: 0;">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M8 9h8" />
                                    <path d="M8 13h6" />
                                    <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="nav-item dropdown d-md-flex me-3">
                        <a id="dropdown" href="#" class="nav-link d-flex lh-1 text-reset p-0"
                            data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm rounded-circle">
                                @if ($data->gambar)
                                <img class="avatar avatar-sm rounded-circle" src="data:image/png;base64,{{ $data->gambar }}" alt=""
                                    style="background-color: #DBE7F9; object-fit: cover;">
                                @else
                                <img class="avatar avatar-sm rounded-circle" src="{{ asset('img/user2.png') }}" alt=""
                                    style="background-color: #DBE7F9; object-fit: cover;">
                                @endif
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="border-radius: 10px">
                            <a class="dropdown-item" href="{{ route(auth()->user()->role . '.profile') }}">
                                Lihat Profile
                            </a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-wrapper">
            <div class="container">
                @yield('home')
                @yield('kitab')
                @yield('video')
                @yield('chat')
                @yield('livechatUstaz')
                @yield('chatUstaz')
                <!-- @yield('hadith')
                @yield('surah')
                @yield('detail')
                @yield('notifikasi') -->
                @yield('profile')
                @yield('viewProfile')
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    sessionStorage.setItem('scrollPosition', window.scrollY);
                });
            });

            const savedScrollPosition = sessionStorage.getItem('scrollPosition');
            if (savedScrollPosition !== null) {
                window.scrollTo(0, parseInt(savedScrollPosition));
                sessionStorage.removeItem('scrollPosition');
            }
        });
    </script>
</body>

</html>