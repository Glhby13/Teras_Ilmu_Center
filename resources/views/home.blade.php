@extends('partials.navbar')
@section('home')
<link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">

<style>
    /* Style untuk form-control saat hover */
    .form-control:hover {
        border-color: #007bff;
        /* Warna border saat hover */
        box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
        cursor: pointer;
        /* Bayangan border saat hover */
    }

    /* Style untuk SVG saat hover */
    svg:hover {
        stroke: #007bff;
        /* Warna stroke SVG saat hover */
        cursor: pointer;
        /* Mengubah cursor menjadi pointer */
        color: #577bff;
    }

    .success-floating {
        position: fixed;
        transform: translate(-50%, -50%);
        z-index: 1050;
        width: 35vh;
        top: 50%;
        left: 50%;
        background-color: #e3f9ff;
        border-radius: 10px;
    }

    .error-floating {
        position: fixed;
        transform: translate(-50%, -50%);
        z-index: 1050;
        width: 35vh;
        top: 50%;
        left: 50%;
        background-color: #ffe3e3;
        border-radius: 10px;
    }

    .kategori .active {
        font-weight: bold;
        color: blue;
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

    html {
        scroll-behavior: smooth;
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

<div class="container mt-3">
    <div class="row">
        <div class="col-lg-2 kategori">
            <div class="d-flex justify-content-between align-items-center" style="margin-top: 1vh!important;">
                <h3>Kategori</h3>
                @if($selectedKategori)
                <a href="{{ route(auth()->user()->role . '.homekategori') }}" class="badge bg-danger ms-2">Reset</a>
                @endif
            </div>

            <ul class="list-unstyled">
                @foreach(['Sholat', 'Nikah', 'Puasa', 'Zakat'] as $kategori)
                <li style="padding:2px;">
                    <a href="{{ route(auth()->user()->role . '.homekategori', $kategori) }}" class="{{ $selectedKategori === $kategori ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-category">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 4h6v6h-6z" />
                            <path d="M14 4h6v6h-6z" />
                            <path d="M4 14h6v6h-6z" />
                            <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                        </svg>
                        {{ $kategori }}
                    </a>
                </li>
                @endforeach
            </ul>

            <div class="d-flex justify-content-between align-items-center" style="margin-top: 1vh!important;">
                <h3>Urutkan</h3>
                @if(request('sort'))
                <a href="{{ route(auth()->user()->role . '.homekategori', ['kategori' => $selectedKategori]) }}" class="badge bg-danger ms-2">Reset</a>
                @endif
            </div>
            <ul class="list-unstyled">
                @foreach([
                'newest' => 'Terbaru',
                'upvotes' => 'Up Votes Terbanyak',
                'downvotes' => 'Down Votes Terbanyak',
                'oldest' => 'Terlama'
                ] as $sortKey => $sortLabel)
                <li style="padding:2px;">
                    <a href="{{ route(auth()->user()->role . '.homekategori', ['sort' => $sortKey, 'kategori' => $selectedKategori]) }}" class="{{ request('sort') === $sortKey ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-category">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 4h6v6h-6z" />
                            <path d="M14 4h6v6h-6z" />
                            <path d="M4 14h6v6h-6z" />
                            <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                        </svg>
                        {{ $sortLabel }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>


        <div class="col-lg-8 content card-post col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <input type="text" class="form-control form-control-rounded"
                                placeholder="What do you want to ask or share?" type="button" data-bs-toggle="modal"
                                data-bs-target="#questionsModal">
                            <div class="row mt-3" style="justify-content: center">
                                <div class="col-4">
                                    <button class="btn btn-ghost-secondary w-100 btn-pill" type="button"
                                        data-bs-toggle="modal" data-bs-target="#questionsModal">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-edit" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1">
                                            </path>
                                            <path
                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z">
                                            </path>
                                            <path d="M16 5l3 3"></path>
                                        </svg>
                                        Post
                                    </button>
                                </div>
                                <!-- <div class="col-4">
                                    <a href="#" class="btn btn-ghost-secondary w-100 btn-pill">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-message-question" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M8 9h8"></path>
                                            <path d="M8 13h6"></path>
                                            <path d="M14 18h-1l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v4.5">
                                            </path>
                                            <path d="M19 22v.01"></path>
                                            <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483">
                                            </path>
                                        </svg>
                                        Jawab
                                    </a>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            @foreach($question as $info)
            <div class="card" style="margin-bottom: 10px!important;" id="question-{{ $info->id_question }}">
                @include('home.question')
            </div>
            @endforeach

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // Ambil query parameter dari URL
                    const urlParams = new URLSearchParams(window.location.search);
                    const focusId = urlParams.get('focus');

                    // Cek apakah parameter 'focus' ada
                    if (focusId) {
                        // Cari elemen berdasarkan ID
                        const element = document.getElementById(`question-${focusId}`);

                        if (element) {
                            // Scroll ke elemen dengan animasi halus
                            element.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });

                            // Berikan efek sorotan untuk menandai elemen dengan box shadow
                            element.style.transition = 'box-shadow 0.5s';
                            element.style.boxShadow = '0 0 10px 5px rgba(66, 153, 225, 0.7)'; // Warna shadow sementara

                            // Kembalikan shadow ke semula setelah 2 detik
                            setTimeout(() => {
                                element.style.boxShadow = '';
                            }, 2000);
                        }
                    }
                });
            </script>





        </div>

        <div class="col-lg-1 call-to-action" style="height: min-content;">
            <h3 style="margin-top: 1vh!important;">Iklan</h3>
            <ul class="list-group" style="margin-bottom: 10px!important;">
                @foreach ($iklan->shuffle()->take(3) as $item)
                <li class="list-group-item" data-bs-toggle="modal" data-bs-target="#iklanModal{{ $item->id_iklan }}" style="cursor: pointer;">
                    <img src="data:image/png;base64,{{ $item->gambar }}" class="img-fluid"
                        style="max-width: 40%; height: auto;">
                    <a style="text-decoration:none;">{{ $item->judul}}
                    </a>
                </li>

                <!-- Iklan Modal -->
                <div class="modal fade" id="iklanModal{{ $item->id_iklan }}" tabindex="-1" role="dialog"
                    aria-labelledby="imageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <b>{{ $item->judul}}</b>
                            </div>
                            <div class="modal-body">
                                <img src="data:image/png;base64,{{ $item->gambar }}" class="img-fluid"
                                    style="max-width: 40%; height: auto;">
                                <br></br>
                                <p>{{ $item->deskripsi}}</p>
                            </div>
                            <div class="modal-footer">
                                <a href="{{ $item->linkIklan}}"
                                    class="btn btn-success"
                                    target="_blank"
                                    rel="noopener noreferrer">Klik untuk informasi lebih lanjut</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </ul>
        </div>
    </div>
