@extends('admin.navbar')
@section('umum')

<style>
    /* Tambahkan CSS di sini */
    #umumTable th:first-child.sorting_asc,
    #umumTable th:first-child.sorting_desc,
    #umumTable th:first-child.sorting {
        background-image: none !important;
        cursor: default !important;
    }

    /* Menambahkan jarak antara Show entries/Search dan tabel */
    #umumTable_wrapper .dataTables_filter {
        margin-bottom: 20px;
        /* Jarak antara Search dan tabel */
    }

    /* Menambahkan border pada tabel */
    #umumTable {
        border: 1px solid #dee2e6;
    }

    #umumTable th,
    #umumTable td {
        border: 1px solid #dee2e6;
    }

    /* Menambahkan style untuk tombol yang disable */
    .action-disabled button {
        pointer-events: none;
        opacity: 0.5;
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

    #editSuggestions {
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

<!-- Tambahkan script Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.6/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.6/dist/flatpickr.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Temukan elemen input tanggal
        var dateInput = document.getElementById("date");

        // Dapatkan tanggal hari ini
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();
        today = dd + '-' + mm + '-' + yyyy;

        // Inisialisasi Flatpickr dengan format yang diinginkan
        flatpickr(dateInput, {
            dateFormat: "d-m-Y",
            maxDate: today,
            disableMobile: true,
        });
    });
</script>
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


