@extends('admin.navbar')
@section('report')

<style>
    /* Tambahkan CSS di sini */
    #reportTable th:first-child.sorting_asc,
    #reportTable th:first-child.sorting_desc,
    #reportTable th:first-child.sorting {
        background-image: none !important;
        cursor: default !important;
    }

    /* Menambahkan jarak antara Show entries/Search dan tabel */
    #reportTable_wrapper .dataTables_filter {
        margin-bottom: 20px;
        /* Jarak antara Search dan tabel */
    }

    /* Menambahkan border pada tabel */
    #reportTable {
        border: 1px solid #dee2e6;
    }

    #reportTable th,
    #reportTable td {
        border: 1px solid #dee2e6;
    }

    /* Menambahkan style untuk tombol yang disable */
    .action-disabled button {
        pointer-events: none;
        opacity: 0.5;
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

    .image-container {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* Menjaga gambar tetap dalam rasio kotak */
    }

    .caption {
        white-space: pre-wrap;
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
    <div class="card mt-2">
        <div class="card-body">
            <h2>Data Report</h2>
            <div class="table-responsive">
                <table id="reportTable" class="table table-hover table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 13px;"><input type="checkbox" id="select-all"></th>
                            <th>Nama Pelapor</th>
                            <th>Alasan</th>
                            <th>Posting</th>
                            <th style="width: 60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                        <!-- Data dummy -->
                        <tr>
                            <td style="padding-left: 18px !Important;"><input type="checkbox" class="select-checkbox"></td>
                            <td>
                                @if ($report->user->role === 'murid' && $report->user->murid)
                                {{ $report->user->murid->nama }}
                                @elseif ($report->user->role === 'ustaz' && $report->user->ustaz)
                                {{ $report->user->ustaz->nama }}
                                @elseif ($report->user->role === 'umum' && $report->user->umum)
                                {{ $report->user->umum->nama }}
                                @else
                                {{ $report->username }} <!-- Tampilkan username jika tidak ada role yang sesuai -->
                                @endif
                            </td>
                            <td>{{ $report->deskripsi }}</td>
                            <td>
                                <div class="view-buttons" style="text-align: center;">
                                    <button data-bs-toggle="modal" data-bs-target="#lihatPost{{ $report->id_question }}"
                                        type="button" class="btn btn-sm btn-outline-success">
                                        Lihat Post
                                    </button>
                                </div>

                                <!-- Modal Lihat Post -->
                                <div class="modal fade" id="lihatPost{{ $report->id_question }}" tabindex="-1" aria-labelledby="modalLihatPostLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLihatPostLabel">Post by:
                                                    @if ($report->question->user->role === 'murid' && $report->question->user->murid)
                                                    {{ $report->question->user->murid->nama }}
                                                    @elseif ($report->question->user->role === 'ustaz' && $report->question->user->ustaz)
                                                    {{ $report->question->user->ustaz->nama }}
                                                    @elseif ($report->question->user->role === 'umum' && $report->question->user->umum)
                                                    {{ $report->question->user->umum->nama }}
                                                    @else
                                                    {{ $report->question->username }} <!-- Tampilkan username jika tidak ada role yang sesuai -->
                                                    @endif
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div>
                                                    <div class="card mt-2 mb-3">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-1" style="align-content: top;">
                                                                    <span class="avatar rounded-circle" style="height: 55px; width: 55px">
                                                                        @if ($report->question->user->ustaz)
                                                                        @if ($report->question->user->ustaz->gambar)
                                                                        <img class="avatar rounded-circle"
                                                                            src="data:image/png;base64,{{ $report->question->user->ustaz->gambar }}"
                                                                            alt=""
                                                                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                                                                        @else
                                                                        <img class="avatar rounded-circle"
                                                                            src="{{ asset('img/user2.png') }}"
                                                                            alt=""
                                                                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                                                                        @endif
                                                                        @elseif ($report->question->user->murid)
                                                                        @if ($report->question->user->murid->gambar)
                                                                        <img class="avatar rounded-circle"
                                                                            src="data:image/png;base64,{{ $report->question->user->murid->gambar }}"
                                                                            alt=""
                                                                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                                                                        @else
                                                                        <img class="avatar rounded-circle"
                                                                            src="{{ asset('img/user2.png') }}"
                                                                            alt=""
                                                                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                                                                        @endif
                                                                        @elseif ($report->question->user->umum)
                                                                        @if ($report->question->user->umum->gambar)
                                                                        <img class="avatar rounded-circle"
                                                                            src="data:image/png;base64,{{ $report->question->user->umum->gambar }}"
                                                                            alt=""
                                                                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                                                                        @else
                                                                        <img class="avatar rounded-circle"
                                                                            src="{{ asset('img/user2.png') }}"
                                                                            alt=""
                                                                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                                                                        @endif
                                                                        @else
                                                                        <img class="avatar rounded-circle"
                                                                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%"
                                                                            src="{{ asset('img/user2.png') }}" alt="">
                                                                        @endif
                                                                    </span>

                                                                </div>
                                                                <div class="col-11" style="padding-left:25px">
                                                                    <a class="username no-underline" href="#">
                                                                        <b>
                                                                            @if ($report->question->user->ustaz)
                                                                            <span>{{ $report->question->user->ustaz->nama }}</span>
                                                                            @elseif ($report->question->user->murid)
                                                                            <span>{{ $report->question->user->murid->nama }}</span>
                                                                            @elseif ($report->question->user->umum)
                                                                            <span>{{ $report->question->user->umum->nama }}</span>
                                                                            @else
                                                                            <span>Nama tidak tersedia</span>
                                                                            @endif
                                                                        </b>

                                                                    </a>

                                                                    <div class="text-secondary" style="display: flex; justify-content: space-between; align-items: center;">
                                                                        <div>
                                                                            <a>Role: {{ ucwords($report->question->user->role) }} | </a>
                                                                            <a>
                                                                                @if ($report->question->user->ustaz)
                                                                                <span>{{ $report->question->user->ustaz->alamat }}</span>
                                                                                @elseif ($report->question->user->murid)
                                                                                <span>{{ $report->question->user->murid->alamat }}</span>
                                                                                @elseif ($report->question->user->umum)
                                                                                <span>{{ $report->question->user->umum->alamat }}</span>
                                                                                @else
                                                                                <span>Alamat tidak tersedia</span>
                                                                                @endif
                                                                            </a>
                                                                        </div>

                                                                        @php
                                                                        // Mengimpor Carbon tanpa "use"
                                                                        $updatedAt = \Carbon\Carbon::parse($report->question->updated_at);
                                                                        $now = \Carbon\Carbon::now();

                                                                        // Tentukan apakah waktu pembaruan adalah hari ini atau sebelumnya
                                                                        $isToday = $updatedAt->isToday();
                                                                        $isYesterday = $updatedAt->isYesterday();

                                                                        // Format output
                                                                        $output = '';

                                                                        if ($isToday) {
                                                                        // Tampilkan dalam format relatif
                                                                        $output = $updatedAt->diffForHumans();
                                                                        } elseif ($isYesterday) {
                                                                        // Tampilkan 'yesterday' untuk pembaruan kemarin
                                                                        $output = 'yesterday';
                                                                        } else {
                                                                        // Tampilkan tanggal jika lebih dari 1 hari
                                                                        $output = $updatedAt->format('d M Y');
                                                                        }
                                                                        @endphp

                                                                        <div>
                                                                            <a>
                                                                                @if ($report->question->created_at != $report->question->updated_at)
                                                                                (edited) {{ $output }}
                                                                                @else
                                                                                {{ $output }}
                                                                                @endif
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <a>
                                                                        Kategori: {{ $report->question->kategori }}
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <!-- untuk photo/caption -->
                                                            <div class="mt-3" style="margin-top: 10px!important;">
                                                                <p class="caption">{{ $report->question->deskripsi }}</p>
                                                                <a>
                                                                    <!-- onclick="console.log('Modal Triggered! ID:', '{{ $report->question->id_question }}')" -->
                                                                    <div class="image-container">
                                                                        @if ($report->question->gambar)
                                                                        <img src="data:image/png;base64,{{ $report->question->gambar }}" class="img-fluid"
                                                                            style="max-width: 100%; height: auto;">
                                                                        @else
                                                                        <img src="{{ asset('img/no-image.png') }}" class="img-fluid"
                                                                            style="max-width: 100%; height: auto;">
                                                                        @endif
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>

                            <td>
                                <div class="action-buttons" style="text-align: center;">
                                    <button type="button" class="btn btn-sm btn-outline-warning hapusPost-btn" data-report="{{ $report->id_report }}">
                                        Hapus post
                                    </button>

                                    <button type="button" class="btn btn-sm btn-outline-danger hapusReport-btn" data-report="{{ $report->id_report }}">
                                        Hapus laporan
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
                <!-- <div class="p-2 bd-highlight">
                    <button type="button" id="selected-hapusPost-btn" class="btn btn-warning" disabled>
                        <i class="fas fa-trash" style="margin-right: 5px;"></i> Hapus Post
                    </button>
                </div> -->
                <div class="p-2 bd-highlight">
                    <button type="button" id="selected-hapusReport-btn" class="btn btn-danger" disabled>
                        <i class="fas fa-trash" style="margin-right: 5px;"></i> Hapus Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hapus Post Invididual Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle individual restore
        document.querySelectorAll('.hapusPost-btn').forEach(button => {
            button.addEventListener('click', function() {
                const report = this.getAttribute('data-report');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Apakah Anda yakin ingin menghapus post secara permanen?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("admin.reportDeletePost") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    report: report
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Dihapus!', 'Data post telah dihapus.', 'success')
                                        .then(() => {
                                            setTimeout(() => {
                                                location.reload();
                                            }, 100);
                                        });
                                } else {
                                    Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data post.', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data post.', 'error');
                            });
                    }
                });
            });
        });
    });
