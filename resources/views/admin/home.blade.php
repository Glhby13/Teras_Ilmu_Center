@extends('admin.navbar')
@section('home')


@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<h1>Welcome, Admin!</h1>
<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit">Logout</button>
</form>


@endsection