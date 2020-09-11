@foreach ($inventory_data as $row => $data)
  <tr>
    <td>{{ $data['id'] }}</td>
    <td>{{ $data['sku'] }}</td>
    <td>{{ $data['name'] }}</td>
    <td>{{ $data['brand'] }}</td>
    <td>{{ $data['supplier'] }}</td>
    <td>
      <a data-toggle="collapse" class="btn btn-image show-medias-modal" aria-expanded="false"><i class="fa fa-picture-o" aria-hidden="true"></i></a>
      <a title="Add Avaibility" class="btn btn-image show-status-history-modal"><i class="fa fa-clock-o" aria-hidden="true"></i></a>
    </td>
    <td class="medias-data"  data='@if(isset($data['medias']))@json($data['medias'])@endif' style="display:none"></td>
    <td class="status-history"  data='@if(isset($data['status_history']))@json($data['status_history'])@endif' style="display:none"></td>
  </tr>

@endforeach