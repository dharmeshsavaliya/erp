<div id="testcaseEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="edit-h3">Edit Test Case</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route'=> ['test-cases.update' ]  ]) !!}

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif

                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label> Name </label>
                    <input class="form-control name" name="name" type="text">
                    <input class="form-control id" name="id" type="hidden">
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                </div>

                <div class="form-group {{ $errors->has('suite') ? 'has-error' : '' }}">
                    <label> Suite </label>
                    <input class="form-control suite" name="suite" type="text">
                    <span class="text-danger">{{ $errors->first('suite') }}</span>
                </div>


                <div class="form-group" {{ $errors->has('module_id') ? 'has-error' : '' }}>
                    <label> Module </label>
                    <select class="form-control module_id" name="module_id">
                        <option value="">Select Module</option>
                        @foreach($filterCategories as  $filterCategory)
                            <option value="{{$filterCategory}}">{{$filterCategory}} </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group {{ $errors->has('precondition') ? 'has-error' : '' }}">
                    <label> Precondition </label>
                    <textarea class="form-control precondition" name="precondition" > </textarea>
                    <span class="text-danger">{{ $errors->first('precondition') }}</span>
                </div>


                <div class="form-group {{ $errors->has('step_to_reproduce') ? 'has-error' : '' }}">
                    <label> Step To Reproduce </label>
                    <textarea class="form-control step_to_reproduce" name="step_to_reproduce" > </textarea>
                    <span class="text-danger">{{ $errors->first('step_to_reproduce') }}</span>
                </div>

                <div class="form-group {{ $errors->has('expected_result') ? 'has-error' : '' }}">
                    <label> Expected Result </label>
                    <input class="form-control expected_result" name="expected_result" type="text">
                    <span class="text-danger">{{ $errors->first('expected_result') }}</span>
                </div>


                <div class="form-group" {{ $errors->has('test_status_id') ? 'has-error' : '' }}>
                    <label> Status </label>
                    <select class="form-control test_status_id" name="test_status_id">
                        <option value="">Select Status</option>
                        @foreach($testCaseStatuses as  $testCaseStatus)
                            <option value="{{$testCaseStatus->id}}">{{$testCaseStatus->name}} </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" {{ $errors->has('website') ? 'has-error' : '' }}>
                    <label> Website </label>
                    <select class="form-control website" name="website">
                        <option value="">Select Website</option>
                        @foreach($filterWebsites as  $filterWebsite)
                            <option value="{{$filterWebsite->id}}">{{$filterWebsite->title}} </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-secondary">Store</button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>