<div class="container">
    <button type="button" class="btn btn-primary mt-2 mb-2" data-toggle="modal" data-target="#tambahUmum">
        <i class="fas fa-plus" style="margin-right: 5px;"></i>
        Tambah Data
    </button>

    <!-- Modal Tambah Data Umum -->
    <div class="modal fade" id="tambahUmum" tabindex="-1" aria-labelledby="modalTambahUmumLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahUmumLabel">Tambah Data Pengguna Umum</h5>
                </div>
                <form id="formTambahUmum" action="{{ route('admin.createUmum') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Kolom 1 -->
                            <div class="col-md-6">
                                <!-- Nama -->
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama" required>
                                </div>

                                <!-- Tanggal Lahir -->
                                <div class="mb-3">
                                    <label for="date" class="form-label">Tanggal Lahir</label>
                                    <input class="form-control" id="date" name="date" type="text"
                                        style="font-size: 14px; background-color: unset;" placeholder="dd-mm-yyyy" required>
                                </div>

                                <!-- Alamat -->
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Ketik nama kota/kabupaten" required>
                                    <ul id="suggestions" class="list-group mt-2"></ul>
                                </div>

                                <!-- Jenis Kelamin -->
                                <div class="mb-3">
                                    <label class="form-label d-block">Jenis Kelamin</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="Laki-laki" value="Laki-laki" required>
                                        <label class="form-check-label" for="Laki-laki">Laki-laki</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="Perempuan" value="Perempuan" required>
                                        <label class="form-check-label" for="Perempuan">Perempuan</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Kolom 2 -->
                            <div class="col-md-6">
                                <!-- No Telepon -->
                                <div class="mb-3">
                                    <label for="telp" class="form-label">No Telepon</label>
                                    <input type="text" class="form-control" id="telp" name="telp" placeholder="No Telepon" required>
                                </div>

                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost-secondary btn-pill" data-bs-dismiss="modal" id="btnCancel">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-pill" id="saveButton">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Script Modal Tambah Data Umum -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle form reset when Cancel button is clicked
            document.getElementById('btnCancel').addEventListener('click', function() {
                document.getElementById('formTambahUmum').reset();
            });

            // Initialize modal
            var myModalEl = document.getElementById('tambahUmum');
            var modal = new bootstrap.Modal(myModalEl);

            // Show modal when Add Data button is clicked
            document.querySelector('[data-toggle="modal"]').addEventListener('click', function() {
                modal.show();
            });

            var saveButton = document.getElementById('saveButton');
            var form = document.getElementById('formTambahUmum');

            var validations = [{
                    id: 'nama',
                    message: 'Nama harus diisi.'
                },
                {
                    id: 'date',
                    message: 'Tanggal Lahir harus diisi.'
                },
                {
                    id: 'alamat',
                    message: 'Alamat harus diisi.'
                },
                {
                    id: 'gender',
                    message: 'Jenis Kelamin harus dipilih.',
                    isChecked: function() {
                        return document.querySelector('input[name="gender"]:checked') !== null;
                    }
                },
                {
                    id: 'telp',
                    message: 'No Telepon harus diisi.'
                },
                {
                    id: 'username',
                    message: 'Username harus diisi.'
                },
                {
                    id: 'email',
                    message: 'Email harus diisi.'
                }

            ];

            saveButton.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent form submission

                // Validate all fields
                var errors = validations.filter(function(validation) {
                    var field = document.getElementById(validation.id);
                    var value = field ? field.value.trim() : null;
                    if (validation.id === 'date') {
                        value = value.length === 0 ? null : value;
                    }
                    if (validation.isChecked) {
                        return !validation.isChecked();
                    }
                    return !value || (validation.id === 'gender' && !document.querySelector('input[name="gender"]:checked'));
                });

                if (errors.length > 0) {
                    // Show alert for the first error
                    var error = errors[0];
                    var field = document.getElementById(error.id);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (field) {
                            field.focus();
                        } else {
                            document.querySelector('input[name="gender"]').focus();
                        }
                    });
                    return;
                }

                // If all fields are valid, submit the form
                form.submit();
            });
        });
    </script>



    <a href="{{ route('admin.trashUmum') }}" class="btn btn-danger mt-2 mb-2">
        <i class="fas fa-trash" style="margin-right: 5px;"></i>
        Trash
    </a>

    <div class="card">
        <div class="card-body">
            <h2>Data Pengguna Umum</h2>
            <div class="table-responsive">
                <table id="umumTable" class="table table-hover table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 13px;"><input type="checkbox" id="select-all"></th>
                            <th>Nama</th>
                            <th>Tanggal Lahir</th>
                            <th>Alamat</th>
                            <th>Jenis Kelamin</th>
                            <th>No Telepon</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th style="width: 60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($umum as $user)
                        <!-- Data dummy -->
                        <tr>
                            <td style="padding-left: 18px !Important;"><input type="checkbox" class="select-checkbox"></td>
                            <td>{{ $user->umum->nama }}</td>
                            <td>{{ \Carbon\Carbon::parse($user->umum->tanggal_lahir)->format('d M Y') }}</td>
                            <td>{{ $user->umum->alamat }}</td>
                            <td>{{ $user->umum->gender }}</td>
                            <td>{{ $user->umum->no_telepon }}</td>
                            <td>{{ $user->umum->username }}</td>
                            <td>{{ $user->umum->email }}</td>
                            <td>
                                <div class="action-buttons" style="text-align: center;">
                                    <button data-bs-toggle="modal" data-bs-target="#editUmum{{ $user->umum->id_umum }}"
                                        type="button" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>

                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            // Temukan elemen input tanggal
                                            var dateInput = document.getElementById("editDate");

                                            // Dapatkan tanggal hari ini
                                            var today = new Date();
                                            var dd = String(today.getDate()).padStart(2, '0');
                                            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                                            var yyyy = today.getFullYear();
                                            today = dd + '-' + mm + '-' + yyyy;

                                            // Inisialisasi Flatpickr dengan format yang diinginkan
                                            flatpickr(dateInput, {
                                                dateFormat: "d-m-Y",
                                                maxDate: today,
                                                disableMobile: true,
                                            });
                                        });
                                    </script>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const input = document.getElementById('editAlamat');
                                            const suggestionsList = document.getElementById('editSuggestions');
                                            const cities = @json($editCities);

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

                                    <!-- Modal Edit Data Umum -->
                                    <div class="modal fade" id="editUmum{{ $user->umum->id_umum }}" tabindex="-1" aria-labelledby="modalEditUmumLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalEditUmumLabel">Edit Data Umum: {{ $user->umum->nama }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form id="formEditUmum" action="{{ route('admin.updateUmum', $user->umum->id_umum ) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-6" style="text-align: left;">
                                                                <!-- Nama -->
                                                                <div class="mb-3">
                                                                    <label for="nama" class="form-label">Nama</label>
                                                                    <input type="text" class="form-control" id="editNama" name="nama" placeholder="{{ $user->umum->nama }}">
                                                                </div>

                                                                <!-- Tanggal Lahir -->
                                                                <div class="mb-3">
                                                                    <label for="date" class="form-label">Tanggal Lahir</label>
                                                                    <input class="form-control" id="editDate" name="date" type="text" placeholder="{{ $user->umum->tanggal_lahir->format('d-m-Y') }}">
                                                                </div>

                                                                <!-- Alamat -->
                                                                <div class="mb-3">
                                                                    <label for="alamat" class="form-label">Alamat</label>
                                                                    <input type="text" class="form-control" id="editAlamat" name="alamat" placeholder="{{ $user->umum->alamat }}">
                                                                    <ul id="editSuggestions" class="list-group mt-2"></ul>
                                                                </div>

                                                                <!-- Gender -->
                                                                <div class="mb-3">
                                                                    <label for="gender" class="form-label">Jenis Kelamin</label>
                                                                    <select id="gender" name="gender" class="form-control" style="color: #b2b2b3!important;">
                                                                        <option value="" disabled selected>{{ $user->umum->gender ?? 'Pilih Jenis Kelamin' }}</option>
                                                                        <option style="color: black!important;" value="Laki-laki" {{ $user->umum->gender === 'Laki-laki' ? 'disabled' : '' }}>Laki-laki</option>
                                                                        <option style="color: black!important;" value="Perempuan" {{ $user->umum->gender === 'Perempuan' ? 'disabled' : '' }}>Perempuan</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-6" style="text-align: left;">
                                                                <!-- No Telepon -->
                                                                <div class="mb-3">
                                                                    <label for="telp" class="form-label">No Telepon</label>
                                                                    <input type="text" class="form-control" id="editTelp" name="telp" placeholder="{{ $user->umum->no_telepon }}">
                                                                </div>

                                                                <!-- Username -->
                                                                <div class="mb-3">
                                                                    <label for="username" class="form-label">Username</label>
                                                                    <input type="text" class="form-control" id="editUsername" name="username" placeholder="{{ $user->umum->username }}">
                                                                </div>

                                                                <!-- Email -->
                                                                <div class="mb-3">
                                                                    <label for="email" class="form-label">Email</label>
                                                                    <input type="email" class="form-control" id="editEmail" name="email" placeholder="{{ $user->umum->email }}">
                                                                </div>

                                                                <!-- Password -->
                                                                <div class="mb-3">
                                                                    <!-- Password fields can be added here if needed -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-ghost-secondary btn-pill" data-bs-dismiss="modal" id="EditbtnCancel">Cancel</button>
                                                        <button type="submit" class="btn btn-primary btn-pill" id="EditsaveButton">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Script Modal Edit Data Umum -->
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            // Handle form reset when Cancel button is clicked
                                            document.getElementById('EditbtnCancel').addEventListener('click', function() {
                                                document.getElementById('formEditUmum').reset();
                                            });
                                        });
                                    </script>

                                    <button type="button" class="btn btn-sm btn-outline-danger soft-delete-btn" data-username="{{ $user->umum->username }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        <!-- Tambahkan data dummy lainnya di sini -->
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <span id="selected-count">0</span> item(s) selected.
                </div>
                <button type="button" id="bulk-delete-btn" class="btn btn-danger" disabled>
                    <i class="fas fa-trash" style="margin-right: 5px;"></i> Delete Selected
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Soft Delete Invididual Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle individual soft delete
        document.querySelectorAll('.soft-delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const username = this.getAttribute('data-username');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Apakah Anda yakin ingin menghapus pengguna umum dengan username ${username}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("admin.softDeleteUmum") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    usernames: [username] // Pastikan `usernames` adalah array
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Dihapus!', 'Data pengguna umum telah dihapus.', 'success')
                                        .then(() => {
                                            // Refresh halaman
                                            setTimeout(() => {
                                                location.reload();
                                            }, 100);
                                        });
                                } else {
                                    Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                                }
                            })
                            .catch(error => {
                                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                            });
                    }
                });
            });
        });
    });
