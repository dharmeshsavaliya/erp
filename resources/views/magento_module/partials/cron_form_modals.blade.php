<div id="cronJobDataAddModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            {{-- {!! Form::open(['route' => 'magento_module_types.store', 'method' => 'POST', 'class' => 'form mb-15', 'enctype' => 'multipart/form-data']) !!} --}}
            <form id="magento_module_cron_job_form" class="form mb-15" >
                @csrf
                {!! Form::hidden('magento_module_id', null, ['id'=>'magento_module_id']) !!}
            <div class="modal-header">
                <h4 class="modal-title">Add Cron Job Details  </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            {!! Form::text('cron_time', null, ['id'=>'cron_time', 'placeholder' => 'Cron Time', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('cron_time'))
                                <span style="color:red">{{ $errors->first('cron_time') }}</span>
                            @endif
                        </div>
                    </div>
                
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            {!! Form::text('frequency', null, ['id'=>'frequency', 'placeholder' => 'Cron frequency', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('frequency'))
                                <span style="color:red">{{ $errors->first('frequency') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            {!! Form::text('cpu_memory', null, ['id'=>'cpu_memory', 'placeholder' => 'CPU Memory', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('cpu_memory'))
                                <span style="color:red">{{ $errors->first('cpu_memory') }}</span>
                            @endif
                        </div>
                    </div>
                
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            {!! Form::text('comments', null, ['id'=>'comments', 'placeholder' => 'Comments', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('comments'))
                                <span style="color:red">{{ $errors->first('comments') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary">Add</button>
            </div>
            </form>
            {{-- {!! Form::close() !!} --}}
        </div>
    </div>
</div>

@push('scripts')
    <script>

    $(document).on('submit', '#magento_module_cron_job_form', function(e){
        e.preventDefault();
        alert("Sdfs");
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_module_cron_job_form"));
        var button = $(this).find('[type="submit"]');
        console.log(button);
        $.ajax({
            url: '{{ route("magento_module_cron_job_histories.store") }}',
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                button.html(spinner_html);
                button.prop('disabled', true);
                button.addClass('disabled');
            },
            complete: function() {
                button.html('Add');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#cronJobDataAddModal #magento_module_cron_job_form').trigger('reset');
                $('#cronJobDataAddModal #magento_module_cron_job_form').find('.error-help-block').remove();
                $('#cronJobDataAddModal #magento_module_cron_job_form').find('.invalid-feedback').remove();
                $('#cronJobDataAddModal #magento_module_cron_job_form').find('.alert').remove();
                oTable.draw();
                toastr["success"](response.message);
            },
            error: function(xhr, status, error) { // if error occured
                if(xhr.status == 422){
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                }
                else{
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });

    $(document).on('click', '.edit-magento-module', function() {
        var magento_module = $(this).data('row');
          console.log({magento_module})
          console.log(magento_module.category_name);
          $('#magento_module_edit_form #id').val(magento_module.id);
          $('#magento_module_edit_form #module_category_id').val(magento_module.module_category_id);
          $('#magento_module_edit_form #module').val(magento_module.module);
          $('#magento_module_edit_form #current_version').val(magento_module.current_version);
          $('#magento_module_edit_form #module_type').val(magento_module.module_type);
          $('#magento_module_edit_form #payment_status').val(magento_module.payment_status);
          $('#magento_module_edit_form #status').val(magento_module.status);
          $('#magento_module_edit_form #task_status').val(magento_module.task_status);
          $('#magento_module_edit_form #cron_time').val(magento_module.cron_time);
          $('#magento_module_edit_form #is_js_css').val(magento_module.is_js_css);
          $('#magento_module_edit_form #is_third_party_js').val(magento_module.is_third_party_js);
          $('#magento_module_edit_form #is_sql').val(magento_module.is_sql);
          $('#magento_module_edit_form #is_third_party_plugin').val(magento_module.is_third_party_plugin);
          $('#magento_module_edit_form #developer_name').val(magento_module.developer_name);
          $('#magento_module_edit_form #is_customized').val(magento_module.is_customized);
          $('#magento_module_edit_form #module_description').val(magento_module.module_description);

          $('#moduleEditModal').modal('show');
    });

    $(document).on('submit', '#magento_module_edit_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_module_edit_form"));
        var magento_module_id = $('#magento_module_edit_form #id').val();
        console.log(formData, magento_module_id);
        var button = $(this).find('[type="submit"]');
        console.log(button);
        $.ajax({
            url: '{{ route("magento_modules.update", '') }}/' + magento_module_id,
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                button.html(spinner_html);
                button.prop('disabled', true);
                button.addClass('disabled');
            },
            complete: function() {
                button.html('Update');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#moduleCreateModal #magento_module_edit_form').find('.error-help-block').remove();
                $('#moduleCreateModal #magento_module_edit_form').find('.invalid-feedback').remove();
                $('#moduleCreateModal #magento_module_edit_form').find('.alert').remove();
                oTable.draw();
                toastr["success"](response.message);
            },
            error: function(xhr, status, error) { // if error occured
                if(xhr.status == 422){
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                }
                else{
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });
    </script>

@endpush