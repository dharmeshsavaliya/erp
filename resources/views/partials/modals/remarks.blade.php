<div id="makeRemarkModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Remarks</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        <ul class="list-unstyled" id="remark-list">

        </ul>
        <form id="add-remark">
          <input type="hidden" name="id" value="">
          <div class="form-group">
            <textarea rows="2" name="remark" class="form-control" placeholder="Start the Remark"></textarea>
          </div>
          <div class="form-group">
            <label><input type="checkbox" class="need_to_send" value="1">&nbsp;Need to Send Message ?</label>
          </div>
          <div class="form-group">
            <label><input type="checkbox" class="inlcude_made_by" value="1">&nbsp;Want to include Made By ?</label>
          </div>
          <button type="button" class="btn btn-secondary btn-block mt-2" id="{{ (!empty($type) && $type = 'scrap') ? 'scrapAddRemarkbutton' : 'addRemarkButton' }}">Add</button>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
