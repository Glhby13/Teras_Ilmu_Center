@php
// Ekstrak VIDEO_ID dari URL
$url = $video->linkVideo;
$videoId = null;

// Cek dan ekstrak ID YouTube dari URL
if (preg_match('/youtube\.com\/(?:v=|embed\/|watch\?v=|watch\?.*v=)([^\&\?\/]+)/', $url, $matches)) {
$videoId = $matches[1];
} elseif (preg_match('/youtu\.be\/([^\?\/]+)/', $url, $matches)) {
$videoId = $matches[1];
}

// Tentukan URL thumbnail berdasarkan ID
$thumbnailUrl = $videoId ? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg" : asset('img/baca-quran.jpg');
@endphp
<div class="col-lg-4 col-md-6 col-sm-12 mb-4">
    <div class="card h-100 shadow-sm" style="display: flex; flex-direction: column;">
        <div class="d-flex justify-content-between">
            <div style="padding-left: 15px;">
                <a>
                    <!-- {{ \Carbon\Carbon::parse($video->created_at)->format('d F Y') }} -->
                    <p></p>
                </a>
            </div>
            @if($user->role === 'ustaz')
            <div class="float-end d-flex justify-content-end" style="padding-right: 15px;">
                <svg role="button" data-bs-toggle="dropdown" xmlns="http://www.w3.org/2000/svg"
                    class="mt-2 icon icon-tabler icon-tabler-dots" width="24" height="24"
                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                    stroke-linecap="round" stroke-linejoin="round" style="margin-top: 0 !important;">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                    <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                    <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                </svg>
                <div class="dropdown-menu">
                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editVideoModal{{ $video->id_video }}">
                        <svg role="button" xmlns="http://www.w3.org/2000/svg"
                            class="icon icon-tabler icon-tabler-edit me-2" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                            <path d="M16 5l3 3"></path>
                        </svg>
                        Edit
                    </a>

                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#hapusVideoModal{{ $video->id_video }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="icon icon-tabler icon-tabler-trash me-2" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M4 7l16 0"></path>
                            <path d="M10 11l0 6"></path>
                            <path d="M14 11l0 6"></path>
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                        </svg>
                        Delete</a>
                    </a>
                </div>
            </div>

            <!-- Edit Video Modal -->
            <div class="modal fade" id="editVideoModal{{ $video->id_video }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form id="editVideoForm{{ $video->id_video }}" action="{{ route('ustaz.editvideo', $video->id_video) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <b>Edit Video</b>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="editjudulVideo" class="form-label">Judul Video</label>
                                    <input type="text" class="form-control" id="editjudulVideo{{ $video->id_video }}" name="editjudulVideo" value="{{ $video->judulVideo }}">
                                </div>
                                <div class="mb-3">
                                    <label for="editdeskripsiVideo" class="form-label">Deskripsi</label>
                                    <textarea name="editdeskripsiVideo" id="editdeskripsiVideo{{ $video->id_video }}"
                                        class="border rounded-0 form-control summernote" rows="6" placeholder="{{ $video->deskripsiVideo }}">{{ $video->deskripsiVideo }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="editlinkVideo" class="form-label">Link YouTube Video</label>
                                    <input type="text" class="form-control" id="editlinkVideo{{ $video->id_video }}" name="editlinkVideo" value="{{ $video->linkVideo }}">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-ghost-secondary btn-pill" data-bs-dismiss="modal" id="canceleditVideoButton{{ $video->id_video }}">Cancel</button>
                                <button type="submit" class="btn btn-primary btn-pill">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                document.getElementById('canceleditVideoButton{{ $video->id_video }}').addEventListener('click', function() {
                    // Reset form fields
                    document.getElementById('editVideoForm{{ $video->id_video }}').reset();

                    // If you are using Summernote, you may need to clear its content separately
                    $('#editdeskripsiVideo{{ $video->id_video }}').summernote('reset');
                });
            </script>

            <!-- Delete Video modal -->
            <div class="modal fade" id="hapusVideoModal{{ $video->id_video }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Hapus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menghapus video?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form id="question-form" action="{{ route('ustaz.hapusvideo', $video->id_video ) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary">Yes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @endif
        </div>
        <div class="image-container">
            <img src="{{ $thumbnailUrl }}" />
        </div>
        <div class="card-body" style="display: flex; flex-direction: column; height: 100%; padding-bottom: 0">
            <div class="card-title" style="margin-bottom: 0;">
                <h2>{{ $video->judulVideo }}</h2>
            </div>
            <div class="card-text" style="overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; height: 4.5em; overflow-y: auto;">
                <p>{{ $video->deskripsiVideo }}</p>
            </div>
            <p></p>
            <a href="{{ $video->linkVideo }}" target="_blank" class="btn btn-outline-primary rounded-0 float-end">Nonton</a>



        </div>
    </div>
</div>