@extends('admin.navbar')
@section('iklan')

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
    <button type="button" class="btn btn-primary mt-2 mb-2" data-bs-toggle="modal" data-bs-target="#tambahIklan">
        <i class="fas fa-plus" style="margin-right: 5px;"></i>
        Tambah Data
    </button>

    <!-- Modal Tambah Iklan -->
    <div class="modal fade" id="tambahIklan" tabindex="-1" aria-labelledby="modalTambahIklanLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="iklanForm" action="{{ route('admin.createIklan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahIklanLabel">Tambah Data Iklan</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3" style="display: flex">
                            <div class="col-11">
                                <label for="judul" class="form-label">Judul Iklan</label>
                                <input type="text" class="form-control" id="judul" name="judul" placeholder="Judul Iklan" required>
                            </div>
                            <div class="col align-self-end" style="display:flex; justify-content: flex-end;">
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
                        <div class="mb-3">
                            <label for="linkIklan" class="form-label">Link Iklan</label>
                            <input type="text" class="form-control" id="linkIklan" name="linkIklan" placeholder="Tambahkan link iklan">
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
    <!-- Script Modal Tambah Data Iklan -->
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
            const form = document.getElementById('iklanForm');
            form.reset(); // Mereset form

            // Kosongkan pratinjau gambar
            const preview = document.getElementById('preview');
            preview.innerHTML = '';
        }

        // Event listener untuk modal
        document.getElementById('tambahIklan').addEventListener('hidden.bs.modal', function(e) {
            resetModal(); // Reset modal ketika modal ditutup
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var saveButton = document.querySelector('button[type="submit"]');
            var form = document.getElementById('iklanForm');

            // Validations to check each input
            var validations = [{
                    id: 'judul',
                    message: 'Judul Iklan harus diisi.'
                },
                {
                    id: 'deskripsi',
                    message: 'Deskripsi harus diisi.'
                },
                {
                    id: 'linkIklan',
                    message: 'Link Iklan harus diisi.'
                },
                {
                    id: 'fileInput',
                    message: 'Gambar Iklan harus diupload.',
                    isFile: true
                }
            ];

            saveButton.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent form submission

                // Validate all fields
                var errors = validations.filter(function(validation) {
                    var field = document.getElementById(validation.id);
                    var value = field ? (validation.isFile ? field.files.length : field.value.trim()) : null;
                    return !value;
                });

                if (errors.length > 0) {
                    // Show alert for the first error
                    var error = errors[0];
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        var field = document.getElementById(error.id);
                        if (field) {
                            field.focus();
                        }
                    });
                    return;
                }

                // If all fields are valid, submit the form
                form.submit();
            });
        });
    </script>


    <a href="{{ route('admin.trashIklan') }}" class="btn btn-danger mt-2 mb-2">
        <i class="fas fa-trash" style="margin-right: 5px;"></i>
        Trash
    </a>

    <div class="card">
        <div class="card-body">
            <h2>Data Iklan</h2>
            <div class="table-responsive">
                <table id="iklanTable" class="table table-hover table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 13px;"><input type="checkbox" id="select-all"></th>
                            <th>Judul Iklan</th>
                            <th>Deskripsi</th>
                            <th>Link Iklan</th>
                            <th>Gambar</th>
                            <th style="width: 60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($iklan as $item)
                        <tr>
                            <td style="padding-left: 18px !Important;"><input type="checkbox" class="select-checkbox"></td>
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
                                    <button data-bs-toggle="modal" data-bs-target="#editIklan{{ $item->id_iklan }}"
                                        type="button" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>

                                    <!-- Modal Edit Data Iklan -->
                                    <div class="modal fade" id="editIklan{{ $item->id_iklan }}" tabindex="-1" aria-labelledby="modalEditIklanLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalEditIklanLabel">Edit Data Iklan: {{ $item->judul }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form id="formEditIklan{{ $item->id_iklan }}" action="{{ route('admin.updateIklan', $item->id_iklan ) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3" style="display: flex">
                                                            <div class="col-11">
                                                                <label for="judul" class="form-label" style="text-align: -webkit-left;">Judul Iklan</label>
                                                                <input type="text" class="form-control" id="editjudul{{ $item->id_iklan }}" name="judul" value="{{ $item->judul}}">
                                                            </div>
                                                            <div class="col align-self-end" style="display:flex; justify-content: flex-end;">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" onclick="document.getElementById('editfileInput{{ $item->id_iklan }}').click();">
                                                                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                                        <path d="M15 8h.01M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3z" />
                                                                        <path d="m3 16l5-5c.928-.893 2.072-.893 3 0l5 5" />
                                                                        <path d="m14 14l1-1c.928-.893 2.072-.893 3 0l3 3" />
                                                                    </g>
                                                                </svg>
                                                                <input type="file" id="editfileInput{{ $item->id_iklan }}" name="image" style="display: none;" accept="image/*">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label" style="color: #000000; text-align: -webkit-left;">Deskripsi</label>
                                                            <textarea name="deskripsi" id="editdeskripsi{{ $item->id_iklan }}" class="border rounded-0 form-control summernote" rows="6" placeholder="$item->id_iklan">{{ $item->deskripsi}}</textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="linkIklan" class="form-label" style="text-align: -webkit-left;">Link Iklan</label>
                                                            <textarea name="linkIklan" id="editlinkIklan{{ $item->id_iklan }}" class="border rounded-0 form-control summernote" rows="3" placeholder="$item->linkIklan">{{ $item->linkIklan}}</textarea>
                                                        </div>
                                                        <div id="editpreview{{ $item->id_iklan }}" style="display: flex; justify-content:center; cursor: pointer;" onclick="document.getElementById('editfileInput{{ $item->id_iklan }}').click();">
                                                            <img id="editimagePreview{{ $item->id_iklan }}" src="{{ $item->gambar ? 'data:image/png;base64,' . $item->gambar : asset('img/no-image.png') }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-ghost-secondary btn-pill" data-bs-dismiss="modal" id="EditbtnCancel{{ $item->id_iklan }}">Cancel</button>
                                                        <button type="submit" class="btn btn-primary btn-pill" id="EditsaveButton{{ $item->id_iklan }}">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Script Modal Edit Data Ustaz -->
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const fileInput = document.getElementById('editfileInput{{ $item->id_iklan }}');
                                            const preview = document.getElementById('editimagePreview{{ $item->id_iklan }}');
                                            const defaultImageSrc = "{{ $item->gambar ? 'data:image/png;base64,' . $item->gambar : asset('img/no-image.png') }}";

                                            fileInput.addEventListener('change', function(event) {
                                                // console.log('File input changed:', event.target.files);

                                                if (event.target.files && event.target.files[0]) {
                                                    const reader = new FileReader();

                                                    reader.onload = function(e) {
                                                        // console.log('FileReader result:', e.target.result);

                                                        preview.src = e.target.result;
                                                    };

                                                    reader.readAsDataURL(event.target.files[0]);
                                                }
                                            });

                                            const modalElement = document.getElementById('editIklan{{ $item->id_iklan }}');
                                            modalElement.addEventListener('hide.bs.modal', function() {
                                                // Reset file input
                                                fileInput.value = '';
                                                // Reset preview image
                                                preview.src = defaultImageSrc;
                                                // Reset form fields
                                                document.getElementById('formEditIklan{{ $item->id_iklan }}').reset();
                                            });
                                        });
                                    </script>

                                    <button type="button" class="btn btn-sm btn-outline-danger soft-delete-btn" data-iklan="{{ $item->id_iklan }}">
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
                const iklan = this.getAttribute('data-iklan');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Apakah Anda yakin ingin menghapus iklan?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("admin.softDeleteIklan") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    iklans: [iklan] // Pastikan `usernames` adalah array
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
<!-- Soft Delete Selected Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('bulk-delete-btn').addEventListener('click', function() {
            const selectedIklans = Array.from(document.querySelectorAll('.select-checkbox:checked'))
                .map(checkbox => checkbox.closest('tr').querySelector('.soft-delete-btn').getAttribute('data-iklan'));

            if (selectedIklans.length === 0) {
                Swal.fire('Peringatan!', 'Tidak ada item yang dipilih.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin menghapus ${selectedIklans.length} iklan?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("admin.softDeleteIklan") }}', {
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