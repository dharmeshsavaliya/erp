@extends('layouts.app')

@section('title', 'Assets Manager List')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Assets Manager List</h2>
            <div class="pull-left">
              <form class="form-inline" action="{{ route('assets-manager.index') }}" method="GET">                
                <div class="form-group ml-3">
                  <select class="form-control" name="archived">
                    <option value="">Select</option>
                    <option value="1" {{ isset($archived) && $archived == 1 ? 'selected' : '' }}>Archived</option>
                   
                  </select>
                </div>
                <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
              </form>
            </div>
            <div class="pull-right">            
                <button type="button" class="btn btn-secondary ml-3" data-toggle="modal" data-target="#assetsCreateModal">+</button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')


    <div class="mt-3 col-md-12">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th width="5%">ID</th>
            <th>Name</th>
            <th>Password</th>
            <th width="10%">Asset Type</th>
            <th width="15%">Category</th>
            <th width="15%">Provider Name</th>
            <th width="10%">Purchase Type</th>
            <th width="10%">Payment Cycle</th>
            <th width="10%">Amount</th>
            <th width="10%">Currency</th>
            <th width="15%">Location</th>
            <th width="15%">Action</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($assets as $asset)
            <tr>
              <td>{{ $asset->id }}</td>
              <td>{{ $asset->name }}</td>
              <td>{{ $asset->password }}</td>
              <td>{{ $asset->asset_type }}</td>
              <td>@if(isset($asset->category)) {{ $asset->category->cat_name }} @endif</td>
              
              <td>{{ $asset->provider_name }}</td>
              <td>{{ $asset->purchase_type }}</td>
              <td>{{ $asset->payment_cycle }}</td>              
              <td>{{ $asset->amount }}</td>
              <td>{{ $asset->currency }}</td> 
              <td>{{ $asset->location }}</td>        
              <td>
                  <div style="min-width: 100px;">
                    <!--   <a href="{{ route('assets-manager.show', $asset->id) }}" class="btn  d-inline btn-image" href=""><img src="/images/view.png" /></a> -->
                      <button type="button" class="btn btn-image edit-assets d-inline" data-toggle="modal" data-target="#assetsEditModal" data-assets="{{ json_encode($asset) }}"><img src="/images/edit.png" /></button>
                      <button type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $asset->id }}"><img src="/images/remark.png" /></button>
                      {!! Form::open(['method' => 'DELETE','route' => ['assets-manager.destroy', $asset->id],'style'=>'display:inline']) !!}
                      <button type="submit" class="btn btn-image d-inline"><img src="/images/delete.png" /></button>
                      {!! Form::close() !!}
                  </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @include('partials.modals.remarks')
    @include('assets-manager.partials.assets-modals')   
@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script type="text/javascript">      

    
    $(document).on('click', '.edit-assets', function() {
      var asset = $(this).data('assets');
      var url = "{{ url('assets-manager') }}/" + asset.id;
      console.log(asset);
      $('#assetsEditModal form').attr('action', url);
      $('#asset_name').val(asset.name);
      $('#password').val(asset.password);
      $('#provider_name').val(asset.provider_name);
      $('#location').val(asset.location);
      $('#currency').val(asset.currency);
      $('#asset_asset_type').val(asset.asset_type);
      $('#category_id2').val(asset.category_id);
      $('#asset_purchase_type').val(asset.purchase_type);
      $('#asset_payment_cycle').val(asset.payment_cycle);
      $('#asset_amount').val(asset.amount);
    });

    $(document).on('click', '.make-remark', function(e) {
      e.preventDefault();

      var id = $(this).data('id');
      $('#add-remark input[name="id"]').val(id);

      $.ajax({
          type: 'GET',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('task.gettaskremark') }}',
          data: {
            id:id,
            module_type: "assets-manager"
          },
      }).done(response => {
          var html='';

          $.each(response, function( index, value ) {
            html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
            html+"<hr>";
          });
          $("#makeRemarkModal").find('#remark-list').html(html);
      });
    });

    $('#addRemarkButton').on('click', function() {
      var id = $('#add-remark input[name="id"]').val();
      var remark = $('#add-remark').find('textarea[name="remark"]').val();

      $.ajax({
          type: 'POST',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('task.addRemark') }}',
          data: {
            id:id,
            remark:remark,
            module_type: 'assets-manager'
          },
      }).done(response => {
          $('#add-remark').find('textarea[name="remark"]').val('');

          var html =' <p> '+ remark +' <br> <small>By You updated on '+ moment().format('DD-M H:mm') +' </small></p>';

          $("#makeRemarkModal").find('#remark-list').append(html);
      }).fail(function(response) {
        console.log(response);

        alert('Could not fetch remarks');
      });
    });

    $(document).ready(function() {
      // Change category on create page logic      
      $('#category_id').on('change', function(){        
          var category_id = $('#category_id').val();
          if( category_id != '' && category_id == '-1')
          {
              $('.othercat').show();
          } 
          else{
            $('.othercat').hide();
          } 
      });
      // Change categoryon create page logic
      $('#category_id2').on('change', function(){        
          var category_id = $('#category_id2').val();
          if( category_id != '' && category_id == '-1')
          {
              $('.othercatedit').show();
          } 
          else{
            $('.othercatedit').hide();
          } 
      });
            
    });
  </script>
@endsection
