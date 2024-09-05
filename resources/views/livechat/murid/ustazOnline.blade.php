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
    <form action="{{ route('murid.livechatstart') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success">
            Mulai Sesi Tanya Jawab
        </button>
    </form>
    @endif
    @endif
</div>