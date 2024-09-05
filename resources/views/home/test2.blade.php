<style>
    .comment-container {
        max-height: 300px;
        /* Atur tinggi maksimal sesuai kebutuhan */
        overflow-y: auto;
        /* Tambahkan scrollbar jika diperlukan */
    }
</style>

<div class="comment-container">
    <div class="comment  rounded">
        <!-- Komentar Utama -->

        @if($info->answer->isNotEmpty())
        @foreach($info->answer->whereNull('id_parent') as $answer)
        <div class="d-flex  rounded" style="background-color: aliceblue; margin-top: 5px;">
            <div>
                <div class="align-self-center">
                    <span class="avatar rounded-circle me-3" style="height: 45px; width: 45px">
                        @if ($answer->user->ustaz)
                        @if ($answer->user->ustaz->gambar)
                        <img class="avatar rounded-circle"
                            src="data:image/png;base64,{{ $answer->user->ustaz->gambar }}"
                            alt=""
                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                        @else
                        <img class="avatar rounded-circle"
                            src="{{ asset('img/user2.png') }}"
                            alt=""
                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                        @endif
                        @elseif ($answer->user->murid)
                        @if ($answer->user->murid->gambar)
                        <img class="avatar rounded-circle"
                            src="data:image/png;base64,{{ $answer->user->murid->gambar }}"
                            alt=""
                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                        @else
                        <img class="avatar rounded-circle"
                            src="{{ asset('img/user2.png') }}"
                            alt=""
                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                        @endif
                        @else
                        <img class="avatar rounded-circle" style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%"
                            src="{{ asset('img/user2.png') }}" alt="">
                        @endif
                    </span>
                </div>
                <div>
                </div>
            </div>

            <!-- Konten Komentar -->
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                    @if ($answer->user->ustaz)
                    <span>
                        {{ $answer->user->ustaz->nama }}
                        @if ($answer->user->username === $info->username)
                        <span class="badge bg-primary ms-2">Author</span>
                        @endif
                    </span>
                    @elseif ($answer->user->murid)
                    <span>
                        {{ $answer->user->murid->nama }}
                        @if ($answer->user->username === $info->username)
                        <span class="badge bg-primary ms-2">Author</span>
                        @endif
                    </span>
                    @else
                    <span>
                        Nama tidak tersedia
                        @if ($answer->user->username === $info->username)
                        <span class="badge bg-primary ms-2">Author</span>
                        @endif
                    </span>
                    @endif
                    @php
                    $updatedAt = \Carbon\Carbon::parse($answer->updated_at);
                    $now = \Carbon\Carbon::now();

                    $isToday = $updatedAt->isToday();
                    $isYesterday = $updatedAt->isYesterday();

                    if ($isToday) {
                    $output = $updatedAt->diffForHumans();
                    } elseif ($isYesterday) {
                    $output = 'yesterday';
                    } else {
                    $output = $updatedAt->format('d M Y');
                    }
                    @endphp

                    <div class="text-muted small">
                        <a>
                            @if ($answer->created_at != $answer->updated_at)
                            (edited) {{ $output }}
                            @else
                            {{ $output }}
                            @endif
                        </a>
                    </div>

                </div>

                <p style="margin-bottom: 0px!important;">
                    {{ $answer->deskripsi}}
                </p>

                @if(!empty($answer->gambar))
                <div>
                    <img class="img-fluid" style="max-height: 70px; max-width: 100%;"
                        src="data:image/png;base64,{{ $answer->gambar }}"
                        alt="">
                </div>
                @endif

                <div class="d-flex justify-content-between align-items-center" style="margin-top: 5px;">
                    <div>
                        <!-- Ikon untuk Menampilkan Balasan -->
                        @php
                        // Cek apakah ada balasan dengan id_parent sama dengan id_answer
                        $hasReplies = $info->answer->where('id_parent', $answer->id_answer)->isNotEmpty();
                        @endphp
                        @if($hasReplies)
                        <button id="repliesButton-{{ $answer->id_answer }}" class="btn btn-link p-0 text-primary">
                            View Replies
                        </button>
                        @endif

                        <!-- Tombol Reply -->
                        <button id="replyTo-{{ $answer->id_answer }}"
                            class="btn btn-link p-0 text-primary"
                            data-username="@if ($answer->user->ustaz) {{ $answer->user->ustaz->nama }} @elseif ($answer->user->murid) {{ $answer->user->murid->nama }} @else Nama tidak tersedia @endif"
                            data-answer-id="{{ $answer->id_answer }}">
                            Reply
                        </button>
                    </div>
                    <div>
                        <div class="float-end">
                            @if ($answer->user->ustaz)

                            @if ($answer->user->username === $user->username)
                            <svg role="button" data-bs-toggle="dropdown" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                            </svg>
                            @endif

                            @elseif ($answer->user->murid)

                            @if ($answer->user->username === $user->username)
                            <svg role="button" data-bs-toggle="dropdown" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                            </svg>
                            @endif

                            @else
                            @endif

                            <div class="dropdown-menu">
                                <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editAnswerModal{{ $answer->id_answer }}">
                                    <svg role="button" xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-edit me-2" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                        <path d="M16 5l3 3"></path>
                                    </svg>
                                    Edit
                                </a>
                                <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#hapusAnswerModal{{ $answer->id_answer }}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-trash me-2" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M4 7l16 0"></path>
                                        <path d="M10 11l0 6"></path>
                                        <path d="M14 11l0 6"></path>
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                    </svg>
                                    Delete</a>
                            </div>

                            <!-- Edit Answer Modal -->
                            <div class="modal fade" id="editAnswerModal{{ $answer->id_answer }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form id="editAnswerForm{{ $answer->id_answer }}" action="{{ route(auth()->user()->role . '.editanswer', $answer->id_answer ) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <b>Edit Answer</b>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3 position-relative">
                                                    <label class="form-label" style="color: #000000;">Deskripsi</label>
                                                    <textarea name="deskripsi" class="border rounded-0 form-control summernote" rows="6" placeholder="{{ $answer->deskripsi}}">{{ $answer->deskripsi}}</textarea>
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
                                    const modalElement = document.getElementById('editAnswerModal{{ $answer->id_answer }}');
                                    modalElement.addEventListener('hide.bs.modal', function() {
                                        // Reset form fields
                                        document.getElementById('editAnswerForm{{ $answer->id_answer }}').reset();
                                    });
                                });
                            </script>

                            <!-- Delete Question modal -->
                            <div class="modal fade" id="hapusAnswerModal{{ $answer->id_answer }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah Anda yakin ingin menghapus balasan?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form id="answer-form" action="{{ route(auth()->user()->role . '.hapusanswer', $answer->id_answer ) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">Yes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Balasan -->
                <div id="repliesSection-{{ $answer->id_answer }}" class="reply" style="display: none; margin-top: 5px">
                    @foreach($info->answer->where('id_parent', $answer->id_answer) as $reply)
                    @include('home.reply')
                    @endforeach
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var repliesButton = document.getElementById('repliesButton-{{ $answer->id_answer }}');
                        var repliesSection = document.getElementById('repliesSection-{{ $answer->id_answer }}');
                        var replyToButton = document.getElementById('replyTo-{{ $answer->id_answer }}');

                        if (repliesButton && repliesSection) {
                            repliesButton.addEventListener('click', function() {
                                if (repliesSection.style.display === 'none' || repliesSection.style.display === '') {
                                    repliesSection.style.display = 'block';
                                    this.textContent = 'Hide Replies';
                                } else {
                                    repliesSection.style.display = 'none';
                                    this.textContent = 'View Replies';
                                }
                            });
                        }

                        if (replyToButton) {
                            replyToButton.addEventListener('click', function() {
                                var username = this.getAttribute('data-username');
                                var answerId = this.getAttribute('data-answer-id');
                                document.getElementById('parent-id-{{ $answer->id_question }}').value = answerId;

                                document.getElementById('judulForm-{{ $answer->id_question }}').textContent = 'Reply to ' + username;
                            });
                        }
                    });
                </script>
            </div>
        </div>
        @endforeach
        @endif



    </div>
