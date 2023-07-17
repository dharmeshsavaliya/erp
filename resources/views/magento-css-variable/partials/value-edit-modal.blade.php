<div id="magento-css-value-edit" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            
            <form id="magento-css-value-edit-form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Update Value</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <strong>File Path :</strong>
                    {!! Form::hidden('id', null, ['id' => 'id', 'class' => 'form-control']) !!}
                    {!! Form::text('file_path', null, ['placeholder' => 'File Path', 'id' => 'file_path', 'class' => 'form-control', 'readonly' => 'readonly']) !!}
                </div>
                <div class="form-group">
                    <strong>Variable :</strong>
                    {!! Form::text('variable', null, ['placeholder' => 'Variable', 'id' => 'variable', 'class' => 'form-control', 'readonly' => 'readonly']) !!}
                </div>
                <div class="form-group">
                    <strong>Value :</strong>
                    {!! Form::text('value', null, ['placeholder' => 'Value', 'id' => 'value', 'class' => 'form-control', 'required' => 'required']) !!}
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary">Update Value</button>
            </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script>

    $(document).on('submit', '#magento-css-value-edit-form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento-css-value-edit-form"));
        var button = $(this).find('[type="submit"]');

        $.ajax({
            url: '/magento-css-variable/update-value',
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
                button.html('Update Value');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                toastr["success"](response.message);
                location.reload();
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