</script>
<!-- Soft Delete Selected Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('bulk-delete-btn').addEventListener('click', function() {
            const selectedUsernames = Array.from(document.querySelectorAll('.select-checkbox:checked'))
                .map(checkbox => checkbox.closest('tr').querySelector('.soft-delete-btn').getAttribute('data-username'));

            if (selectedUsernames.length === 0) {
                Swal.fire('Peringatan!', 'Tidak ada item yang dipilih.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin menghapus ${selectedUsernames.length} pengguna umum?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("admin.softDeleteUmum") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                usernames: selectedUsernames
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire('Dihapus!', 'Data umum telah dihapus.', 'success')
                                .then(() => {
                                    // Refresh halaman setelah 2 detik
                                    setTimeout(() => {
                                        location.reload();
                                    }, 100);
                                });
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                        });
                }
            });
        });
    });
</script>



<!-- Table Script -->
<script>
    $(document).ready(function() {
        var table = $('#umumTable').DataTable({
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            columnDefs: [{
                    orderable: false,
                    targets: [0, 8]
                } // Disable ordering on the first column (Select All)
            ]
        });

        // Select/Deselect all checkboxes
        $('#select-all').click(function() {
            var isChecked = $(this).is(':checked');
            $('.select-checkbox').prop('checked', isChecked);
            updateSelectedCount();
        });

        // Update selected items count and select-all checkbox state
        function updateSelectedCount() {
            var totalCheckboxes = $('.select-checkbox').length;
            var checkedCheckboxes = $('.select-checkbox:checked').length;
            $('#selected-count').text(checkedCheckboxes);

            // Disable/Enable bulk delete button based on selected items
            $('#bulk-delete-btn').prop('disabled', checkedCheckboxes === 0);

            // If not all checkboxes are selected, uncheck the select-all checkbox
            if (checkedCheckboxes < totalCheckboxes) {
                $('#select-all').prop('checked', false);
            } else {
                $('#select-all').prop('checked', true);
            }

            // Disable/Enable action buttons in each row based on checkbox selection
            $('.select-checkbox').each(function() {
                var isChecked = $(this).is(':checked');
                var actionButtons = $(this).closest('tr').find('.action-buttons');
                if (isChecked) {
                    actionButtons.addClass('action-disabled');
                } else {
                    actionButtons.removeClass('action-disabled');
                }
            });
        }

        // Update count and select-all checkbox state on individual checkbox change
        $('.select-checkbox').on('change', function() {
            updateSelectedCount();
        });
    });
</script>
@endsection