@extends('layouts.app')

@section('title', 'Google Play Developer')


@section('styles')
<style type="text/css">
    .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
 #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
</style>
@endsection
@section('content')
 <div id="myDiv">
      <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row">
        <div class="col-md-12">
           
        </div>
        <div class="col-md-12">
            @if(Session::has('message'))
                <script>
                    alert("{{Session::get('message')}}")
                </script>
            @endif
            <div class="row">
                <div class="col-lg-12 margin-tb mb-3">
        <h2 class="page-heading">Crash Report </h2>
            </div>
          
            
           
        
        <div class="col-md-12">
 <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th width="5%">ID</th>
            <th width="5%">App Name</th>
            <th width="5%">Aggregation Period</th>
            <th width="10%">LatestEndTime</th>
            <th width="10%">Time Zone</th>
            
          </tr>
        </thead>

        <tbody>
   



@foreach ($crashes as $crash)
 
<tr>
<td>{{ $id+=1 }}</td>
<td>{{ $crash->name }}</td>
<td>{{ $crash->aggregation_period }}</td>
<td>{{ $crash->latestEndTime }}</td>
<td>{{ $crash->timezone }}</td>
</tr>
@endforeach
 </tbody>
</table>   


	
        </div>
    </div>
    <div style="height: 600px;">
    </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection

@section('scripts')
<!--     <script>
   $('#sub').on('click',function(){
value=$('#search').val();

$.ajax({
type : 'get',
url : '{{ route("google.developer-api.crash") }}',
data:{'search':value},
success:function(data){
$('tbody').html(data);
}
});


})
    </script> -->
@endsection