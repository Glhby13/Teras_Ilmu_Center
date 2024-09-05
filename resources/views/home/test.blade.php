<div class class="comment mb-4 p-3 border rounded">
    <!-- Komentar Utama -->
    <div x-data="{ showReplies: false, replyingTo: null }">
        @if($info->answer->isNotEmpty())

        <!-- @php
        $reply = $info->answer->whereNotNull('id_parent');
        @endphp -->

        <!-- Debugging Output -->
        <!-- <script>
            console.log('Answer ID:', @json($reply));
        </script> -->

        <!-- Tampilkan komentar dan balasan jika ada -->
        @foreach($info->answer->whereNull('id_parent') as $answer)
        <div class="d-flex">
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
                    <span>{{ $answer->user->ustaz->nama }}</span>
                    @elseif ($answer->user->murid)
                    <span>{{ $answer->user->murid->nama }}</span>
                    @else
                    <span>Nama tidak tersedia</span>
                    @endif
                    <span class="text-muted small">2 hours ago</span>
                </div>
                <p style="margin-bottom: 5px!important;">
                    {{ $answer->deskripsi}}
                </p>

                <!-- Ikon untuk Menampilkan Balasan -->
                @php
                // Cek apakah ada balasan dengan id_parent sama dengan id_answer
                $hasReplies = $info->answer->where('id_parent', $answer->id_answer)->isNotEmpty();
                @endphp
                @if($hasReplies)
                <button @click="showReplies = !showReplies" class="btn btn-link p-0 text-primary">
                    <span x-text="showReplies ? 'Hide Replies' : 'View Replies'"></span>
                </button>
                @endif

                <!-- Tombol Reply -->
                <button
                    id="reply-btn-{{ $answer->id_answer }}"
                    @click="replyingTo = '{{ $answer->user->ustaz ? $answer->user->ustaz->nama : ($answer->user->murid ? $answer->user->murid->nama : 'Unknown') }}'; setReplyId({{ $answer->id_answer }})"
                    class="btn btn-link p-0 text-primary">
                    Reply
                </button>



                <!-- Balasan -->
                <div x-show="showReplies" class="reply mt-3 ms-4">
                    @foreach($info->answer->where('id_parent', $answer->id_answer) as $reply)
                    @include('home.reply')
                    @endforeach

                </div>

                <div>
                    <p></p>
                </div>
            </div>
        </div>
        @endforeach

        @endif

        <!-- Form untuk Menambahkan Komentar Baru atau Reply -->
        <div x-show="replyingTo !== null || replyingTo === null" class="add-comment mt-4 p-3 border rounded">
            <h5 x-text="replyingTo === null ? 'Add a Comment' : 'Reply to ' + replyingTo"></h5>
            <form action="{{ route(auth()->user()->role . '.createanswer', $info->id_question ) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_question" value="{{ $info->id_question }}">
                <!-- Hidden input for reply id -->
                <input type="hidden" id="reply-id" name="id_parent" value="">
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1">
                                <path d="M15 8h.01M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3z" />
                                <path d="m3 16l5-5c.928-.893 2.072-.893 3 0l5 5" />
                                <path d="m14 14l1-1c.928-.893 2.072-.893 3 0l3 3" />
                            </g>
                        </svg>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" @click="replyingTo = null" class="btn btn-ghost-secondary btn-pill btn-sm" id="cancel-reply">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-pill btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk mengatur ID balasan
    function setReplyId(id) {
        document.getElementById('reply-id').value = id;
    }

    // Fungsi untuk mengatur nilai hidden input menjadi null
    function clearReplyId() {
        document.getElementById('reply-id').value = '';
    }

    // Menambahkan event listener untuk tombol Cancel
    document.getElementById('cancel-reply').addEventListener('click', clearReplyId);
</script>