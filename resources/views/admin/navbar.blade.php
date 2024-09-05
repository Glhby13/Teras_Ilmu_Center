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
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

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
                    <a>
                        Teras Ilmu Center || Admin
                    </a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item me-2" style="display: flex; justify-content: center;">
                        <div class="btn-items">
                            <!-- Home -->
                            <!-- <a href="{{ route('admin.home') }}"
                                class="btn-item btn btn-outline-info border-info {{ request()->is('admin/home*') ? 'active' : '' }}"
                                style="width: 60px;">
                                Home
                            </a> -->
                            <!-- @if($admin->username === 'superAdmin') -->
                            <!-- Admin -->
                            <!-- <a href="#"
                                class="btn-item btn btn-outline-info border-info {{ request()->is('admin/admin*') ? 'active' : '' }}"
                                style="width: 60px;">
                                Admin
                            </a> -->
                            <!-- @endif -->
                            <!-- Ustaz -->
                            <a href="{{ route('admin.ustaz') }}"
                                class="btn-item btn btn-outline-info border-info {{ request()->is('admin/ustaz*') ? 'active' : '' }}"
                                style="width: 60px;">
                                Ustaz
                            </a>
                            <!-- Murid -->
                            <a href="{{ route('admin.murid') }}"
                                class="btn-item btn btn-outline-info border-info {{ request()->is('admin/murid*') ? 'active' : '' }}"
                                style="width: 60px;">
                                Murid
                            </a>
                            <!-- Umum -->
                            <a href="{{ route('admin.umum') }}"
                                class="btn-item btn btn-outline-info border-info {{ request()->is('admin/umum*') ? 'active' : '' }}"
                                style="width: 60px;">
                                Umum
                            </a>
                            <!-- Report -->
                            <a href="{{ route('admin.report') }}"
                                class="btn-item btn btn-outline-info border-info {{ request()->is('admin/report*') ? 'active' : '' }}"
                                style="width: 60px;">
                                Report
                            </a>
                            <!-- Iklan -->
                            <a href="{{ route('admin.iklan') }}"
                                class="btn-item btn btn-outline-info border-info {{ request()->is('admin/iklan*') ? 'active' : '' }}"
                                style="width: 60px;">
                                Iklan
                            </a>
                            <!-- Log Out -->
                            <a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"
                                class="btn-item btn btn-outline-info border-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-book"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin: 0;">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                    <path d="M9 12h12l-3 -3" />
                                    <path d="M18 15l3 -3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-wrapper">
            <div class="container">
                @yield('home')
                @yield('ustaz')
                @yield('ustazTrash')
                @yield('murid')
                @yield('muridTrash')
                @yield('umum')
                @yield('umumTrash')
                @yield('report')
                @yield('iklan')
                @yield('iklanTrash')
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