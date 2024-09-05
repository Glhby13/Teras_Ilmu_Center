@extends('partials.navbar')
@section('chatUstaz')


<div class="container mt-3">
    <!-- Ustaz Online Section -->
    <a href="{{ route(auth()->user()->role . '.livechat') }}"
        class="btn-item btn btn-outline-info border-info {{ request()->routeIs(auth()->user()->role . '.livechat') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round"
            class="icon icon-tabler icon-tabler-message" style="margin: 0;">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M8 9h8" />
            <path d="M8 13h6" />
            <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
        </svg>
    </a>
</div>


<!-- Chat Messages -->
<div class="chat-container" style="max-width: 900px; margin: auto; padding: 20px; background-color: ghostwhite; overflow-y: auto; height: 60vh;">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatContainer = document.getElementById('chat-container');
            const chatContainerHeight = chatContainer.scrollHeight;

            function fetchMessages() {
                const livechatId = '{{ $livechat->id_livechat }}'; // Ambil ID livechat dari variabel Blade
                fetch(`/api/ustaz/livechat/${livechatId}/messages`)
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
    <div class="d-flex justify-content-end mb-4">
        <div class="me-3 text-end">
            <div class="bg-primary text-white p-2 rounded">
                <strong>System</strong>
                <p class="mb-0 text-start">Assalamu'alaikum, ada yang bisa dibantu?</p>
            </div>
            <small class="text-muted">{{ \Carbon\Carbon::parse($livechat->created_at)->format('H:i') }}</small>
        </div>
        <div class="me-3">
            <span class="avatar rounded-circle" style="height: 55px; width: 55px">
                <img class="avatar avatar-sm rounded-circle" src="{{ asset('img/system.png') }}" alt=""
                    style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
            </span>
        </div>
    </div>
    <div id="chat-container">
        <!-- Loop through messages -->
        @foreach($messages as $message)
        @if($message->user->role == 'umum')
        <!-- Message from umum -->
        <div class="d-flex mb-4">
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
            <div>
                <div class="bg-info text-white p-2 rounded">
                    <strong>{{ $message->user->umum->nama }}</strong>
                    <p class="mb-0">{{ $message->message }}</p>
                </div>
                <small class="text-muted">{{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}</small>
            </div>
        </div>
        @elseif($message->user->role == 'murid')
        <!-- Message from murid -->
        <div class="d-flex mb-4">
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
            <div>
                <div class="bg-info text-white p-2 rounded">
                    <strong>{{ $message->user->murid->nama }}</strong>
                    <p class="mb-0">{{ $message->message }}</p>
                </div>
                <small class="text-muted">{{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}</small>
            </div>
        </div>
        @elseif($message->user->role == 'ustaz')
        <!-- Message from ustaz -->
        <div class="d-flex justify-content-end mb-4">
            <div class="me-3 text-end">
                <div class="bg-primary text-white p-2 rounded">
                    <strong>{{ $message->user->ustaz->nama }}</strong>
                    <p class="mb-0 text-start">{{ $message->message }}</p>
                </div>
                <small class="text-muted">{{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}</small>
            </div>
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
<!-- resources/views/livechat/chat.blade.php -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#message-form').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            var formData = $(this).serialize();
            var url = "{{ route('livechat.sendUstaz') }}";

            $.post(url, formData, function(data) {
                // Append the new message to the chat container
                var newMessageHtml = `
                    <div class="d-flex justify-content-end mb-4">
                        <div class="me-3 text-end">
                            <div class="bg-primary text-white p-2 rounded">
                                <strong>{{ Auth::user()->ustaz->nama }}</strong>
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



@endsection