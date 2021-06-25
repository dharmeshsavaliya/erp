<div id="taskReminderModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Set / Edit Reminder</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="frequency">Frequency</label>
                        <?php echo Form::select("frequency",drop_down_frequency(),null,["class" => "form-control", "id" => "frequency"]); ?>
                    </div>
                    <div class="form-group">
                        <label for="frequency">Reminder Start From</label>
                        <input type="text" name="reminder_from" id="task_reminder_from" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="reminder_message">Check Last Message?</label>
                        <label class="radio-inline">
                          <input type="radio" id="reminder_last_reply" name="reminder_last_reply" value="1" checked>Yes
                        </label>
                        <label class="radio-inline">
                          <input type="radio" id="reminder_last_reply_no" name="reminder_last_reply" value="0">No
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="reminder_message">Reminder Message</label>
                        <textarea name="reminder_message" id="reminder_message" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-secondary task-submit-reminder">Save</button>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>