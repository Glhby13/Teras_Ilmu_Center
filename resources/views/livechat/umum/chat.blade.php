@extends('partials.navbar')
@section('chat')

<div class="container mt-3">
    <div id="ustaz-online-section">
        <div class="online-ustaz-container mb-3">
            <h3>Ustaz Online</h3>
            <p class="mb-3">Jumlah Ustaz yang online: {{ $ustazOnline->count() }}</p>
            <div class="d-flex overflow-auto" style="max-width: 900px;">
                @if($ustazOnline->isEmpty())
                <span class="avatar rounded-circle" style="height: 55px; width: 55px">
                    <img class="avatar avatar-sm rounded-circle" src="{{ asset('img/user2.png') }}" alt=""
                        style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                </span>
                @else
                @foreach($ustazOnline as $ustaz)
                <div class="text-center me-3">
                    <span class="avatar rounded-circle" style="height: 55px; width: 55px">
                        @if ($ustaz->gambar)
                        <img class="avatar avatar-sm rounded-circle" src="data:image/png;base64,{{ $ustaz->gambar }}" alt=""
                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                        @else
                        <img class="avatar avatar-sm rounded-circle" src="{{ asset('img/user2.png') }}" alt=""
                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                        @endif
                    </span>
                    <p class="small mb-0">{{ $ustaz->nama }}</p>
                </div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-center align-items-center mb-3">
            @if($ustazOnline->isEmpty())
            <button type="button" class="btn btn-secondary" disabled>
                Maaf, fitur tidak tersedia karena tidak ada ustaz yang online saat ini
            </button>
            @else
            @if(!$hasActiveSession)
            <form action="{{ route('umum.livechatstart') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">
                    Mulai Sesi Tanya Jawab
                </button>
            </form>
            @endif
            @endif
        </div>
    </div>
    <div id="ustaz-session-button">

    </div>

    <!-- JavaScript to Refresh Ustaz Online Section -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof jQuery !== 'undefined') {
                function refreshUstazOnline() {
                    jQuery.get("{{ route('umum.ustazOnline') }}", function(data) {
                        jQuery('#ustaz-online-section').html(data.ustazOnlineHtml);
                    }).fail(function() {
                        console.error('Gagal memuat data ustaz online');
                    });
                }

                // Refresh setiap 5 detik
                setInterval(refreshUstazOnline, 1000);
            } else {
                console.error('jQuery is not loaded');
            }
        });
    </script>


    <!-- Chat Messages -->
    @if($hasActiveSession)
    <div class="chat-container" style="max-width: 900px; margin: auto; padding: 20px; background-color: ghostwhite; overflow-y: auto; height: 60vh;">
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chatContainer = document.getElementById('chat-container');
                const chatContainerHeight = chatContainer.scrollHeight;

                function fetchMessages() {
                    const livechatId = '{{ $livechat->id_livechat }}'; // Ambil ID livechat dari variabel Blade
                    fetch(`/api/umum/livechat/${livechatId}/messages`)
                        .then(response => response.json())
                        .then(data => {
                            // Update chat container dengan pesan baru
                            chatContainer.innerHTML = data.html;

                            // // Restore scroll position
                            // chatContainer.scrollTop = chatContainer.scrollHeight - chatContainerHeight;
                        })
                        .catch(error => console.error('Error fetching messages:', error));
                }

                // Fetch messages every 5 seconds
                setInterval(fetchMessages, 1000);
            });
        </script>
        <!-- Message from system -->
        <div class="d-flex mb-4">
            <div class="me-3">
                <span class="avatar rounded-circle" style="height: 55px; width: 55px">
                    <img class="avatar avatar-sm rounded-circle" src="{{ asset('img/system.png') }}" alt=""
                        style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                </span>
            </div>
            <div>
                <div class="bg-info text-white p-2 rounded">
                    <strong>System</strong>
                    <p class="mb-0">Assalamu'alaikum, ada yang bisa dibantu?</p>
                </div>
                <small class="text-muted">{{ \Carbon\Carbon::parse($livechat->created_at)->format('H:i') }}</small>
            </div>
        </div>
        <div id="chat-container">
            <!-- Loop through messages -->
            @foreach($messages as $message)
            @if($message->user->role == 'umum')
            <!-- Message from umum -->
            <div class="d-flex justify-content-end mb-4">
                <div class="me-3 text-end">
                    <div class="bg-primary text-white p-2 rounded">
                        <strong>{{ $message->user->umum->nama }}</strong>
                        <p class="mb-0 text-start">{{ $message->message }}</p>
                    </div>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}</small>
                </div>
                <div class="me-3">
                    <span class="avatar rounded-circle" style="height: 55px; width: 55px">
                        @if ($message->user->umum->gambar)
                        <img class="avatar rounded-circle"
                            src="data:image/png;base64,{{ $message->user->umum->gambar }}"
                            alt=""
                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                        @else
                        <img class="avatar rounded-circle"
                            src="{{ asset('img/user2.png') }}"
                            alt=""
                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                        @endif
                    </span>
                </div>
            </div>
            @elseif($message->user->role == 'murid')
            <!-- Message from Murid -->
            <div class="d-flex justify-content-end mb-4">
                <div class="me-3 text-end">
                    <div class="bg-primary text-white p-2 rounded">
                        <strong>{{ $message->user->murid->nama }}</strong>
                        <p class="mb-0 text-start">{{ $message->message }}</p>
                    </div>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}</small>
                </div>
                <div class="me-3">
                    <span class="avatar rounded-circle" style="height: 55px; width: 55px">
                        @if ($message->user->murid->gambar)
                        <img class="avatar rounded-circle"
                            src="data:image/png;base64,{{ $message->user->murid->gambar }}"
                            alt=""
                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                        @else
                        <img class="avatar rounded-circle"
                            src="{{ asset('img/user2.png') }}"
                            alt=""
                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                        @endif
                    </span>
                </div>
            </div>
            @elseif($message->user->role == 'ustaz')
            <!-- Message from Ustaz -->
            <div class="d-flex mb-4">
                <div class="me-3">
                    <span class="avatar rounded-circle" style="height: 55px; width: 55px">
                        @if ($message->user->ustaz->gambar)
                        <img class="avatar rounded-circle"
                            src="data:image/png;base64,{{ $message->user->ustaz->gambar }}"
                            alt=""
                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                        @else
                        <img class="avatar rounded-circle"
                            src="{{ asset('img/user2.png') }}"
                            alt=""
                            style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                        @endif
                    </span>
                </div>
                <div>
                    <div class="bg-info text-white p-2 rounded">
                        <strong>{{ $message->user->ustaz->nama }}</strong>
                        <p class="mb-0">{{ $message->message }}</p>
                    </div>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}</small>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>

    <!-- Send New Message -->
    <div class="mt-4" style="max-width: 900px; margin: auto;">
        <form id="message-form" method="POST">
            @csrf
            <input type="hidden" name="id_livechat" value="{{ $livechat->id_livechat }}">
            <div class="input-group">
                <input type="text" id="message-input" class="form-control" name="message" placeholder="Ketik pesan...">
                <button id="send-button" class="btn btn-primary" type="submit">Kirim</button>
            </div>
        </form>
    </div>
    <!-- resources/views/livechat/umum/chat.blade.php -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#message-form').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                var formData = $(this).serialize();
                var url = "{{ route('umumlivechat.send') }}";

                $.post(url, formData, function(data) {
                    // Append the new message to the chat container
                    var newMessageHtml = `
                    <div class="d-flex justify-content-end mb-4">
                        <div class="me-3 text-end">
                            <div class="bg-primary text-white p-2 rounded">
                                <strong>{{ Auth::user()->umum->nama }}</strong>
                                <p class="mb-0 text-start">${data.message}</p>
                            </div>
                            <small class="text-muted">${data.timestamp}</small>
                        </div>
                        <div class="me-3">
                            <span class="avatar rounded-circle" style="height: 55px; width: 55px">
                                <img class="avatar rounded-circle" src="{{ asset('img/user2.png') }}" alt=""
                                    style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
                            </span>
                        </div>
                    </div>
                `;
                    $('#chat-container').append(newMessageHtml);
                    $('#message-input').val(''); // Clear the input
                    $('#chat-container').scrollTop($('#chat-container')[0].scrollHeight); // Scroll to the bottom
                });
            });
        });
    </script>

    @endif
</div>





@endsection