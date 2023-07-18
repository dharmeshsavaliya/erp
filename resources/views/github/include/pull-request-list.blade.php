@foreach($pullRequests as $pullRequest)
<?php $class =  !empty($pullRequest['conflict_exist']) ? "table-danger" : ""; ?>
<tr class="{!! $class !!}">
    <td><input type="checkbox" checked name="bulk_select_pull_request[]" class="d-inline bulk_select_pull_request" value="{{$pullRequest['id']}}" data-repo="{{$pullRequest['repository']['id']}}"></td>
    <td class="Website-task">{{$pullRequest['repository']['name']}}
    <td class="Website-task">{{$pullRequest['id']}}</td>
    <td class="Website-task">{{$pullRequest['title']}}</td>
    <td class="Website-task">{{$pullRequest['source']}}</td>
    <td class="Website-task">{{$pullRequest['username']}}</td>
    <td class="Website-task">{{date('Y-m-d H:i:s', strtotime($pullRequest['updated_at']))}}</td>
    <td class="Website-task">
        @if ($pullRequest['latest_activity'])
            <strong>Activity ID: </strong>{{$pullRequest['latest_activity']['activity_id']}}</br>
            <strong>User: </strong>{{$pullRequest['latest_activity']['user']}}</br>
            <strong>Event: </strong>{{$pullRequest['latest_activity']['event']}}
            <button type="button" title="Activities" data-repo="{{$pullRequest['repository']['id']}}" data-pull-number="{{$pullRequest['id']}}" class="btn btn-xs show-pr-activities">
                <i class="fa fa-eye"></i>
            </button>
        @endif
    </td>
    <td >
       <button data-toggle="tooltip" data-placement="top" title="Deploy" style="margin-right:40px;"><a class="deploye"  href="{{ url('/github/repos/'.$pullRequest['repository']['id'].'/deploy?branch='.urlencode($pullRequest['source'])) }}"><img src="/Svglogo/deploy.svg" alt="Deploy"></a> </button>
        @if($pullRequest['repository']['name'] == "erp")
           <button data-toggle="tooltip" data-placement="top" title="Deploy + Composer"> <a style="margin-top: 5px;" class="deployepluscomposer" href="{{ url('/github/repos/'.$pullRequest['repository']['id'].'/deploy?branch='.urlencode($pullRequest['source'])) }}&composer=true"><img src="/Svglogo/deploy.svg" alt="Deploy"> <img src="/Svglogo/add.svg" alt=""> <img src="/Svglogo/compooser1.svg" alt="Composer"></a></button>
        @endif
    </td>
    <td style="width:10%;">
        {{-- <div>
            <a class="btn btn-sm btn-secondary" href="{{url('/github/repos/'.$pullRequest['repository']['id'].'/branch/merge?source=master&destination='.urlencode($pullRequest['source']))}}">
                Merge from master
            </a>
        </div> --}}
        <div style="margin-top: 5px;">
            <button data-toggle="tooltip" data-placement="top" title="Merge Into Master"  class="mergeintomaster" style="margin-top: 5px;" onclick="confirmMergeToMaster('{{$pullRequest["source"]}}','{{url('/github/repos/'.$pullRequest['repository']['id'].'/branch/merge?destination=master&source='.urlencode($pullRequest['source']).'&task_id='.urlencode($pullRequest['id']))}}')">
                <img src="/Svglogo/merge.svg" alt="Merge into master">
            </button>
            <button data-toggle="tooltip" data-placement="top" title="Close" class="closepr" style="margin-top: 5px;" onclick="confirmClosePR({!! $pullRequest['repository']['id'] !!}, {!! $pullRequest['id'] !!})">
                <img src="/Svglogo/close.svg" alt="Close">
            </button>
            <button type="button" title="Review Comments" data-repo="{{$pullRequest['repository']['id']}}" data-pull-number="{{$pullRequest['id']}}" class="btn btn-xs show-pr-review-comments">
                <i class="fa fa-pencil"></i>
            </button>
            <button type="button" class="btn btn-xs show-pr-error-logs" title="Error Log" data-repo="{{$pullRequest['repository']['id']}}" data-pull-number="{{$pullRequest['id']}}">
                <i class="fa fa-info-circle" style="color: #808080;"></i>
            </button>
            <button title="Build Process"  data-id="{{$pullRequest['repository']['id']}}" data-branch="{{$pullRequest['source']}}" type="button" class="btn open-build-process-template" style="padding:1px 0px;">
                <a href="javascript:void(0);" style="color:gray;"><i class="fa fa-simplybuilt"></i></a>
            </button>
            <a title="Build Process Logs" target="_blank"  href="{{ route("project.buildProcessLogs") }}" style="color:gray;"><i class="fa fa-info-circle"></i></a>
        </div>
    </td>
</tr>
@endforeach