@extends('layouts.app')


@section('content')

<div class="col-lg-12 margin-tb page-heading">
    <h2>Choose event preferred timing</h2>
</div>


<h4>Event details:</h4>


<div class="row">
    <div class="col-lg-4">
        {{Form::open(array('url' => '/calendar/public/event/suggest-time/'.$invitationId, 'method' => 'POST'))}}
        <div class="form-group">
            {{ Form::label('host', 'Host:') }}
            {{ Form::text('host',$attendee->event->user->name , array('class' => 'form-control', 'disabled' => ''))  }}
        </div>

        <div class="form-group">
            {{ Form::label('subject', 'Subject:') }}
            {{ Form::text('subject',$attendee->event->subject, array('class' => 'form-control', 'disabled' => ''))  }}
        </div>

        <div class="form-group">
            {{ Form::label('description', 'Description:') }}
            {{ Form::text('description',$attendee->event->description, array('class' => 'form-control', 'disabled' => ''))  }}
        </div>
        <div class="form-group">
            {{ Form::label('date', 'Date:') }}
            {{ Form::text('date',$attendee->event->date, array('class' => 'form-control', 'disabled' => ''))  }}
        </div>


        <div class="form-group">
            {{ Form::label('time', 'Time:') }}
            {{ Form::text('time',$attendee->suggested_time, array('class' => 'form-control'))  }}
        </div>

        <div>
            <input type="submit" class="btn btn-primary" data-dismiss="modal" value="Save" />
        </div>
        {{ Form::close() }}
    </div>
</div>
@if (session()->has('message'))
<div class="mt-1">
    <div class="alert alert-info">
        {{ session('message') }}
    </div>
</div>
@endif
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#time').datetimepicker({
            format: 'HH:mm'
        });
    })
</script>

@endsection