</div>

<!-- @if($info->answer->count() > 4)
<div class="text-center mt-2">
    <button id="viewMoreComments" class="btn btn-link text-primary">View More</button>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var viewMoreButton = document.getElementById('viewMoreComments');
        if (viewMoreButton) {
            viewMoreButton.addEventListener('click', function() {
                // Logika untuk menampilkan lebih banyak komentar
                // Misalnya, Anda bisa memuat komentar tambahan dari server
                this.style.display = 'none'; // Sembunyikan tombol setelah diklik
            });
        }
    });
</script> -->

<!-- Form untuk Menambahkan Komentar Baru atau Reply -->
<div class="add-comment p-3 border rounded" style="margin-top: 10px;">
    <h5 id="judulForm-{{ $info->id_question }}">Add an Answer</h5>
    <form id="answerForm-{{ $info->id_question }}" action="{{ route(auth()->user()->role . '.createanswer', $info->id_question ) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id_question" value="{{ $info->id_question }}">
        <!-- Hidden input for reply id -->
        <input type="hidden" id="parent-id-{{ $info->id_question }}" name="id_parent" value="">
        <input type="hidden" id="To-{{ $info->id_question }}" name="replyTo" value="">
        <div class="d-flex align-items-start mb-3">
            <!-- Avatar Placeholder untuk Pengguna -->
            <div>
                <span class="avatar rounded-circle">
                    @if ($data->gambar)
                    <img class="avatar rounded-circle" style="background-color: #DBE7F9; object-fit: cover;"
                        src="data:image/png;base64,{{ $data->gambar }}" alt="">
                    @else
                    <img class="avatar rounded-circle" style="background-color: #DBE7F9; object-fit: cover;"
                        src="{{ asset('img/user2.png') }}" alt="">
                    @endif
                </span>
            </div>

            <!-- Input Komentar -->
            <div class="flex-grow-1" style="margin-left: 10px;">
                <textarea name="deskripsi" class="border rounded-0 form-control" rows="1" placeholder="Write your comment here..." required></textarea>
            </div>

            <div>
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" onclick="document.getElementById('answerfileInput-{{ $info->id_question }}').click();">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1">
                        <path d="M15 8h.01M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3z" />
                        <path d="m3 16l5-5c.928-.893 2.072-.893 3 0l5 5" />
                        <path d="m14 14l1-1c.928-.893 2.072-.893 3 0l3 3" />
                    </g>
                </svg>
                <input type="file" id="answerfileInput-{{ $info->id_question }}" name="image" style="display: none;" accept="image/*" onchange="previewAnswerImage(event, '{{ $info->id_question }}')">
            </div>
        </div>

        <div class="d-flex align-items-start mb-3">
            <div>
                <span class="avatar rounded-circle" style="background-color: unset; box-shadow: none;"></span>
            </div>
            <div class="flex-grow-1" id="answerImagepreview-{{ $info->id_question }}" style="margin-left: 10px; cursor: pointer;"
                onclick="document.getElementById('answerfileInput-{{ $info->id_question }}').click();">
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="button" id="cancelReply-{{ $info->id_question }}" class="btn btn-ghost-secondary btn-pill btn-sm">Cancel</button>
            <button type="submit" class="btn btn-primary btn-pill btn-sm">Save</button>
        </div>
    </form>
