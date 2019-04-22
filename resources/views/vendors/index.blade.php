@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Vendor Info</h2>
            <div class="pull-left">
              {{-- <form action="/order/" method="GET">
                  <div class="form-group">
                      <div class="row">
                          <div class="col-md-12">
                              <input name="term" type="text" class="form-control"
                                     value="{{ isset($term) ? $term : '' }}"
                                     placeholder="Search">
                          </div>
                          <div class="col-md-4">
                              <button hidden type="submit" class="btn btn-primary">Submit</button>
                          </div>
                      </div>
                  </div>
              </form> --}}
            </div>
            <div class="pull-right">
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#vendorCreateModal">+</a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Social handle</th>
            <th>Agents</th>
            <th>GST</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($vendors as $vendor)
            <tr>
              <td>{{ $vendor->id }}</td>
              <td>
                {{ $vendor->name }}
                <br>
                <span class="text-muted">
                  {{ $vendor->phone }}
                  <br>
                  {{ $vendor->email }}
                </span>
              </td>
              <td>{{ $vendor->address }}</td>
              <td>{{ $vendor->social_handle }}</td>
              <td>
                @if ($vendor->agents)
                  <ul>
                    @foreach ($vendor->agents as $agent)
                      <li>
                        <strong>{{ $agent->name }}</strong> <br>
                        {{ $agent->phone }} - {{ $agent->email }} <br>
                        <span class="text-muted">{{ $agent->address }}</span> <br>
                        <button type="button" class="btn btn-xs btn-secondary edit-agent-button" data-toggle="modal" data-target="#editAgentModal" data-agent="{{ $agent }}">Edit</button>
                      </li>
                    @endforeach
                  </ul>
                @endif
              </td>
              <td>{{ $vendor->gst }}</td>
              <td>
                <button type="button" class="btn btn-xs create-agent" data-toggle="modal" data-target="#createAgentModal" data-id="{{ $vendor->id }}">Add Agent</button>
                <button type="button" class="btn btn-image edit-vendor" data-toggle="modal" data-target="#vendorEditModal" data-vendor="{{ $vendor }}"><img src="/images/edit.png" /></button>

                {!! Form::open(['method' => 'DELETE','route' => ['vendor.destroy', $vendor->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                {!! Form::close() !!}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $vendors->appends(Request::except('page'))->links() !!}

    @include('vendors.partials.vendor-modals')
    @include('vendors.partials.agent-modals')

@endsection

@section('scripts')
  <script type="text/javascript">
    $(document).on('click', '.edit-vendor', function() {
      var vendor = $(this).data('vendor');
      var url = "{{ url('vendor') }}/" + vendor.id;

      $('#vendorEditModal form').attr('action', url);
      $('#vendor_name').val(vendor.name);
      $('#vendor_address').val(vendor.address);
      $('#vendor_phone').val(vendor.phone);
      $('#vendor_email').val(vendor.email);
      $('#vendor_social_handle').val(vendor.social_handle);
      $('#vendor_gst').val(vendor.gst);
    });

    $(document).on('click', '.create-agent', function() {
      var id = $(this).data('id');

      $('#agent_vendor_id').val(id);
    });

    $(document).on('click', '.edit-agent-button', function() {
      var agent = $(this).data('agent');
      var url = "{{ url('agent') }}/" + agent.id;

      $('#editAgentModal form').attr('action', url);
      $('#agent_name').val(agent.name);
      $('#agent_address').val(agent.address);
      $('#agent_phone').val(agent.phone);
      $('#agent_email').val(agent.email);
    });
  </script>
@endsection
