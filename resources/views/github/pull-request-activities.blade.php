
<div class="modal-header">
    <h5 class="modal-title">Pull Request Activities</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body" >
    <input type="hidden" id="repo"/>
    <input type="hidden" id="pullNumber"/>
    <table id="repository-table" class="table table-bordered">
        <thead>
            <tr>
                <th>Activity ID</th>
                <th>User</th>
                <th>Event</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activities as $activity)
                <tr>
                    <td>{{$activity['activity_id']}}</td>
                    <td>{{$activity['user']}}</td>
                    <td>{{$activity['event']}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Display pagination links -->
    {{ $activities->links() }}
</div>