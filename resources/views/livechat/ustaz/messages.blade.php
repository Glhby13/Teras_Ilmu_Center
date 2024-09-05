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