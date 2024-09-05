<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Tambahkan script Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.6/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.6/dist/flatpickr.min.js"></script>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login TIC</title>
  <link rel="shortcut icon" type="image/png" href="../img/logoTSII.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<style>
  .page-wrapper {
    background-color: #699ce82c;
  }

  .logo {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif
  }

  .success-floating {
    position: fixed;
    transform: translate(-50%, -50%);
    z-index: 1050;
    width: 35vh;
    top: 50%;
    left: 50%;
    background-color: #e3f9ff;
    border-radius: 10px;
  }

  /* Atur tinggi maksimum dan scrollbar untuk dropdown */
  #suggestions {
    max-height: 200px;
    /* Atur tinggi maksimal sesuai kebutuhan */
    overflow-y: auto;
    /* Tambahkan scroll vertikal jika konten melebihi tinggi */
    border-radius: 4px;
    background-color: #fff;
    position: absolute;
    z-index: 1000;
    width: 40%;
  }

  .list-group-item {
    cursor: pointer;
  }

  .list-group-item:hover {
    background-color: #f8f9fa;
  }
</style>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    // Temukan notifikasi sukses
    var successFloating = document.querySelector('.success-floating');

    // Tambahkan event listener untuk mendeteksi klik di luar notifikasi sukses
    document.addEventListener("click", function(event) {
      if (event.target !== successFloating) {
        successFloating.style.display = 'none'; // Sembunyikan notifikasi sukses jika diklik di luar notifikasi
      }
    });
  });

  function closeSuccessNotification() {
    var successFloating = document.querySelector('.success-floating');
    successFloating.style.display = 'none';
  }
</script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    // Temukan elemen input tanggal
    var dateInput = document.getElementById("date");

    // Dapatkan tanggal hari ini
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    today = dd + '-' + mm + '-' + yyyy;

    // Inisialisasi Flatpickr dengan format yang diinginkan
    flatpickr(dateInput, {
      dateFormat: "d-m-Y",
      maxDate: today,
      disableMobile: true,
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('alamat');
    const suggestionsList = document.getElementById('suggestions');
    const cities = @json($cities);

    input.addEventListener('input', function() {
      const query = input.value.toLowerCase();
      suggestionsList.innerHTML = '';

      if (query.length > 0) {
        const filteredCities = cities.filter(city => city.lokasi.toLowerCase().includes(query));

        filteredCities.forEach(city => {
          const listItem = document.createElement('li');
          listItem.textContent = city.lokasi;
          listItem.classList.add('list-group-item');
          listItem.addEventListener('click', () => {
            input.value = city.lokasi;
            suggestionsList.innerHTML = '';
          });
          suggestionsList.appendChild(listItem);
        });
      }
    });

    // Handle form submission or Enter key press
    input.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        const firstSuggestion = suggestionsList.querySelector('.list-group-item');
        if (firstSuggestion) {
          input.value = firstSuggestion.textContent;
          suggestionsList.innerHTML = '';
        }
        e.preventDefault(); // Prevent form submission
      }
    });
  });
</script>

<body>

  @if (session('success'))
  <div class="success-floating">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-body text-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" style="color:#44B158; margin-top: 1rem;" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
            <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z" />
            <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z" />
          </svg>
          <p class="font-weight-bold font-size-20 mt-2">{{ session('success') }}</p>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div
      class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-lg-6">
            <div class="card mb-0">
              <div class="card-body">
                <a class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="../img/logoTSII.png" width="180" alt="">
                </a>
                <p class="text-center"><b>Gabung Dengan Kami!</b></p>
                <form action="{{ route('register') }}" method="POST">
                  @csrf
                  <div class="row">
                    <div class="col-6">
                      <!-- Nama -->
                      <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}">
                        @error('nama')
                        <div style="color: red; font-size: 12px; position: absolute;">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- Tanggal Lahir -->
                      <div class="mb-3">
                        <label for="date" class="form-label">Tanggal Lahir</label>
                        <input class="form-control" id="date" name="date" type="text"
                          style="font-size: 14px; background-color: unset;" placeholder="dd-mm-yyyy"
                          value="{{ old('date') ? old('date') : '' }}">
                        @error('date')
                        <div style="color: red; font-size: 12px; position: absolute;">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- Alamat -->
                      <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Ketik nama kota/kabupaten">
                        <ul id="suggestions" class="list-group mt-2"></ul>
                        @error('alamat')
                        <div style="color: red; font-size: 12px; position: absolute;">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- Jenis Kelamin -->
                      <div class="mb-5">
                        <label class="form-label d-block">Jenis Kelamin</label>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="gender" id="Laki-laki" value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'checked' : '' }}>
                          <label class="form-check-label" for="Laki-laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="gender" id="Perempuan" value="Perempuan" {{ old('gender') == 'Perempuan' ? 'checked' : '' }}>
                          <label class="form-check-label" for="Perempuan">Perempuan</label>
                        </div>
                        @error('gender')
                        <div style="color: red; font-size: 12px;">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>

                    <div class="col-6">
                      <!-- No Telepon -->
                      <div class="mb-3">
                        <label for="telp" class="form-label">No Telepon</label>
                        <input type="text" class="form-control" id="telp" name="telp" value="{{ old('telp') }}">
                        @error('telp')
                        <div style="color: red; font-size: 12px; position: absolute;">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- Username -->
                      <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}">
                        @error('username')
                        <div style="color: red; font-size: 12px; position: absolute;">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- Email -->
                      <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                        @error('email')
                        <div style="color: red; font-size: 12px; position: absolute;">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- Password -->
                      <div class="mb-5">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                        @error('password')
                        <div style="color: red; font-size: 12px; position: absolute;">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <button name="submit" type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign up</button>
                </form>

                <div class="d-flex align-items-center justify-content-center">
                  <p class="fs-4 mb-0 fw-bold">Sudah punya akun</p>
                  <a class="text-primary fw-bold ms-2" href="{{ route('login') }}">Log in</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>