</script>

<!-- Hapus Post Selected Script -->
<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('selected-hapusPost-btn').addEventListener('click', function() {
            const selectedReports = Array.from(document.querySelectorAll('.select-checkbox:checked'))
                .map(checkbox => checkbox.closest('tr').querySelector('.hapusPost-btn').getAttribute('data-report'));

            if (selectedReports.length === 0) {
                Swal.fire('Peringatan!', 'Tidak ada item yang dipilih.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin menghapus ${selectedReports.length} post secara permanen?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("admin.reportDeletePost") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                reports: selectedReports
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Dihapus!', 'Data post telah dihapus.', 'success')
                                    .then(() => {
                                        setTimeout(() => {
                                            location.reload();
                                        }, 100);
                                    });
                            } else {
                                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data post.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data post.', 'error');
                        });
                }
            });
        });
    });
</script> -->


<!-- Hapus Report Invididual Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle individual restore
        document.querySelectorAll('.hapusReport-btn').forEach(button => {
            button.addEventListener('click', function() {
                const report = this.getAttribute('data-report');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Apakah Anda yakin ingin menghapus report secara permanen?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("admin.reportDeleteReport") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    reports: [report] // Pastikan `reports` adalah array
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Dihapus!', 'Data report telah dihapus.', 'success')
                                        .then(() => {
                                            setTimeout(() => {
                                                location.reload();
                                            }, 100);
                                        });
                                } else {
                                    Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data report.', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data report.', 'error');
                            });
                    }
                });
            });
        });
    });
</script>

<!-- Hapus Report Selected Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('selected-hapusReport-btn').addEventListener('click', function() {
            const selectedReports = Array.from(document.querySelectorAll('.select-checkbox:checked'))
                .map(checkbox => checkbox.closest('tr').querySelector('.hapusReport-btn').getAttribute('data-report'));

            if (selectedReports.length === 0) {
                Swal.fire('Peringatan!', 'Tidak ada item yang dipilih.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin menghapus ${selectedReports.length} report secara permanen?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("admin.reportDeleteReport") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                reports: selectedReports
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Dihapus!', 'Data report telah dihapus.', 'success')
                                    .then(() => {
                                        setTimeout(() => {
                                            location.reload();
                                        }, 100);
                                    });
                            } else {
                                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data report.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data report.', 'error');
                        });
                }
            });
        });
    });
</script>


<!-- Table Script -->
<script>
    $(document).ready(function() {
        var table = $('#reportTable').DataTable({
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            columnDefs: [{
                    orderable: false,
                    targets: [0, 2, 3, 4]
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

            // Disable/Enable delete post button based on selected items
            $('#selected-hapusPost-btn').prop('disabled', checkedCheckboxes === 0);

            // Disable/Enable delete report button based on selected items
            $('#selected-hapusReport-btn').prop('disabled', checkedCheckboxes === 0);

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