</div>



<!-- Question Modal -->
<div class="modal fade" id="questionsModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="questionsForm" action="{{ route(auth()->user()->role . '.createquestion') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <b>Create Post</b>
                </div>
                <div class="modal-body">
                    <div class="mb-3" style="display: flex">
                        <div class="col-4">
                            <select id="kategori" name="kategori" class="form-control" style="color: #b2b2b3!important;" required>
                                <option value="" disabled selected>Pilih Kategori </option>
                                <option style="color: black!important;" value="Sholat">Sholat</option>
                                <option style="color: black!important;" value="Nikah">Nikah</option>
                                <option style="color: black!important;" value="Puasa">Puasa</option>
                                <option style="color: black!important;" value="Zakat">Zakat</option>
                            </select>
                        </div>
                        <div class="col-8" style="display:flex; justify-content: flex-end;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" onclick="document.getElementById('fileInput').click();">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                    <path d="M15 8h.01M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3z" />
                                    <path d="m3 16l5-5c.928-.893 2.072-.893 3 0l5 5" />
                                    <path d="m14 14l1-1c.928-.893 2.072-.893 3 0l3 3" />
                                </g>
                            </svg>
                            <input type="file" id="fileInput" name="image" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: #000000;">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="border rounded-0 form-control summernote" rows="6" placeholder="Write something" required></textarea>
                    </div>
                    <div id="preview" style="display: flex; justify-content:center; cursor: pointer;" onclick="document.getElementById('fileInput').click();"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost-secondary btn-pill" data-bs-dismiss="modal" id="cancelButton">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-pill">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk menampilkan pratinjau gambar
    function previewImage(event) {
        const preview = document.getElementById('preview');
        preview.innerHTML = ''; // Kosongkan preview sebelumnya

        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                // img.style.maxWidth = '100%';
                // img.style.maxHeight = '300px'; // Sesuaikan tinggi maksimum jika perlu
                preview.appendChild(img);
            };

            reader.readAsDataURL(file);
        }
    }

    // Fungsi untuk mereset modal
    function resetModal() {
        const form = document.getElementById('questionsForm');
        form.reset(); // Mereset form

        // Kosongkan pratinjau gambar
        const preview = document.getElementById('preview');
        preview.innerHTML = '';
    }

    // Event listener untuk modal
    document.getElementById('questionsModal').addEventListener('hidden.bs.modal', function(e) {
        resetModal(); // Reset modal ketika modal ditutup
    });
</script>

@endsection