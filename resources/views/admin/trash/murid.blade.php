@extends('admin.navbar')
@section('muridTrash')

<style>
    /* Tambahkan CSS di sini */
    #muridTable th:first-child.sorting_asc,
    #muridTable th:first-child.sorting_desc,
    #muridTable th:first-child.sorting {
        background-image: none !important;
        cursor: default !important;
    }

    /* Menambahkan jarak antara Show entries/Search dan tabel */
    #muridTable_wrapper .dataTables_filter {
        margin-bottom: 20px;
        /* Jarak antara Search dan tabel */
    }

    /* Menambahkan border pada tabel */
    #muridTable {
        border: 1px solid #dee2e6;
    }

    #muridTable th,
    #muridTable td {
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
    <a href="{{ route('admin.murid') }}" class="btn mt-2 mb-2 btn-info">
        <i class="fa-solid fa-chevron-left" style="margin-right: 5px;"></i>
        Back
    </a>
    <div class="card">
        <div class="card-body">
            <h2>Delected Data Murid</h2>
            <div class="table-responsive">
                <table id="muridTable" class="table table-hover table-bordered" width="100%" cellspacing="0">
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
                        @foreach ($murid as $user)
                        <!-- Data dummy -->
                        <tr>
                            <td style="padding-left: 18px !Important;"><input type="checkbox" class="select-checkbox"></td>
                            <td>{{ $user->murid->nama }}</td>
                            <td>{{ \Carbon\Carbon::parse($user->murid->tanggal_lahir)->format('d M Y') }}</td>
                            <td>{{ $user->murid->alamat }}</td>
                            <td>{{ $user->murid->gender }}</td>
                            <td>{{ $user->murid->no_telepon }}</td>
                            <td>{{ $user->murid->username }}</td>
                            <td>{{ $user->murid->email }}</td>
                            <td>
                                <div class="action-buttons" style="text-align: center;">
                                    <button type="button" class="btn btn-sm btn-outline-success restore-btn" data-username="{{ $user->murid->username }}">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger hard-delete-btn" data-username="{{ $user->murid->username }}">
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

            <div class="d-flex bd-highlight mb-3 align-items-center mt-3">
                <div class="me-auto p-2 bd-highlight">
                    <div>
                        <span id="selected-count">0</span> item(s) selected.
                    </div>
                </div>
                <div class="p-2 bd-highlight">
                    <button type="button" id="selected-Restore-btn" class="btn btn-success" disabled>
                        <i class="fas fa-trash" style="margin-right: 5px;"></i> Restore Selected
                    </button>
                </div>
                <div class="p-2 bd-highlight">
                    <button type="button" id="bulk-Harddelete-btn" class="btn btn-danger" disabled>
                        <i class="fas fa-trash" style="margin-right: 5px;"></i> Delete Selected
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Restore Invididual Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle individual restore
        document.querySelectorAll('.restore-btn').forEach(button => {
            button.addEventListener('click', function() {
                const username = this.getAttribute('data-username');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Apakah Anda yakin ingin memulihkan murid dengan username ${username}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Pulihkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("admin.restoreMurid") }}', {
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
                                    Swal.fire('Dipulihkan!', 'Data murid telah dipulihkan.', 'success')
                                        .then(() => {
                                            // Refresh halaman
                                            setTimeout(() => {
                                                location.reload();
                                            }, 100);
                                        });
                                } else {
                                    Swal.fire('Error!', 'Terjadi kesalahan saat memulihkan data.', 'error');
                                }
                            })
                            .catch(error => {
                                Swal.fire('Error!', 'Terjadi kesalahan saat memulihkan data.', 'error');
                            });
                    }
                });
            });
        });
    });
</script>
<!-- Restore Selected Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('selected-Restore-btn').addEventListener('click', function() {
            const selectedUsernames = Array.from(document.querySelectorAll('.select-checkbox:checked'))
                .map(checkbox => checkbox.closest('tr').querySelector('.restore-btn').getAttribute('data-username'));

            if (selectedUsernames.length === 0) {
                Swal.fire('Peringatan!', 'Tidak ada item yang dipilih.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin memulihkan ${selectedUsernames.length} murid?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Pulihkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("admin.restoreMurid") }}', {
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
                            Swal.fire('Dipulihkan!', 'Data murid telah dipulihkan.', 'success')
                                .then(() => {
                                    // Refresh halaman setelah 2 detik
                                    setTimeout(() => {
                                        location.reload();
                                    }, 100);
                                });
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'Terjadi kesalahan saat memulihkan data.', 'error');
                        });
                }
            });
        });
    });
</script>


<!-- Hard Delete Invididual Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle individual hard delete
        document.querySelectorAll('.hard-delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const username = this.getAttribute('data-username');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Apakah Anda yakin ingin menghapus murid dengan username ${username} secara permanen?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("admin.DeleteMurid") }}', {
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
                                    Swal.fire('Dihapus!', 'Data murid telah dihapus.', 'success')
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
<!-- Hard Delete Selected Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('bulk-Harddelete-btn').addEventListener('click', function() {
            const selectedUsernames = Array.from(document.querySelectorAll('.select-checkbox:checked'))
                .map(checkbox => checkbox.closest('tr').querySelector('.hard-delete-btn').getAttribute('data-username'));

            if (selectedUsernames.length === 0) {
                Swal.fire('Peringatan!', 'Tidak ada item yang dipilih.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin menghapus ${selectedUsernames.length} murid secara permanen?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("admin.DeleteMurid") }}', {
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
                            Swal.fire('Dihapus!', 'Data murid telah dihapus.', 'success')
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
        var table = $('#muridTable').DataTable({
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

            // Disable/Enable selected restore button based on selected items
            $('#selected-Restore-btn').prop('disabled', checkedCheckboxes === 0);

            // Disable/Enable bulk delete button based on selected items
            $('#bulk-Harddelete-btn').prop('disabled', checkedCheckboxes === 0);

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