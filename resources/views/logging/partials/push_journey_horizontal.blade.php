

<tr>
    <td>SKU</td>
    <td>entered_in_product_push</td>
    @foreach($conditions as $condition)
        <td>{{$condition->condition}}</td>
    @endforeach
</tr>

<tr>
    <td>{{$productSku}}</td>
    <td> @if(in_array('entered_in_product_push', $pushJourney)) <i class="fa fa-check-circle-o text-success fa-lg" aria-hidden="true"></i> @else <i class="fa fa-times-circle text-danger fa-lg" aria-hidden="true"></i> @endif</td>
    @foreach($conditions as $condition)
        {{-- <td>@if(in_array($condition, $pushJourney)) YES @else NO @endif</td> --}}
        {{-- <td>{{$condition->$useStatus}}</td> --}}
        <td>
            @if($condition->$useStatus == '1')
                <i class="fa fa-check-circle-o text-success fa-lg" aria-hidden="true"></i>
            @else
                <i class="fa fa-times-circle text-danger fa-lg" aria-hidden="true"></i>
            @endif
        </td>
    @endforeach
</tr>