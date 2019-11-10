<!-- Modal To Add Category-->
<div class="modal fade" id="commentModal{{ $post->id }}" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Communication</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
                <div class="modal-body">
                    @if ($post->send_comment)
                    @foreach($post->send_comment as $keyy=>$comment)
                    <p><a href="https://instagram.com/{{ $comment->username }}">{{ $comment->username }} </a><span> {{ $comment->comment }}</span><small>...{{ $comment->created_at->format('d-m-y') }}</small></p>
                    
                    @endforeach
                    @else
                    <strong>No Send Message yet!</strong>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
        </div>
    </div>
</div>