@extends('layouts.app')

@section('content')
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    var currentChatParams = {};
    currentChatParams.data = {
        page: 1
        , hasMore: true
    , };
    var workingOn = null;

    function confirmDelete(repositoryId, branchName) {
        let result = confirm("Are you sure you want to delete this branch : "+ branchName+ "?");
        if (result) {
            $.ajax({
                headers : {
                    'Accept' : 'application/json',
                    'Content-Type' : 'application/json',
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: '/github/repos/'+repositoryId+'/branch?branch_name='+branchName,
                dataType: "json",
                success: function (response) {
                    if(response.status) {
                        toastr['success']('Branch has been deleted successfully!');
                        window.location.reload();
                    }else{
                        errorMessage = response.error ? response.error : 'Something went wrong please try again later!';
                        toastr['error'](errorMessage);
                    }
                },
                error: function () {
                    toastr['error']('Could not change module!');
                }
            });
        }
    }

    function getBranchHtml(response) {
        let html = "";
        $.each(response, function(key, value) {
            html += "<tr>";
            html += "<td>" + value.branch_name + "</td>";
            html += "<td>" + value.status + "</td>";
            html += "<td>" + value.behind_by + "</td>";
            html += "<td>" + value.ahead_by + "</td>";
            html += "<td>" + value.last_commit_author_username + "</td>";
            html += "<td>" + value.last_commit_time + "</td>";
            html += `<td style="width:10%;">
                <div style="margin-top: 5px;">
                    <button class="btn btn-sm btn-secondary" style="margin-top: 5px;" onclick="confirmDelete('`+value.repository_id+`','`+value.branch_name+`')">
                        Delete Branch
                    </button>
                </div>
            </td>`;
            html += "</tr>";
        });
        return html;
    }

    let isApiCall = true;
    let pageNum = 1;

    async function getBranches({repoId, page})
    {
        return $.ajax({
            type: "GET",
            url: "",
            async:true,
            data: {
                repoId: repoId,
                status: $("#status").val(),
                page:page
            },
            dataType: "json",
            success: function (response) {
                return response;
            }
        });
    }


    let $dataTable = $(document).find("#branch-section table").DataTable({
            "bPaginate": false,
            "ordering": false,
            "searching": true, 
            "search": {
                "addClass": 'form-control input-lg col-xs-12'
            },
        });
    async function fetchBranches({repoId}) {
        $dataTable.destroy();
        $(document).find("#branch-section .loader-section").show();
        $(document).find("#branch-section table tbody tr").remove();
        $(document).find("#branch-section table tfoot").hide();
        let branches = await getBranches({
            repoId: repoId,

        });
        if(branches.data.length < 1) {
            $(document).find("#branch-section table tfoot").show();
            // return false;
        }
        let htmlContent = getBranchHtml(branches.data);
        $(document).find("#branch-section table tbody").html(htmlContent);
        $(document).find("#branchCount").html(`(${branches.data.length})`)
        $(document).find("#branch-section .loader-section").hide();
        $dataTable = $(document).find("#branch-section table").DataTable({
            "bPaginate": false,
            "ordering": false,
            "searching": true, 
            "search": {
                "addClass": 'form-control input-lg col-xs-12'
            },
        });
    }
    
    $(document).ready(function() {
        $(async function() {
            await fetchBranches({
                repoId: $(document).find("select[name=repoId]").val(),
            });
        });

        $(document).on('change', "select[name=repoId]", async function() {
            await fetchBranches({
                repoId: $(document).find("select[name=repoId]").val(),
            });
        })

        $(document).on('change', "#status", async function() {
            await fetchBranches({
                repoId: $(document).find("select[name=repoId]").val(),
            });
        })

    })


</script>
<style>
    #action-workflow-table_filter {
        text-align: right;
    }

    table {
        margin: 0 auto;
        width: 100%;
        clear: both;
        border-collapse: collapse;
        table-layout: fixed; // 
        word-wrap: break-word; // 
    }

    .dataTables_wrapper.dt-bootstrap4 .row div.col-sm-12.col-md-6:empty{ display: none }

</style>

<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        <h5 class="ml-5">Branches <span id="branchCount"></span></h5>
        <h3 class="text-center">Github Branches</h3>
    </div>
</div>
@if(session()->has('message'))
<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        @php $type = Session::get('alert-type', 'info'); @endphp
            @if($type == "info")
            <div class="alert alert-secondary">
                {{ session()->get('message') }}
            </div>
            @elseif($type == "warning")
            <div class="alert alert-warning">
                {{ session()->get('message') }}
            </div>
            @elseif($type == "success")
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
            @elseif($type == "error")
            <div class="alert alert-error">
                {{ session()->get('message') }}
            </div>
            @endif
            
        </div>
    </div>
@endif

<div class="container" style="max-width: 100%;width: 100%;" id="branch-section">
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="" class="form-label">Repository</label>
            <select name="repoId" class="form-control">
                @foreach ($repos as $repo)
                    <option value="{{ $repo->id }}">{{ $repo->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="">All</option>
                <option value="open">Open</option>
                <option value="closed">Closed</option>
            </select>
        </div>
    </div>
    <table class="table table-bordered action-table" style="table-layout: fixed;">
        <thead>
            <tr>
                <th style="width:25% !important;">Name</th>
                <th style="width:10% !important;">Status</th>
                <th style="width:10% !important;">Behind By</th>
                <th style="width:10% !important;">Ahead By</th>
                <th style="width:15% !important;">Last Commit By</th>
                <th style="width:20% !important;">Last Commit At</th>
                <th style="width:10% !important;">Actions</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
        <tfoot style="display: none">
            <td colspan="5" >
                <h5 class="text-center text-bold">No Data Found</h5>
            </td>
        </tfoot>
    </table>
    <div class="loader-section">
        <div style="position: relative;left: 0px;top: 0px;width: 100%;height: 120px;z-index: 9999;background: url({{ url('images/pre-loader.gif')}}) 50% 50% no-repeat;"></div>
    </div>
</div>
@endsection