</div>

<script>
    // Fungsi untuk menampilkan pratinjau gambar
    function previewAnswerImage(event, questionId) {
        const preview = document.getElementById('answerImagepreview-' + questionId);
        preview.innerHTML = ''; // Kosongkan preview sebelumnya

        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100%';
                img.style.maxHeight = '100px'; // Sesuaikan tinggi maksimum jika perlu
                preview.appendChild(img);
            };

            reader.readAsDataURL(file);
        }
    }

    // Fungsi untuk mereset modal
    function resetAnswerImage(questionId) {
        const form = document.getElementById('answerForm-' + questionId);
        form.reset(); // Mereset form

        // Kosongkan pratinjau gambar
        const preview = document.getElementById('answerImagepreview-' + questionId);
        preview.innerHTML = '';
    }

    // Event listener untuk modal
    document.querySelectorAll('[id^="cancelReply-"]').forEach(button => {
        button.addEventListener('click', function() {
            const questionId = this.id.replace('cancelReply-', '');
            resetAnswerImage(questionId);

            const form = document.getElementById('answerForm-' + questionId);
            const parentIdInput = document.getElementById('parent-id-' + questionId);
            const ToInput = document.getElementById('To-' + questionId);
            const formTitle = document.getElementById('judulForm-' + questionId);

            // Reset form fields
            form.reset();

            // Reset hidden reply-id input
            parentIdInput.value = '';

            // Reset hidden name input
            ToInput.value = '';

            // Reset form title
            formTitle.textContent = 'Add an Answer';
        });
    });
</script>