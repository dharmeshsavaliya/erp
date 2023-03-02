@foreach($adGroups as $adGroup)
    <tr>
        <td>{{$adGroup->id}}</td>
        <td>{{$adGroup->ad_group_name}}</td>
        <td>{{$adGroup->adgroup_google_campaign_id}}</td>
        <td>{{$adGroup->google_adgroup_id}}</td>
        @if(!in_array(@$campaign_channel_type, ["MULTI_CHANNEL"]))
        <td>{{$adGroup->bid}}</td>
        @endif
        <td>{{$adGroup->status}}</td>
        <td>{{$adGroup->created_at}}</td>
        <td>
            <div class="d-flex justify-content-between">
                @if(in_array(@$campaign_channel_type, ["DISPLAY"]))
                    <form method="GET" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroup['google_adgroup_id']}}/responsive-display-ad">
                        <button type="submit" class="btn-image">Display Ads</button>
                    </form>
                @elseif(in_array(@$campaign_channel_type, ["SEARCH"]))
                    <form method="GET" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroup['google_adgroup_id']}}/ads">
                        <button type="submit" class="btn-image">Ads</button>
                    </form>
                @elseif(in_array(@$campaign_channel_type, ["MULTI_CHANNEL"]))
                    <form method="GET" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroup['google_adgroup_id']}}/app-ad">
                        <button type="submit" class="btn-image">App Ads</button>
                    </form>
                @endif
                {!! Form::open(['method' => 'DELETE','route' => ['adgroup.deleteAdGroup',$campaignId,$adGroup['google_adgroup_id']],'style'=>'display:inline']) !!}
                <button type="submit" class="btn-image"><img src="/images/delete.png"></button>
                {!! Form::close() !!}
                {!! Form::open(['method' => 'GET','route' => ['adgroup.updatePage',$campaignId,$adGroup['google_adgroup_id']],'style'=>'display:inline']) !!}
                <button type="submit" class="btn-image"><img src="/images/edit.png"></i></button>
                {!! Form::close() !!}
            </div>
        </td>
    </tr>
@endforeach