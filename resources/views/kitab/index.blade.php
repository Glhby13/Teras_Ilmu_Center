@extends('partials.navbar')
@section('kitab')

<style>
    .card {
        background-color: rgb(185, 195, 206);
    }
</style>

<div class="container mt-4">
    <div class="card-sholat">
        <div class="card-sholat">
            <h4 class="text-center">Jadwal Sholat Hari Ini</h4>
        </div>
        <div class="card-body">
            <p class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor"
                    class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                </svg>
                {{ $data->alamat }}, {{ ucwords(strtolower($prayerSchedule['daerah'] ?? 'N/A')) }}
            </p>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center imsak">
                    Imsak
                    <span class="badge text-bg-primary rounded-pill">{{ $prayerSchedule['jadwal']['imsak'] ?? 'N/A' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center subuh">
                    Subuh
                    <span class="badge text-bg-primary rounded-pill">{{ $prayerSchedule['jadwal']['subuh'] ?? 'N/A' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center dzuhur">
                    Dzuhur
                    <span class="badge text-bg-primary rounded-pill">{{ $prayerSchedule['jadwal']['dzuhur'] ?? 'N/A' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center ashar">
                    Ashar
                    <span class="badge text-bg-primary rounded-pill">{{ $prayerSchedule['jadwal']['ashar'] ?? 'N/A' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center maghrib">
                    Maghrib
                    <span class="badge text-bg-primary rounded-pill">{{ $prayerSchedule['jadwal']['maghrib'] ?? 'N/A' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center isya">
                    Isya
                    <span class="badge text-bg-primary rounded-pill">{{ $prayerSchedule['jadwal']['isya'] ?? 'N/A' }}</span>
                </li>
            </ul>
        </div>

    </div>
</div>

<div class="row mt-4">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">AL-Quran</h5>
                <p class="card-text">Baca Alquran Disini.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Hadits</h5>
                <p class="card-text">Pelajari Hadits Disini.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
    </div>
</div>

@endsection