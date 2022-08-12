<div id="modalTaskInformationUpdates" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Task's Information Update</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <?php
                $cls_1 = 'col-md-8';
                $cls_2 = 'col-md-4';
                ?>
                <div class="row">
                    <div class="col-md-4">
                        <label>Estimated Time: [In Minutes]</label>
                        <div class="form-group">
                            <input type="number" class="form-control" name="estimate_minutes" value="" min="1" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Remark:</label>
                        <div class="form-group">
                            <textarea class="form-control" name="remark" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="funTaskInformationUpdates('estimate_minutes')">Update</button>
                            <button type="button" class="btn btn-default show-time-history">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <?php if (isAdmin()) { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label>Lead Estimated Time: [In Minutes]</label>
                            <div class="form-group">
                                <input type="number" class="form-control" name="lead_estimate_time" value="" min="1" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Remark:</label>
                            <div class="form-group">
                                <textarea class="form-control" name="lead_remark" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="{{$cls_2}}">
                            <label>Actions</label>
                            <div class="form-group">
                                <button type="button" class="btn btn-secondary" onclick="funTaskInformationUpdates('lead_estimate_time')">Update</button>
                                <button type="button" class="btn btn-default show-lead-time-history">History</button>
                            </div>
                        </div>
                    </div>

                    <hr />
                <?php } ?>

                <div class="row">
                    <div class="{{$cls_1}}">
                        <label>Estimated Start Datetime:</label>
                        <div class="form-group">
                            <div class='input-group date cls-start-due-date'>
                                <input type="text" class="form-control" name="start_date" value="" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="funTaskInformationUpdates('start_date')">Update</button>
                            <button type="button" class="btn btn-default" onclick="funTaskHistories('start_date')">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="{{$cls_1}}">
                        <label>Estimated End Datetime: [Due Date]</label>
                        <div class="form-group">
                            <div class='input-group date cls-start-due-date'>
                                <input type="text" class="form-control" name="estimate_date" value="" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="funTaskInformationUpdates('estimate_date')">Update</button>
                            <button type="button" class="btn btn-default" onclick="funTaskHistories('estimate_date')">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="{{$cls_1}}">
                        <label>Cost:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="cost" value="" />
                        </div>
                    </div>
                    <div class="{{$cls_2}}">
                        <label>Actions</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary" onclick="funTaskInformationUpdates('cost')">Update</button>
                            <button type="button" class="btn btn-default" onclick="funTaskHistories('cost')">History</button>
                        </div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="col-md-6">
                        <label>Actual Start Time:</label>
                        <div class="form-group cls-actual_start_date"></div>
                    </div>
                    <div class="col-md-6">
                        <label>Actual End Time:</label>
                        <div class="form-group cls-actual_end_date"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="modalTaskHistories" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    var currTaskInformationTaskId = 0;

    function funGetTaskInformationModal() {
        return jQuery('#modalTaskInformationUpdates');
    }

    function funTaskInformationModal(ele, taskId) {
        siteLoader(1);
        currTaskInformationTaskId = taskId;
        let mdl = funGetTaskInformationModal();
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{!! route('development.task.get') !!}",
            type: 'GET',
            data: {
                id: taskId,
            },
        }).done(function(res) {
            siteLoader(0);
            if (res.data) {
                mdl.find('input[name="start_date"]').val(res.data.start_date);
                mdl.find('input[name="estimate_date"]').val(res.data.estimate_date);
                mdl.find('input[name="cost"]').val(res.data.cost);
                mdl.find('input[name="estimate_minutes"]').val(res.data.estimate_minutes);
                mdl.find('input[name="lead_estimate_time"]').val(res.data.lead_estimate_time);
                mdl.find('input[name="remark"]').val('');
                mdl.find('input[name="lead_remark"]').val('');

                mdl.find('.cls-actual_start_date').html(res.data.actual_start_date ? res.data.actual_start_date : '-');
                mdl.find('.cls-actual_end_date').html(res.data.actual_end_date ? res.data.actual_end_date : '-');

                mdl.find('.show-time-history').attr('data-id', res.data.id);
                mdl.find('.show-time-history').attr('data-userid', res.data.user_id);

                if (mdl.find('.show-lead-time-history').length) {
                    mdl.find('.show-lead-time-history').attr('data-id', res.data.id);
                }
                mdl.modal("show");
            } else {
                siteErrorAlert(res);
            }
        }).fail(function(err) {
            siteLoader(0);
            siteErrorAlert(err);
        });
    }

    function funTaskInformationUpdates(type) {
        if (type == 'start_date') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('development.update.start-date') }}",
                    type: 'POST',
                    data: {
                        id: currTaskInformationTaskId,
                        value: mdl.find('input[name="start_date"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        } else if (type == 'estimate_date') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('development.update.estimate-date') }}",
                    type: 'POST',
                    data: {
                        id: currTaskInformationTaskId,
                        value: mdl.find('input[name="estimate_date"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        } else if (type == 'cost') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('development.update.cost') }}",
                    type: 'POST',
                    data: {
                        id: currTaskInformationTaskId,
                        value: mdl.find('input[name="cost"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        } else if (type == 'estimate_minutes') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('development.update.estimate-minutes') }}",
                    type: 'POST',
                    data: {
                        issue_id: currTaskInformationTaskId,
                        estimate_minutes: mdl.find('input[name="estimate_minutes"]').val(),
                        remark: mdl.find('input[name="remark"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        } else if (type == 'lead_estimate_time') {
            if (confirm('Are you sure, do you want to update?')) {
                siteLoader(1);
                let mdl = funGetTaskInformationModal();
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('development.update.lead-estimate-minutes') }}",
                    type: 'POST',
                    data: {
                        issue_id: currTaskInformationTaskId,
                        lead_estimate_time: mdl.find('input[name="lead_estimate_time"]').val(),
                        remark: mdl.find('input[name="lead_remark"]').val(),
                    }
                }).done(function(res) {
                    siteLoader(0);
                    siteSuccessAlert(res);
                }).fail(function(err) {
                    siteLoader(0);
                    siteErrorAlert(err);
                });
            }
        }

    }

    function funTaskHistories(type) {
        if (type == 'start_date' || type == 'estimate_date' || type == 'cost') {
            siteLoader(1);
            let mdl = jQuery('#modalTaskHistories');
            let url = '';

            if (type == 'start_date') {
                mdl.find('.modal-title').html('Estimated Start Datetime History');
                url = "{{ route('development.history.start-date.index') }}";
            } else if (type == 'estimate_date') {
                mdl.find('.modal-title').html('Estimated End Datetime History');
                url = "{{ route('development.history.estimate-date.index') }}";
            } else if (type == 'cost') {
                mdl.find('.modal-title').html('Cost History');
                url = "{{ route('development.history.cost.index') }}";
            }

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                data: {
                    id: currTaskInformationTaskId
                }
            }).done(function(response) {
                mdl.find('.modal-body').html(response.data);
                mdl.modal('show');
                siteLoader(0);
            }).fail(function(err) {
                siteLoader(0);
                siteErrorAlert(err);
            });
        }
    }

    jQuery(document).ready(function() {
        applyDateTimePicker(jQuery('.cls-start-due-date'));
    });
</script>
@endpush