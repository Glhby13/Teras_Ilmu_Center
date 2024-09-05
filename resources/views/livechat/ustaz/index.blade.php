@extends('partials.navbar')
@section('livechatUstaz')


<div class="container  mt-3">
    <h3>Daftar Live Chat</h3>

    <div class="list-group" id="livechat-list">

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function fetchLivechat() {
            $.get("{{ route('ustaz.livechat') }}", function(data) {
                $('#livechat-list').html(data);
            });
        }

        // Polling every 10 seconds
        setInterval(fetchLivechat, 2000);

        // Fetch live chat on page load
        fetchLivechat();
    });
</script>

@endsection