@extends('partials.navbar')
@section('profile')

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
                            @if ($data->gambar)
                            <img class="avatar avatar-xl rounded-circle"
                                src="data:image/png;base64,{{ $data->gambar }}"
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
                                    <p style="margin-bottom: 0;">{{ $data->nama }}</p>
                                </b>
                                <b style="font-size: 20px">
                                    <p>{{ $data->email }}</p>
                                </b>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#profileModal">Edit profile</a>
                                <a class="text-primary"> | </a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#passwordModal">Edit password</a>
                                @if ($data->deskripsi)
                                <a class="text-primary"> | </a>
                                <a class="text-primary" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Hapus deskripsi</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 ms-1">
                    @if ($data->deskripsi)
                    <!-- Jika deskripsi tidak null, tampilkan isi deskripsi dan tombol hapus -->
                    <div class="deskripsi-container" data-bs-toggle="modal" data-bs-target="#deskripsiModal">
                        <div class="deskripsi-text">{{ $data->deskripsi }} <i class="fa-solid fa-pen-to-square"></i></div>
                    </div>
                    <br></br>
                    @else
                    <!-- Jika deskripsi null, tampilkan teks untuk menulis deskripsi -->
                    <a href="#" class="text-secondary" data-bs-toggle="modal" data-bs-target="#deskripsiModal">
                        Write a description about yourself <i class="fa-solid fa-pen-to-square"></i>
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
                    <span class="float-end">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#credentialsModal">
                            <svg role="button" xmlns="http://www.w3.org/2000/svg"
                                class="icon icon-tabler icon-tabler-edit" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                <path d="M16 5l3 3"></path>
                            </svg>
                        </a>
                    </span>

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
                        @if ($data->pekerjaan)
                        Pekerjaan: {{ $data->pekerjaan }}
                        @else
                        Add employment credential
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
                        @if ($data->pendidikan)
                        Pendidikan: {{ $data->pendidikan }}
                        @else
                        Add education credential
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
                        Alamat: {{ $data->alamat }}
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

    <!-- Password Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="passwordForm" action="{{ route(auth()->user()->role . '.updatePassword') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <b>Edit Password</b>
                    </div>
                    <div class="modal-body">
                        <!-- Current Password Field -->
                        <div class="mb-3 position-relative">
                            <label for="current_password" class="form-label">Password saat ini</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Masukkan password saat ini">
                                <span class="input-group-text" onclick="togglePasswordVisibility('current_password', this)">
                                    <i class="fas fa-eye"></i> <!-- Ikon mata -->
                                </span>
                            </div>
                        </div>
                        <!-- New Password Field -->
                        <div class="mb-3 position-relative">
                            <label for="password" class="form-label">Password baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password baru">
                                <span class="input-group-text" onclick="togglePasswordVisibility('password', this)">
                                    <i class="fas fa-eye"></i> <!-- Ikon mata -->
                                </span>
                            </div>
                        </div>
                        <!-- Confirmation Password Field -->
                        <div class="mb-3 position-relative">
                            <label for="password_confirmation" class="form-label">Konfirmasi password baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password baru">
                                <span class="input-group-text" onclick="togglePasswordVisibility('password_confirmation', this)">
                                    <i class="fas fa-eye"></i> <!-- Ikon mata -->
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost-secondary btn-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-pill">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Reset form saat modal ditutup
            var profileModal = document.getElementById('passwordModal');
            profileModal.addEventListener('hidden.bs.modal', function() {
                // Reset form
                document.getElementById('passwordForm').reset();

                // Reset semua input password ke tipe password dan ikon mata ke default (fa-eye)
                const passwordFields = ['current_password', 'password', 'password_confirmation'];
                passwordFields.forEach(function(fieldId) {
                    const inputField = document.getElementById(fieldId);
                    const iconElement = inputField.nextElementSibling.querySelector('i');

                    // Kembalikan tipe input menjadi 'password'
                    inputField.type = 'password';

                    // Kembalikan ikon mata menjadi 'fa-eye'
                    if (iconElement.classList.contains('fa-eye-slash')) {
                        iconElement.classList.remove('fa-eye-slash');
                        iconElement.classList.add('fa-eye');
                    }
                });
            });
        });

        function togglePasswordVisibility(fieldId, icon) {
            const inputField = document.getElementById(fieldId);
            const iconElement = icon.querySelector('i');

            if (inputField.type === "password") {
                inputField.type = "text";
                iconElement.classList.remove("fa-eye");
                iconElement.classList.add("fa-eye-slash");
            } else {
                inputField.type = "password";
                iconElement.classList.remove("fa-eye-slash");
                iconElement.classList.add("fa-eye");
            }
        }
    </script>

    <!-- Credentials Modal -->
    <div class="modal fade" id="credentialsModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="credentialsForm" action="{{ route(auth()->user()->role . '.updateprofile') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <b>Edit Credentials</b>
                    </div>
                    <div class="modal-body">
                        <!-- Employment Field -->
                        <div class="mb-3">
                            <label for="pekerjaan" class="form-label">Employment</label>
                            <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" placeholder="{{ $data->pekerjaan ? $data->pekerjaan : 'Tambahkan pekerjaan anda saat ini' }}">
                        </div>
                        <!-- Education Field -->
                        <div class="mb-3">
                            <label for="pendidikan" class="form-label">Education</label>
                            <input type="text" class="form-control" id="pendidikan" name="pendidikan" placeholder="{{ $data->pendidikan ? $data->pendidikan : 'Tambahkan pendidikan terakhir anda' }}">
                        </div>
                        <!-- Location Field -->
                        <div class="mb-3 position-relative">
                            <label for="alamat" class="form-label">Location</label>
                            <input type="text" class="form-control" id="alamat" name="alamat" placeholder="{{ $data->alamat }}">
                            <ul id="suggestions" class="list-group mt-2"></ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost-secondary btn-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-pill">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('alamat');
            const suggestionsList = document.getElementById('suggestions');
            const cities = @json($cities);

            input.addEventListener('input', function() {
                const query = input.value.toLowerCase();
                suggestionsList.innerHTML = '';

                if (query.length > 0) {
                    const filteredCities = cities.filter(city => city.lokasi.toLowerCase().includes(query));

                    filteredCities.forEach(city => {
                        const listItem = document.createElement('li');
                        listItem.textContent = city.lokasi;
                        listItem.classList.add('list-group-item');
                        listItem.addEventListener('click', () => {
                            input.value = city.lokasi;
                            suggestionsList.innerHTML = '';
                        });
                        suggestionsList.appendChild(listItem);
                    });
                }
            });

            // Handle form submission or Enter key press
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    const firstSuggestion = suggestionsList.querySelector('.list-group-item');
                    if (firstSuggestion) {
                        input.value = firstSuggestion.textContent;
                        suggestionsList.innerHTML = '';
                    }
                    e.preventDefault(); // Prevent form submission
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Reset form saat modal ditutup
            var profileModal = document.getElementById('credentialsModal');
            profileModal.addEventListener('hidden.bs.modal', function() {
                document.getElementById('credentialsForm').reset();
            });
        });
    </script>



    <!-- Deskripsi Modal -->
    <div class="modal fade" id="deskripsiModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="deskripsiForm" action="{{ route(auth()->user()->role . '.updatedeskripsi') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <b>Edit Description</b>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" style="color: #000000;">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi"
                                class="border rounded-0 form-control summernote" rows="6">{{ $data->deskripsi }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost-secondary btn-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-pill">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Konfirmasi Hapus Deskripsi Modal-->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus Deskripsi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus deskripsi ini?
                </div>
                <div class="modal-footer">
                    <!-- Form untuk menghapus deskripsi -->
                    <form id="deleteDescriptionForm" action="{{ route(auth()->user()->role . '.updatedeskripsi') }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="delete_deskripsi">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="profileForm" action="{{ route(auth()->user()->role . '.updateprofile') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <b>Edit profile</b>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Gambar -->
                            <div class="col-2">
                                <div class="avatar-preview">
                                    <img id="avatarPreview"
                                        src="{{ $data->gambar ? 'data:image/png;base64,' . $data->gambar : asset('img/user2.png') }}"
                                        alt=""
                                        class="avatar avatar-lg rounded-circle">
                                </div>
                            </div>
                            <div class="col-10">
                                <label class="form-label">
                                    Image
                                </label>
                                <input type="file" class="form-control" name="image" id="imageUpload" accept="image/*">
                                @if ($data->gambar)
                                <button type="button" id="deleteImageButton" class="btn btn-danger btn-sm mt-2">Hapus Gambar</button>
                                <!-- <button type="submit" name="action" value="delete_image" class="btn btn-danger btn-sm mt-2">Hapus Gambar</button> -->
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <!-- Nama -->
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="{{ $data->nama }}">
                                </div>

                                <!-- Tanggal Lahir -->
                                <div class="mb-3">
                                    <label for="date" class="form-label">Tanggal Lahir</label>
                                    <input class="form-control" id="date" name="date" type="text"
                                        style="font-size: 14px; background-color: unset;" placeholder="{{ $data->tanggal_lahir->format('d-m-Y') }}">
                                    @error('date')
                                    <div style="color: red; font-size: 12px; position: absolute;">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Alamat -->
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="alamat1" name="alamat" placeholder="{{ $data->alamat }}">
                                    <ul id="suggestions1" class="list-group mt-2"></ul>
                                </div>

                                <!-- Gender -->
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label for="gender">Jenis Kelamin</label>
                                        <select id="gender" name="gender" class="form-control" style="color: #b2b2b3!important;">
                                            <option value="" disabled selected>{{ $data->gender ?? 'Pilih Jenis Kelamin' }}</option>
                                            <option style="color: black!important;" value="Laki-laki" {{ $data->gender === 'Laki-laki' ? 'disabled' : '' }}>Laki-laki</option>
                                            <option style="color: black!important;" value="Perempuan" {{ $data->gender === 'Perempuan' ? 'disabled' : '' }}>Perempuan</option>
                                        </select>
                                    </div>

                                    @error('gender')
                                    <div style="color: red; font-size: 12px;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <!-- No Telepon -->
                                <div class="mb-3">
                                    <label for="telp" class="form-label">No Telepon</label>
                                    <input type="text" class="form-control" id="telp" name="telp" placeholder="{{ $data->no_telepon }}">
                                </div>

                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="{{ $user->username }}">
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="{{ $data->email }}">
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <!-- Password fields can be added here if needed -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost-secondary btn-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-pill">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan script Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.6/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.6/dist/flatpickr.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Temukan elemen input tanggal
        var dateInput = document.getElementById("date");

        // Inisialisasi Flatpickr dengan format yang diinginkan
        flatpickr(dateInput, {
            dateFormat: "d-m-Y",
            maxDate: "today", // Mengatur tanggal maksimum ke hari ini
            disableMobile: true,
        });
    });
</script>

<!-- Preview gambar -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Temukan elemen input file dan preview gambar
        var imageInput = document.getElementById("imageUpload");
        var imagePreview = document.getElementById("avatarPreview");

        // Fungsi untuk menampilkan preview gambar
        imageInput.addEventListener("change", function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = "{{ $data->gambar ? asset($data->gambar) : asset('img/user2.png') }}";
            }
        });

        // Reset form saat modal ditutup
        var profileModal = document.getElementById('profileModal');
        profileModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('profileForm').reset();
            document.getElementById('avatarPreview').src = "{{ $data->gambar ? asset($data->gambar) : asset('img/user2.png') }}";
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Reset form saat modal ditutup
        var profileModal = document.getElementById('deskripsiModal');
        profileModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('deskripsiForm').reset();
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var deleteImageButton = document.getElementById('deleteImageButton');
        var avatarPreview = document.getElementById('avatarPreview');

        deleteImageButton.addEventListener('click', function() {
            // Ganti gambar dengan gambar default
            avatarPreview.src = "{{ asset('img/user2.png') }}";
            // Tambahkan input tersembunyi untuk mengatur penghapusan gambar
            var deleteImageInput = document.createElement('input');
            deleteImageInput.type = 'hidden';
            deleteImageInput.name = 'action';
            deleteImageInput.value = 'delete_image';
            document.getElementById('profileForm').appendChild(deleteImageInput);
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('alamat1');
        const suggestionsList = document.getElementById('suggestions1');
        const cities = @json($cities);

        input.addEventListener('input', function() {
            const query = input.value.toLowerCase();
            suggestionsList.innerHTML = '';

            if (query.length > 0) {
                const filteredCities = cities.filter(city => city.lokasi.toLowerCase().includes(query));

                filteredCities.forEach(city => {
                    const listItem = document.createElement('li');
                    listItem.textContent = city.lokasi;
                    listItem.classList.add('list-group-item');
                    listItem.addEventListener('click', () => {
                        input.value = city.lokasi;
                        suggestionsList.innerHTML = '';
                    });
                    suggestionsList.appendChild(listItem);
                });
            }
        });

        // Handle form submission or Enter key press
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const firstSuggestion = suggestionsList.querySelector('.list-group-item');
                if (firstSuggestion) {
                    input.value = firstSuggestion.textContent;
                    suggestionsList.innerHTML = '';
                }
                e.preventDefault(); // Prevent form submission
            }
        });
    });
</script>

@endsection