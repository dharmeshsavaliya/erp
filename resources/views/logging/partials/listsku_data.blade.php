@foreach ($logScrappers as $logScrapper)
    <tr>
        <td style="width: 5% !important;">{{ $logScrapper->sku }}</td>
        <td style="width: 5% !important;">{{ $logScrapper->skuFormat($logScrapper->sku,$logScrapper->brand) }}</td>
         <td style="width: 5% !important;">{{ $logScrapper->skuFormatExample($logScrapper->sku,$logScrapper->brand) }}</td>
        <td style="width: 20% !important;"> {{ $logScrapper->brand }}</td>
        <td style="width: 20% !important;">@if(isset($logScrapper->category)) {{ $logScrapper->unserialize($logScrapper->category) }} @endif</td>
        <td style="width: 20% !important;">{{ $logScrapper->website }}</td>
        <td>{{ $logScrapper->skuError( $logScrapper->validation_result) }}</td>
        <td>{{ $logScrapper->created_at->format('d-M-Y H:i:s') }}</td>
        <td>@if($logScrapper->taskType($logScrapper->website,$logScrapper->unserialize($logScrapper->category),$logScrapper->brand) == false) 
                <button onclick="addTask('{{ $logScrapper->website }}' , '{{ $logScrapper->unserialize($logScrapper->category) }}','{{ $logScrapper->sku }}','{{ $logScrapper->brand }}')" class="btn btn-secondary">Add Issue</button>
            @else
               {{ $logScrapper->taskType($logScrapper->website,$logScrapper->unserialize($logScrapper->category),$logScrapper->brand) }}
            @endif
            </td>
    </tr>
@endforeach
