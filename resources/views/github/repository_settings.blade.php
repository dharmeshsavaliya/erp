@extends('layouts.app')

@section('content')
<h2 class="text-center">{{ $repository->name }} branches</h2>
<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Behind By</th>
                <th>Ahead By</th>
                <th>Deployment</th>
                <th>Merge</th>
            </tr>
        </thead>
        <tbody>
            @foreach($branches as $branch)
            <tr>
                <td>{{$branch->branch_name}}</td>
                <td>{{$branch->ahead_by}}</td>
                <td>{{$branch->behind_by}}</td>
                <td>
                    @if($branch->branch_name == $current_branch)
                    <span class="badge badge-pill badge-light">Deployed</span>
                    @else
                    <a class="btn btn-sm btn-primary" href="#">Deploy</a>
                    @endif
                </td>
                <td>
                    <div>
                        <a class="btn btn-sm btn-warning" href="#">Merge from master</a>
                    </div>
                    <div style="margin-top: 5px;">
                        <a class="btn btn-sm btn-info" href="#">Merge into master</a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection