@extends('partials.navbar')
@section('viewProfile')

<style>
    .avatar-preview {
        width: 88px;
        /* Ukuran kotak */
        height: 88px;
        /* Ukuran kotak */
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 50%;
        /* Membuat gambar bulat */
        background-color: #DBE7F9;
    }

    .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* Menjaga gambar tetap dalam rasio kotak */
    }

    .deskripsi-container {
        position: relative;
        cursor: pointer;
        padding: 10px;
        width: fit-content;
        /* Sesuaikan padding sesuai kebutuhan */
    }

    .deskripsi-text {
        white-space: pre-wrap;
    }

    .deskripsi-container:hover .deskripsi-text {
        text-decoration: underline;
        /* Opsional, jika ingin menambahkan efek underline saat hover */
    }

    /* Atur tinggi maksimum dan scrollbar untuk dropdown */
    #suggestions {
        max-height: 200px;
        /* Atur tinggi maksimal sesuai kebutuhan */
        overflow-y: auto;
        /* Tambahkan scroll vertikal jika konten melebihi tinggi */
        border-radius: 4px;
        background-color: #fff;
        position: absolute;
        z-index: 1000;
        width: 40%;
    }

    /* Atur tinggi maksimum dan scrollbar untuk dropdown */
    #suggestions1 {
        max-height: 200px;
        /* Atur tinggi maksimal sesuai kebutuhan */
        overflow-y: auto;
        /* Tambahkan scroll vertikal jika konten melebihi tinggi */
        border-radius: 4px;
        background-color: #fff;
        position: absolute;
        z-index: 1000;
        width: 40%;
    }

    .list-group-item {
        cursor: pointer;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }

    .swal2-success-circular-line-right {
        background-color: unset !important;
    }

    .swal2-success-circular-line-left {
        background-color: unset !important;
    }

    .swal2-success-fix {
        background-color: unset !important;
    }
</style>

@if(session('success'))
<div id="success-message" data-message="{{ session('success') }}"></div>
@endif

@if($errors->any())
<div id="error-messages" data-errors='@json($errors->all())'></div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var successMessage = document.getElementById('success-message');
        var errorMessages = document.getElementById('error-messages');

        if (successMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: successMessage.getAttribute('data-message'),
                confirmButtonText: 'OK'
            });
        }

        if (errorMessages) {
            var errors = JSON.parse(errorMessages.getAttribute('data-errors'));
            errors.forEach(function(error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error,
                    confirmButtonText: 'OK'
                });
            });
        }
    });
</script>

