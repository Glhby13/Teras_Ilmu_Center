@extends('partials.navbar')
@section('video')

<style>
    .swal2-success-circular-line-right {
        background-color: unset !important;
    }

    .swal2-success-circular-line-left {
        background-color: unset !important;
    }

    .swal2-success-fix {
        background-color: unset !important;
    }
</style>

<div class="card-body">
    <div class="card-body mt-3">
        <div class="row">
            <div class="col-9">
                @if($user->role === 'ustaz')
                <div class="d-flex justify-content-start mt-3" style="padding-left: 10px;">
                    <button type="button" class="btn btn-primary me-1" data-bs-toggle="modal"
                        data-bs-target="#uploadVideoModal" data-bs-whatever="@mdo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 24">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2">
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2m-5-10v6" />
                                <path d="M9.5 13.5L12 11l2.5 2.5" />
                            </g>
                        </svg>
                        Upload
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
    <div id="success-message" data-message="{{ session('success') }}"></div>
    @endif

    @if($errors->any())
    <div id="error-messages" data-errors='@json($errors->all())'></div>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successMessage = document.getElementById('success-message');
            var errorMessages = document.getElementById('error-messages');

            if (successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses',
                    text: successMessage.getAttribute('data-message'),
                    confirmButtonText: 'OK'
                });
            }

            if (errorMessages) {
                var errors = JSON.parse(errorMessages.getAttribute('data-errors'));
                errors.forEach(function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error,
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    </script>


    <!-- Video Modal -->
    <div class="modal fade" id="uploadVideoModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="uploadVideoForm" action="{{ route('ustaz.createvideo') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <b>Create Video</b>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="judulVideo" class="form-label">Judul Video</label>
                            <input type="text" class="form-control" id="judulVideo" name="judulVideo" placeholder="Tambahkan judul video">
                        </div>
                        <div class="mb-3">
                            <label for="deskripsiVideo" class="form-label">Deskripsi</label>
                            <textarea name="deskripsiVideo" id="deskripsiVideo"
                                class="border rounded-0 form-control summernote" rows="6" placeholder="Tambahkan deskripsi video"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="linkVideo" class="form-label">Link YouTube Video</label>
                            <input type="text" class="form-control" id="linkVideo" name="linkVideo" placeholder="Tambahkan link YouTube video">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost-secondary btn-pill" data-bs-dismiss="modal" id="cancelVideoButton">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-pill">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('cancelVideoButton').addEventListener('click', function() {
            // Reset form fields
            document.getElementById('uploadVideoForm').reset();

            // If you are using Summernote, you may need to clear its content separately
            $('#deskripsiVideo').summernote('reset');
        });
    </script>


    <div class="container mt-3">
        <div class="row">
            @foreach($video as $video)
            @include('video.materi')
            @endforeach

        </div>
    </div>
</div>
@endsection