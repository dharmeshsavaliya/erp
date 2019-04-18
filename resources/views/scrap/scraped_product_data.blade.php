@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-responsive">
                @foreach($products as $key=>$product)
                    <tr>
                        <th colspan="34" class="text-center">
                            <h3>{{ $key }}</h3>
                        </th>
                    </tr>
                    <tr>
                        @foreach($product as $bkey => $brand)
                                <td>{{ $bkey }}</td>
                        @endforeach
                        <th>Total</th>
                    </tr>
                    <tr>
                        <?php $total = 0; ?>
                        @foreach($product as $bkey => $brand)
                            <?php $total += $brand ?>
                            <td>{{ $brand }}</td>
                        @endforeach
                        <th>{{ $total }}</th>
                    </tr>

                @endforeach
            </table>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection

@section('scripts')
    <script>
        var cid = null;
        $(function(){

            $('.show-details').on('click',function() {
                let id = $(this).attr('data-pid');
                let post_id = $(this).attr('data-media-id');

                $.ajax({
                    url: "{{ action('InstagramController@getComments') }}",
                    data: {
                        post_id: post_id
                    },
                    success: function(response) {
                        $('.reveal-'+id+' .comments-content').html('');
                        response.forEach(function (comment) {
                            var commentHTML = '<div class="comment text-justify m-2 mb-3" data-cid="'+comment.id+'">';
                            commentHTML += '<span><button data-pid="'+id+'" data-username="'+comment.username+'" data-cid="'+comment.id+'" type="button" class="close reply-to-comment" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>';
                            commentHTML+= '<span class="text-info">@'+comment.username+'</span>';
                            commentHTML += '<span style="display: block">'+comment.text+'</span>';
                            let repliesHTML = '<div class="replies-'+comment.id+'" style="margin: 5px 0 5px 10px; border-left:2px solid #DDD;">';
                            if (comment.replies !== []) {
                                comment.replies.forEach(function(reply) {
                                    repliesHTML += '<p style="margin: 5px 20px 5px 5px;">';
                                    repliesHTML += '<span class="text-info">@'+reply.username+'</span>';
                                    repliesHTML += '<span>'+reply.text+'</span>';
                                    repliesHTML += '</p>';
                                });
                            }
                            repliesHTML += '</div>';
                            commentHTML += repliesHTML;
                            commentHTML += '</div>';
                            $('.reveal-'+id+' .comments-content').prepend(commentHTML);
                        })
                    },
                    error: function() {
                        $('.reveal-'+id+' .comments-content').html('<p style="text-align: center;font-weight: bolder">We could not load comments at the moment. Please try again later.</p>');
                    },
                    beforeSend: function () {
                        $('.reveal-'+id).slideToggle('slow');
                        $('.reveal-'+id+' .comments-content').html('<p style="text-align: center"><img style="width:50px" src="/images/loading2.gif">Loading Comments...</p>');
                    }
                });


            });

            $('body').on('click', '.reply-to-comment', function() {
                let commentId = $(this).attr('data-cid');
                let username = $(this).attr('data-username');
                let pid = $(this).attr('data-pid');
                cid = commentId;
                $('.reply-'+pid).val('@'+username);
                $('.reply-'+pid).focus();
            });

            $('.reply').keypress(function (event) {
                if (event.keyCode == 13) {
                    let reply = $(this).val();
                    let comment_id = cid;
                    cid = null;
                    $(this).val('');
                    let id = $(this).attr('data-pid');
                    let self = this;
                    let postId = $(this).attr('data-post-id');
                    $.ajax({
                        url: "{{ action('InstagramController@postComment') }}",
                        type: 'post',
                        dataType: 'json',
                        data: {
                            message: reply,
                            post_id: postId,
                            comment_id: comment_id,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                if (comment_id == null) {
                                    var commentHTML = '<div class="comment text-justify m-2 mb-3" data-cid="'+response.id+'">';
                                    commentHTML += '<span><button data-username="'+response.username+'" data-cid="'+response.id+'" type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-reply"></i></button></span>';
                                    commentHTML+= '<span class="text-info">@'+response.username+'</span>';
                                    commentHTML += '<span style="display: block">'+response.text+'</span></div>';
                                    $('.reveal-'+id+' .comments-content').append(commentHTML);
                                } else {
                                    let repliesHTML = '<p style="margin: 5px 20px 5px 5px;">';
                                    repliesHTML += '<span class="text-info">@'+response.username+'</span>';
                                    repliesHTML += '<span>'+response.text+'</span>';
                                    repliesHTML += '</p>';
                                    $('.replies-'+comment_id).append(repliesHTML);
                                    comment_id = null;
                                }
                                $('.count-for-'+id).html(parseInt($('.count-for-'+id).html())+1);
                                $(".s-d-"+id).attr('data-comment-ids', $(".s-d-"+id).attr('data-comment-ids')+','+response.id);
                            }
                        },
                        error: function() {
                            alert("There was an unknown error saving this reply.");
                            $('.s-d-'+id).click();
                        },
                        complete: function () {
                            $(self).removeAttr('disabled');
                        },
                        beforeSend: function () {
                            $(self).attr('disabled', 'disabled');
                        }
                    });
                }
            });

            $('.card-reveal .close').on('click',function(){
                $(this).parent().slideToggle('slow');
            });
        });


    </script>
@endsection