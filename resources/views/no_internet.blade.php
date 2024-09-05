<!-- resources/views/no_internet.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koneksi Internet Diperlukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container text-center mt-5">
        <h1>Koneksi Internet Diperlukan</h1>
        <p>Untuk melanjutkan, pastikan perangkat Anda terhubung ke internet.</p>
        <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">Coba Lagi</a>
    </div>
</body>

</html>