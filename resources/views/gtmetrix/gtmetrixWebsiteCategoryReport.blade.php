@extends('layouts.app')



@section('title', $title)

@section('styles')

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
        
        .disabled{
            pointer-events: none;
            background: #bababa;
        }
        .glyphicon-refresh-animate {
            -animation: spin .7s infinite linear;
            -webkit-animation: spin2 .7s infinite linear;
        }

        @-webkit-keyframes spin2 {
            from { -webkit-transform: rotate(0deg);}
            to { -webkit-transform: rotate(360deg);}
        }

        @keyframes spin {
            from { transform: scale(1) rotate(0deg);}
            to { transform: scale(1) rotate(360deg);}
        }
        table.dataTable{
          margin:0;

        }
        .dataTables_scrollHeadInner,table.dataTable{
            width: 100% !important;
        }
        #gtmetrix-report-modal .modal-body {
    height: calc(100vh - 50vh);
    overflow-x: auto;
}
    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class=" mt-3 pr-2 pl-2" style="overflow-x: auto;">
        <div class="col-md-12">
            <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
          </div>  
        </div>
        <div class="gtmetrix_table_data" style="overflow-x: auto;">
            <table class="table table-bordered " id="gtmetrix_table">
                <thead>
                    <tr>
                        <th width="10%"> Website URL </th>
                        @foreach ($catArr as $catN)
                            <th width="10%"> {{$catN}} </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pagespeedDatanew as $cat)
                        <tr>
                            <td width="10%">{{$cat['website']}}</td>
                            @foreach ($cat['score'] as $key => $score)
                                <td width="10%">
                                    @if($score >= 89)
                                    @php $color = 'bg-success' ; @endphp
                                    @endif
                                    @if($score <= 48)
                                    @php $color = 'bg-danger' ; @endphp
                                    @endif
                                    @if($score <= 60 && $score >= 48 )
                                    @php $color = 'bg-warning' ; @endphp
                                    @endif
                                    <div class="progress">
                                        <div class="progress-bar {{$color}} progress-bar-striped " role="progressbar" style="width: {{$score}}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{$score}}%</div>
                                    </div>
                                </td>
                            @endforeach
                            @empty($cat['score'])
                                @foreach ($catArr as $catN1)
                                    <td width="10%"> N/A </td>
                                @endforeach  
                            @endempty
                                
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="gtmetrix-report-modal" role="dialog">
    <div class="modal-dialog modal-lg model-width">
      <!-- Modal content-->
        <div class="modal-content message-modal" style="width: 100%;">
            
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).on('click', '#searchReset', function(e) {
            //alert('success');
            $('#dateform').trigger("reset");
            e.preventDefault();
            oTable.draw();
        });

        $('#dateform').on('submit', function(e) {
            e.preventDefault();
            oTable.draw();

            return false;
        });

        $('#extraSearch').on('click', function(e) {
            e.preventDefault();
            oTable.draw();
        }); 

        
        
        $(document).on('click', '.show-gtmetrix-report-details', function(e){
            e.preventDefault();
            var id = $(this).data("id");
            $('.message-modal').html(''); 
            $('#loading-image').show();     
            $.ajax({
                url: '{{ route('gtmetrix.single.report') }}',
                type: 'POST',
                dataType: 'html',
                data:{
                    id: id,
                    _token: '{{ csrf_token() }}',
                },
            })
            .done(function(data){
                $('.message-modal').html('');    
                $('.message-modal').html(data); // load response 
                $("#gtmetrix-report-modal").modal("show");
                $('#loading-image').hide();        // hide ajax loader 
            })
            .fail(function(){
                toastr["error"]("Something went wrong please check log file");
                $('#loading-image').hide();
            });
        });
    </script>

@endsection