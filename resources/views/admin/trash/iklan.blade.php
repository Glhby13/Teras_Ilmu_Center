@extends('admin.navbar')
@section('iklanTrash')

<style>
    /* Tambahkan CSS di sini */
    #iklanTable th:first-child.sorting_asc,
    #iklanTable th:first-child.sorting_desc,
    #iklanTable th:first-child.sorting {
        background-image: none !important;
        cursor: default !important;
    }

    /* Menambahkan jarak antara Show entries/Search dan tabel */
    #iklanTable_wrapper .dataTables_filter {
        margin-bottom: 20px;
        /* Jarak antara Search dan tabel */
    }

    /* Menambahkan border pada tabel */
    #iklanTable {
        border: 1px solid #dee2e6;
    }

    #iklanTable th,
    #iklanTable td {
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
    <a href="{{ route('admin.iklan') }}" class="btn mt-2 mb-2 btn-info">
        <i class="fa-solid fa-chevron-left" style="margin-right: 5px;"></i>
        Back
    </a>
    <div class="card">
        <div class="card-body">
            <h2>Delected Data Iklan</h2>
            <div class="table-responsive">
                <table id="IklanTable" class="table table-hover table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 13px;"><input type="checkbox" id="select-all"></th>
                            <th>Judul Iklan</th>
                            <th>Deskripsi</th>
                            <th>Link Iklan</th>
                            <th>Gambar</th>
                            <th style="width: 90px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($iklan as $item)
                        <tr>
                            <td style="padding-left: 12px !Important;"><input type="checkbox" class="select-checkbox"></td>
                            <td>{{ $item->judul}}</td>
                            <td>{{ $item->deskripsi}}</td>
                            <td style="max-width: 200px;">
                                <a href="{{ $item['linkIklan'] ?? '#' }}"
                                    target="_blank">{{ $item['linkIklan'] ?? ' ' }}</a>
                            </td>
                            <td>
                                <img class="img-fluid" style="max-height: 70px; max-width: 100%;"
                                    src="data:image/png;base64,{{ $item->gambar }}"
                                    alt=""
                                    data-bs-toggle="modal" data-bs-target="#imageModal{{ $item->id_iklan }}">
                                <!-- Modal -->
                                <div class="modal fade" id="imageModal{{ $item->id_iklan }}" tabindex="-1" role="dialog"
                                    aria-labelledby="imageModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 85vh!important;">
                                        <div class="modal-content" style="background-color: unset; border: none;">
                                            <div class="modal-body">
                                                @if ($item->gambar)
                                                <img src="data:image/png;base64,{{ $item->gambar }}" class="img-fluid"
                                                    style="min-width: 100%; height: auto;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons" style="text-align: center;">
                                    <button type="button" class="btn btn-sm btn-outline-success restore-btn" data-iklan="{{ $item->id_iklan }}">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger hard-delete-btn" data-iklan="{{ $item->id_iklan }}">
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
                const iklan = this.getAttribute('data-iklan');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Apakah Anda yakin ingin memulihkan iklan?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Pulihkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("admin.restoreIklan") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    iklans: [iklan] // Pastikan `iklans` adalah array
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Dipulihkan!', 'Data iklan telah dipulihkan.', 'success')
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
            const selectedIklans = Array.from(document.querySelectorAll('.select-checkbox:checked'))
                .map(checkbox => checkbox.closest('tr').querySelector('.restore-btn').getAttribute('data-iklan'));

            if (selectedIklans.length === 0) {
                Swal.fire('Peringatan!', 'Tidak ada item yang dipilih.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin memulihkan ${selectedIklans.length} iklan?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Pulihkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("admin.restoreIklan") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                iklans: selectedIklans
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire('Dipulihkan!', 'Data iklan telah dipulihkan.', 'success')
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
                const iklan = this.getAttribute('data-iklan');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Apakah Anda yakin ingin menghapus iklan secara permanen?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("admin.DeleteIklan") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    iklans: [iklan] // Pastikan `iklans` adalah array
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Dihapus!', 'Data iklan telah dihapus.', 'success')
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
            const selectedIklans = Array.from(document.querySelectorAll('.select-checkbox:checked'))
                .map(checkbox => checkbox.closest('tr').querySelector('.hard-delete-btn').getAttribute('data-iklan'));

            if (selectedIklans.length === 0) {
                Swal.fire('Peringatan!', 'Tidak ada item yang dipilih.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin menghapus ${selectedIklans.length} iklan secara permanen?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("admin.DeleteIklan") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                iklans: selectedIklans
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire('Dihapus!', 'Data iklan telah dihapus.', 'success')
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
        var table = $('#iklanTable').DataTable({
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            columnDefs: [{
                    orderable: false,
                    targets: [0, 5]
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