<div id="modalUserAvailabilityHistories" class="modal fade" role="dialog">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">User Availabilities</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="funUserAvailabilityAdd()">Add New</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div id="modalUserAvailabilityForm" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="" method="post">
        <input type="hidden" name="user_id" value="">
        <div class="modal-header">
          <h4 class="modal-title">Add User Availability</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <?php
          $cls_1 = 'col-md-6';
          $cls_2 = 'col-md-6';
          ?>

          <div class="row">
            <div class="col-md-12">
              <label>Days:</label>
              <div class="form-group">
                <label><input type="checkbox" class="form-control1" name="day[]" value="monday" style="height: auto;"> Monday</label>
                <label class="ml-2"><input type="checkbox" class="form-control1" name="day[]" value="tuesday" required style="height: auto;"> Tuesday</label>
                <label class="ml-2"><input type="checkbox" class="form-control1" name="day[]" value="wednesday" required style="height: auto;"> Wednesday</label>
                <label class="ml-2"><input type="checkbox" class="form-control1" name="day[]" value="thursday" required style="height: auto;"> Thursday</label>
                <label class="ml-2"><input type="checkbox" class="form-control1" name="day[]" value="friday" required style="height: auto;"> Friday</label>
                <label class="ml-2"><input type="checkbox" class="form-control1" name="day[]" value="saturday" required style="height: auto;"> Saturday</label>
                <label class="ml-2"><input type="checkbox" class="form-control1" name="day[]" value="sunday" required style="height: auto;"> Sunday</label>
              </div>
            </div>
          </div>

          <hr />

          <div class="row">
            <div class="{{$cls_1}}">
              <label>From Date:</label>
              <div class="form-group">
                <div class='input-group date cls-datepicker'>
                  <input type="text" class="form-control" name="from" value="" required />
                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
              </div>
            </div>
            <div class="{{$cls_2}}">
              <label>To Date:</label>
              <div class="form-group">
                <div class='input-group date cls-datepicker'>
                  <input type="text" class="form-control" name="to" value="" required />
                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
              </div>
            </div>
          </div>

          <hr />

          <div class="row">
            <div class="col-md-4">
              <label>Start Time:</label>
              <div class="form-group">
                <div class='input-group date cls-timepicker'>
                  <input type="text" class="form-control" name="start_time" value="" required />
                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <label>End Time:</label>
              <div class="form-group">
                <div class='input-group date cls-timepicker'>
                  <input type="text" class="form-control" name="end_time" value="" required />
                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <label>Lunch Time:</label>
              <div class="form-group">
                <div class='input-group date cls-timepicker'>
                  <input type="text" class="form-control" name="lunch_time" value="" />
                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
              </div>
            </div>
          </div>

          <hr />

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="funUserAvailabilitySave(this)">Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
@push('scripts')
<script>
  let currModalUserAvailabilityListUserId = 0;

  function funUserAvailabilityList(ele, id) {
    currModalUserAvailabilityListUserId = id;
    siteLoader(1);
    let mdl = jQuery('#modalUserAvailabilityHistories');
    jQuery.ajax({
      headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('user-avaibility.index') }}",
      type: 'GET',
      data: {
        id: currModalUserAvailabilityListUserId
      }
    }).done(function(response) {
      siteLoader(0);
      mdl.find('.modal-body').html(response.data);
      mdl.modal('show');
    }).fail(function(err) {
      siteLoader(0);
      siteErrorAlert(err);
    });
  }

  function funUserAvailabilityAdd() {
    let mdl = jQuery('#modalUserAvailabilityForm');
    let frm = mdl.find('form');

    frm.find('input[name="from"]').val('');
    frm.find('input[name="to"]').val('');
    frm.find('input[name="start_time"]').val('');
    frm.find('input[name="end_time"]').val('');
    frm.find('input[name="lunch_time"]').val('');
    frm.find('input[name="day[]"]').prop('checked', false);

    mdl.modal('show');
  }

  function funUserAvailabilitySave(ele) {
    let frm = jQuery(ele).closest('form');

    frm.find('input[name="user_id"]').val(currModalUserAvailabilityListUserId);

    siteLoader(1);
    jQuery.ajax({
      headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('user-avaibility.save') }}",
      type: 'POST',
      data: frm.serialize()
    }).done(function(res) {
      siteLoader(0);
      siteSuccessAlert(res);
      jQuery('#modalUserAvailabilityForm').modal('hide');
      jQuery('#modalUserAvailabilityHistories').find('.modal-body').html(res.list);
    }).fail(function(err) {
      siteLoader(0);
      siteErrorAlert(err);
    });
  }

  jQuery(document).ready(function() {
    applyDatePicker(jQuery('.cls-datepicker'));
    applyTimePicker(jQuery('.cls-timepicker'));
  });
</script>
@endpush