<div>
    <div class="mt-4">
        <div class="row">
            <div class="col-6">
                <div class="row">
                    <div class="col-3">
                        <span>
                            <!-- Tampilkan gambar dari database atau gambar default jika tidak ada gambar -->
                            @if ($datauser->gambar)
                            <img class="avatar avatar-xl rounded-circle"
                                src="data:image/png;base64,{{ $datauser->gambar }}"
                                alt=""
                                style="background-color: #DBE7F9; object-fit: cover;">
                            @else
                            <img class="avatar avatar-xl rounded-circle"
                                src="{{ asset('img/user2.png') }}"
                                alt=""
                                style="background-color: #DBE7F9;">
                            @endif
                        </span>
                    </div>
                    <div class="col-9">
                        <div class="row">
                            <div class="col-12">
                                <b style="font-size: 27px;">
                                    <p style="margin-bottom: 0;">{{ $datauser->nama }}</p>
                                </b>
                                <b style="font-size: 20px">
                                    <p>{{ $datauser->email }}</p>
                                </b>

                                @if (auth()->check())
                                @php
                                $isFollowing = \App\Models\Follow::where('follower', auth()->user()->username)
                                ->where('following', $datauser->username)
                                ->exists();
                                @endphp

                                @if ($datauser->username != auth()->user()->username)

                                @if ($isFollowing)
                                <!-- Jika sedang Following -->
                                <form action="{{ route(auth()->user()->role . '.unfollow') }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="follower" value="{{ auth()->user()->username }}">
                                    <input type="hidden" name="following" value="{{ $datauser->username }}">
                                    <button type="button" class="btn btn-info btn-pill" onclick="event.preventDefault(); this.closest('form').submit();">
                                        Unfollow
                                    </button>
                                </form>
                                @else
                                <!-- Jika belum Follow -->
                                <form action="{{ route(auth()->user()->role . '.follow') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="follower" value="{{ auth()->user()->username }}">
                                    <input type="hidden" name="following" value="{{ $datauser->username }}">
                                    <button type="button" class="btn btn-primary btn-pill" onclick="event.preventDefault(); this.closest('form').submit();">
                                        Follow
                                    </button>
                                </form>

                                @endif
                                @endif
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 ms-1">
                    @if ($datauser->deskripsi)
                    <!-- Jika deskripsi tidak null, tampilkan isi deskripsi dan tombol hapus -->
                    <div class="deskripsi-container">
                        <div class="deskripsi-text">{{ $datauser->deskripsi }}</div>
                    </div>
                    <br></br>
                    @else
                    <!-- Jika deskripsi null, tampilkan teks untuk menulis deskripsi -->
                    <a href="#" class="text-secondary">
                    </a>
                    @endif
                </div>


                <div class="mt-4">
                    <div class="row text-center">
                        <div class="col-3">
                            <h3 href="" class="text-danger text-secondary">Answers</h3>
                        </div>
                        <div class="col-3">
                            <h3 href="" class="text-danger text-secondary">Questions</h3>
                        </div>
                        <div class="col-3">
                            <h3 href="" class="text-danger text-secondary">Followers</h3>
                        </div>
                        <div class="col-3">
                            <h3 href="" class="text-danger text-secondary">Following</h3>
                        </div>
                    </div>
                </div>
                <hr class="mt-3 mb-3">
                <div class="mt-4">
                    <div class="row text-center">
                        <div class="col-3">
                            <h3 href="#" class="text-danger text-secondary">{{ $answers ?? 0 }}</h3>
                        </div>
                        <div class="col-3">
                            <h3 href="#" class="text-danger text-secondary">{{ $questions ?? 0 }}</h3>
                        </div>
                        <div class="col-3">
                            <h3 href="#" class="text-danger text-secondary">{{ $followers ?? 0 }}</h3>
                        </div>
                        <div class="col-3">
                            <h3 href="#" class="text-danger text-secondary">{{ $followings ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-2">

            </div>
            <div class="col-4">
                <div>
                    Credentials & Highlights
                    <hr class="mt-3 mb-3">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-1 icon icon-tabler icon-tabler-briefcase"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z">
                            </path>
                            <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"></path>
                            <path d="M12 12l0 .01"></path>
                            <path d="M3 13a20 20 0 0 0 18 0"></path>
                        </svg>
                        @if ($datauser->pekerjaan)
                        Pekerjaan: {{ $datauser->pekerjaan }}
                        @else
                        None
                        @endif
                    </div>
                    <div class="mt-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-1 icon icon-tabler icon-tabler-school"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6"></path>
                            <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4"></path>
                        </svg>
                        @if ($datauser->pendidikan)
                        Pendidikan: {{ $datauser->pendidikan }}
                        @else
                        None
                        @endif
                    </div>
                    <div class="mt-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-1 icon icon-tabler icon-tabler-map-pin"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                            <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z">
                            </path>
                        </svg>
                        Alamat: {{ $datauser->alamat }}
                    </div>
                    <div class="mt-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-1 icon icon-tabler icon-tabler-calendar"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z">
                            </path>
                            <path d="M16 3v4"></path>
                            <path d="M8 3v4"></path>
                            <path d="M4 11h16"></path>
                            <path d="M11 15h1"></path>
                            <path d="M12 15v3"></path>
                        </svg>
                        Joined: {{ $created_at_formatted }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection