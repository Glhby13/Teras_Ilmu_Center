@if($livechat->isEmpty())
<p>Tidak ada sesi live chat saat ini.</p>
@else
<p class="mb-3">Jumlah Live Chat : {{ $livechat->count() }}</p>
<div class="list-group">
    @foreach($livechat as $chat)
    <a href="{{ route('ustaz.chat', $chat->id_livechat) }}" class="list-group-item list-group-item-action d-flex align-items-center">
        <!-- Avatar Murid -->
        <span class="avatar rounded-circle" style="height: 55px; width: 55px">
            @if ($chat->user && $chat->user->murid && $chat->user->murid->gambar)
            <img class="avatar avatar-sm rounded-circle" src="data:image/png;base64,{{ $chat->user->murid->gambar }}" alt=""
                style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
            @elseif ($chat->user && $chat->user->umum && $chat->user->umum->gambar)
            <img class="avatar avatar-sm rounded-circle" src="data:image/png;base64,{{ $chat->user->umum->gambar }}" alt=""
                style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
            @else
            <img class="avatar avatar-sm rounded-circle" src="{{ asset('img/user2.png') }}" alt=""
                style="background-color: #DBE7F9; object-fit: cover; height: 100%; width: 100%">
            @endif
        </span>

        <!-- Nama -->
        <div style="padding-left: 10px;">
            @if ($chat->user && $chat->user->murid)
            <h5 class="mb-1">{{ $chat->user->murid->nama }} ({{$chat->user->role}})</h5>
            <small class="text-muted">{{ $chat->created_at->diffForHumans() }}</small>
            @elseif ($chat->user && $chat->user->umum)
            <h5 class="mb-1">{{ $chat->user->umum->nama }} ({{$chat->user->role}})</h5>
            <small class="text-muted">{{ $chat->created_at->diffForHumans() }}</small>
            @endif
        </div>
    </a>
    @endforeach
</div>
@endif