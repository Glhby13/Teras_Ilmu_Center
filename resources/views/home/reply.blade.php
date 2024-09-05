<div class="d-flex">
    <div>
        <div class="align-self-center">
            <span class="avatar rounded-circle me-3" style="height: 45px; width: 45px">
                @if ($reply->user->ustaz)
                @if ($reply->user->ustaz->gambar)
                <img class="avatar rounded-circle"
                    src="data:image/png;base64,{{ $reply->user->ustaz->gambar }}"
                    alt=""
                    style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                @else
                <img class="avatar rounded-circle"
                    src="{{ asset('img/user2.png') }}"
                    alt=""
                    style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                @endif
                @elseif ($reply->user->murid)
                @if ($reply->user->murid->gambar)
                <img class="avatar rounded-circle"
                    src="data:image/png;base64,{{ $reply->user->murid->gambar }}"
                    alt=""
                    style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                @else
                <img class="avatar rounded-circle"
                    src="{{ asset('img/user2.png') }}"
                    alt=""
                    style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                @endif
                @elseif ($reply->user->umum)
                @if ($reply->user->umum->gambar)
                <img class="avatar rounded-circle"
                    src="data:image/png;base64,{{ $reply->user->umum->gambar }}"
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
            @if ($reply->user->ustaz || $reply->user->murid || $reply->user->umum)
            <span>
                {{-- Tampilkan Nama User Berdasarkan Peran --}}
                @if ($reply->user->ustaz)
                {{ $reply->user->ustaz->nama }}
                @elseif ($reply->user->murid)
                {{ $reply->user->murid->nama }}
                @elseif ($reply->user->umum)
                {{ $reply->user->umum->nama }}
                @endif

                {{-- Tampilkan Badge Author jika User adalah Author --}}
                @if ($reply->user->username === $info->username)
                <span class="badge bg-primary">Author</span>
                @endif

                {{-- Tampilkan Panah dan Nama ReplyTo User jika Ada --}}
                @if ($reply->replyTo)
                <img src="{{ asset('img/right-arrow-svgrepo-com.png') }}" alt=""
                    style="background-color: unset; object-fit: cover; height: 12px; margin-bottom: 3px;">

                @if ($reply->replyTouser->ustaz)
                {{ $reply->replyTouser->ustaz->nama }}
                @elseif ($reply->replyTouser->murid)
                {{ $reply->replyTouser->murid->nama }}
                @elseif ($reply->replyTouser->umum)
                {{ $reply->replyTouser->umum->nama }}
                @endif

                @if ($reply->replyTouser->username === $info->username)
                <span class="badge bg-primary">Author</span>
                @endif
                @endif
            </span>
            @else
            <span>Nama tidak tersedia</span>
            @endif

            {{-- Tampilkan Waktu Update atau Created At --}}
            @php
            $updatedAt = \Carbon\Carbon::parse($reply->updated_at);
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
                    @if ($reply->created_at != $reply->updated_at)
                    (edited) {{ $output }}
                    @else
                    {{ $output }}
                    @endif
                </a>
            </div>
        </div>


        <p style="margin-bottom: 5px!important;">
            {{ $reply->deskripsi}}
        </p>

        @if(!empty($reply->gambar))
        <div>
            <img class="img-fluid" style="max-height: 70px; max-width: 100%;"
                src="data:image/png;base64,{{ $reply->gambar }}"
                alt=""
                data-bs-toggle="modal" data-bs-target="#replyimageModal{{ $reply->id_answer }}">
        </div>
        <!-- Modal -->
        <div class="modal fade" id="replyimageModal{{ $reply->id_answer }}" tabindex="-1" role="dialog"
            aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 85vh!important;">
                <div class="modal-content" style="background-color: unset; border: none;">
                    <div class="modal-body">
                        @if ($info->gambar)
                        <img src="data:image/png;base64,{{ $reply->gambar }}" class="img-fluid"
                            style="min-width: 100%; height: auto;">
                        @else
                        <img src="{{ asset('img/no-image.png') }}" class="img-fluid"
                            style="max-width: 100%; height: auto;">
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @endif

        <div class="d-flex justify-content-between align-items-center">
            <div>
                <!-- Tombol Reply -->
                <button id="replyToreply-{{ $reply->id_answer }}"
                    class="btn btn-link p-0 text-primary"
                    data-username="
        @if ($reply->user->ustaz) 
            {{ $reply->user->ustaz->nama }} 
        @elseif ($reply->user->murid) 
            {{ $reply->user->murid->nama }} 
        @elseif ($reply->user->umum) 
            {{ $reply->user->umum->nama }} 
        @else 
            Nama tidak tersedia 
        @endif"
                    data-answer-id="{{ $answer->id_answer }}"
                    name-data="
        @if ($reply->user->ustaz) 
            {{ $reply->user->ustaz->username }} 
        @elseif ($reply->user->murid) 
            {{ $reply->user->murid->username }} 
        @elseif ($reply->user->umum) 
            {{ $reply->user->umum->username }} 
        @else 
            Nama tidak tersedia 
        @endif">
                    Reply
                </button>

            </div>

            <div>
                <div class="float-end">
                    @if ($reply->user->ustaz)

                    @if ($reply->user->username === $user->username)
                    <svg role="button" data-bs-toggle="dropdown" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                    </svg>
                    @endif

                    @elseif ($reply->user->murid)

                    @if ($reply->user->username === $user->username)
                    <svg role="button" data-bs-toggle="dropdown" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                    </svg>
                    @endif

                    @elseif ($reply->user->umum)

                    @if ($reply->user->username === $user->username)
                    <svg role="button" data-bs-toggle="dropdown" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                    </svg>
                    @endif

                    @else
                    Nama tidak tersedia
                    @endif


                    <div class="dropdown-menu">
                        <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editReplyModal{{ $reply->id_answer }}">
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
                        <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#hapusReplyModal{{ $reply->id_answer }}">
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

                    <!-- Edit Reply Modal -->
                    <div class="modal fade" id="editReplyModal{{ $reply->id_answer }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <form id="editReplyForm{{ $reply->id_answer }}" action="{{ route(auth()->user()->role . '.editanswer', $reply->id_answer ) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <b>Edit Reply</b>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3 position-relative">
                                            <label class="form-label" style="color: #000000;">Deskripsi</label>
                                            <textarea id="editReplyDeskripsi{{ $reply->id_answer }}" name="deskripsi" class="border rounded-0 form-control summernote" rows="6" placeholder="{{ $reply->deskripsi }}">{{ $reply->deskripsi }}</textarea>
                                        </div>
                                        <div class="mb-3 position-relative">
                                            <label class="form-label" style="color: #000000;">Gambar</label>
                                            <div id="editReplyImageContainer{{ $reply->id_answer }}">
                                                @if (!empty($reply->gambar))
                                                <!-- Jika Gambar Sudah Ada -->
                                                <div class="d-flex justify-content-center mb-3" onclick="document.getElementById('editReplyFileInput-{{ $reply->id_answer }}').click();">
                                                    <img id="editReplyImagePreview{{ $reply->id_answer }}" src="data:image/png;base64,{{ $reply->gambar }}" style="max-width: 100%; cursor: pointer;" alt="Gambar Jawaban">
                                                </div>
                                                <button type="button" id="deleteReplyImageButton{{ $reply->id_answer }}" class="btn btn-danger btn-sm mb-3">Hapus Gambar</button>
                                                @else
                                                <!-- Jika Gambar Kosong -->
                                                <div class="d-flex justify-content-start mb-3" onclick="document.getElementById('editReplyFileInput-{{ $reply->id_answer }}').click();">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                                                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1">
                                                            <path d="M15 8h.01M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3z" />
                                                            <path d="m3 16l5-5c.928-.893 2.072-.893 3 0l5 5" />
                                                            <path d="m14 14l1-1c.928-.893 2.072-.893 3 0l3 3" />
                                                        </g>
                                                    </svg>
                                                </div>
                                                @endif
                                            </div>
                                            <input type="file" id="editReplyFileInput-{{ $reply->id_answer }}" name="image" style="display: none;" accept="image/*" onchange="editPreviewReplyImage(event, '{{ $reply->id_answer }}')">
                                            <input type="hidden" id="editReplyActionInput{{ $reply->id_answer }}" name="action" value="">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-ghost-secondary btn-pill" data-bs-dismiss="modal" id="editCancelReplyButton{{ $reply->id_answer }}">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-pill">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const replyId = '{{ $reply->id_answer }}';
                            const replyfileInput = document.getElementById('editReplyFileInput-' + replyId);
                            const replypreviewContainer = document.getElementById('editReplyImageContainer' + replyId);
                            const replydeskripsiInput = document.getElementById('editReplyDeskripsi' + replyId);
                            const replydeleteButton = document.getElementById('deleteReplyImageButton' + replyId);
                            const replymodalElement = document.getElementById('editReplyModal' + replyId);
                            const replyActionInput = document.getElementById('editReplyActionInput' + replyId);

                            // Simpan nilai default
                            const defaultDeskripsi = replydeskripsiInput.value;
                            const defaultImageSrc = replypreviewContainer.querySelector('img') ? replypreviewContainer.querySelector('img').src : '';

                            // Reset form saat modal ditutup
                            replymodalElement.addEventListener('hide.bs.modal', function() {
                                replydeskripsiInput.value = defaultDeskripsi;
                                if (defaultImageSrc) {
                                    replypreviewContainer.innerHTML = `
                    <div class="d-flex justify-content-center mb-3" onclick="document.getElementById('editReplyFileInput-${replyId}').click();">
                        <img id="editReplyImagePreview${replyId}" src="${defaultImageSrc}" style="max-width: 100%; cursor: pointer;" alt="Gambar Jawaban">
                    </div>
                    <button type="button" id="deleteReplyImageButton${replyId}" class="btn btn-danger btn-sm mb-3">Hapus Gambar</button>
                `;
                                } else {
                                    replypreviewContainer.innerHTML = `
                    <div class="d-flex justify-content-start mb-3" onclick="document.getElementById('editReplyFileInput-${replyId}').click();">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1">
                                <path d="M15 8h.01M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3z" />
                                <path d="m3 16l5-5c.928-.893 2.072-.893 3 0l5 5" />
                                <path d="m14 14l1-1c.928-.893 2.072-.893 3 0l3 3" />
                            </g>
                        </svg>
                    </div>
                `;
                                }
                                replyfileInput.value = '';
                            });

                            if (replydeleteButton) {
                                replydeleteButton.addEventListener('click', function() {
                                    deleteReplyImagePreview(replyId);
                                });
                            }
                        });

                        // Fungsi untuk pratinjau gambar yang baru diunggah
                        function editPreviewReplyImage(event, replyId) {
                            const file = event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    const replypreviewContainer = document.getElementById('editReplyImageContainer' + replyId);
                                    replypreviewContainer.innerHTML = `
                    <div class="d-flex justify-content-center mb-3" onclick="document.getElementById('editReplyFileInput-${replyId}').click();">
                        <img id="editReplyImagePreview${replyId}" src="${e.target.result}" style="max-width: 100%; cursor: pointer;" alt="Gambar Jawaban">
                    </div>
                    <button type="button" id="deleteReplyImageButton${replyId}" class="btn btn-danger btn-sm mb-3">Hapus Gambar</button>
                `;
                                    document.getElementById('deleteReplyImageButton' + replyId).addEventListener('click', function() {
                                        deleteReplyImagePreview(replyId);
                                    });
                                };
                                reader.readAsDataURL(file);
                            }
                        }

                        // Fungsi untuk menghapus gambar dari pratinjau dan mengirimkan nilai untuk menghapus gambar
                        function deleteReplyImagePreview(replyId) {
                            const replypreviewContainer = document.getElementById('editReplyImageContainer' + replyId);
                            const replyActionInput = document.getElementById('editReplyActionInput' + replyId);
                            replypreviewContainer.innerHTML = `
            <div class="d-flex justify-content-start mb-3" onclick="document.getElementById('editReplyFileInput-${replyId}').click();">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1">
                        <path d="M15 8h.01M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3z" />
                        <path d="m3 16l5-5c.928-.893 2.072-.893 3 0l5 5" />
                        <path d="m14 14l1-1c.928-.893 2.072-.893 3 0l3 3" />
                    </g>
                </svg>
            </div>
        `;
                            document.getElementById('editReplyFileInput-' + replyId).value = '';
                            replyActionInput.value = 'delete_image';
                        }
                    </script>


                    <!-- Delete Question modal -->
                    <div class="modal fade" id="hapusReplyModal{{ $reply->id_answer }}" tabindex="-1" aria-hidden="true">
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
                                    <form id="reply-form" action="{{ route(auth()->user()->role . '.hapusanswer', $reply->id_answer ) }}" method="POST" class="d-inline">
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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var replyToreplyButton = document.getElementById('replyToreply-{{ $reply->id_answer }}');

                if (replyToreplyButton) {
                    replyToreplyButton.addEventListener('click', function() {
                        var username = this.getAttribute('data-username');
                        var answerId = this.getAttribute('data-answer-id');
                        var name = this.getAttribute('name-data');
                        document.getElementById('parent-id-{{ $answer->id_question }}').value = answerId;
                        document.getElementById('To-{{ $answer->id_question }}').value = name;
                        document.getElementById('judulForm-{{ $answer->id_question }}').textContent = 'Reply to ' + username;
                    });
                }
            });
        </script>

    </div>
</div>