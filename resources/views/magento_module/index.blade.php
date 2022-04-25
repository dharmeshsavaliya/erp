@extends('layouts.app')



@section('title', $title)

@section('styles')

    <style>
        .users {
            display: none;
        }

        table.dataTable thead th {
            padding: 5px 5px !important;
        }
        table.dataTable tbody th, table.dataTable tbody td {
            padding: 5px 5px !important;
        }
        .copy_remark{
            cursor: pointer;
        }
    </style>

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.jqueryui.min.css"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
        
        .disabled{
            pointer-events: none;
            background: #bababa;
        }
        .glyphicon-refresh-animate {
            -animation: spin .7s infinite linear;
            -webkit-animation: spin2 .7s infinite linear;
        }

        @-webkit-keyframes spin2 {
            from { -webkit-transform: rotate(0deg);}
            to { -webkit-transform: rotate(360deg);}
        }

        @keyframes spin {
            from { transform: scale(1) rotate(0deg);}
            to { transform: scale(1) rotate(360deg);}
        }

    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>

    @php
    $message_send_to = [
        'to_master' => 'Send To Master Developer',
        'to_developer' => 'Send To Developer',
        'to_team_lead' => 'Send To Team Lead',
        'to_tester' => 'Send To Tester',
    ];
    @endphp

    <div class="row ">
        <div class="col-lg-12 ">
            <h2 class="page-heading">{{ $title }}
            </h2>

            <form method="POST" action="#" id="dateform">

                <div class="row m-4">
                    <div class="col-xs-3 col-sm-3">
                        <div class="form-group">
                            {!! Form::text('module', null, ['placeholder' => 'Module Name', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {!! Form::select('module_type', $magento_module_types, null, ['placeholder' => 'Select Module Type', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {!! Form::select('module_category_id', $module_categories, null, ['placeholder' => 'Select Module Category', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {!! Form::select('is_customized', ['No', 'Yes'], null, ['placeholder' => 'Customized', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {!! Form::select('store_website_id', $store_websites, null, ['placeholder' => 'Store Website', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-xs-2 col-sm-1 pt-2 ">
                        <div class="d-flex" >
                            <div class="form-group pull-left ">
                                <button type="submit" class="btn btn-image search">
                                    <img src="/images/search.png" alt="Search" style="cursor: inherit;">
                                </button>
                            </div>
                            <div class="form-group pull-left ">
                                <button type="submit" id="searchReset" class="btn btn-image search ml-3">
                                    <img src="/images/resend2.png" alt="Search" style="cursor: inherit;">
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group pull-right ml-3 mt-3">
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#moduleTypeCreateModal"> Module Type Create </button>
                    
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#moduleCategoryCreateModal"> Module Category Create </button>

                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#moduleCreateModal"> Magento Module Create </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive mt-3 pr-2 pl-2">
        @if ($message = Session::get('success'))
            <div class="col-lg-12">
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        <div class="erp_table_data">
            <table class="table table-bordered " id="erp_table">
                <thead>
                    <tr>
                        <th> Id </th>
                        <th width="200px"> Remark </th>
                        <th> Category </th>
                        <th> Description </th>
                        <th> Website </th>
                        <th> Name </th>
                        <th> API </th>
                        <th> Cron </th>
                        <th> Version </th>
                        <th> Type </th>
                        <th> Payment Status</th>
                        <th> Status </th>
                        <th> Developer Name </th>
                        <th> Customized </th>
                        <th> js/css </th>
                        <th> 3rd Party Js </th>
                        <th> Sql </th>
                        <th> 3rd Party plugin </th>
                        <th> Site Impact </th>
                        <th> Action </th>
    
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        
    </div>

    {{-- #blank-modal --}}
    @include('partials.plain-modal')
    {{-- #remark-area-list --}}
    @include('magento_module.partials.remark_list')
    {{-- moduleTypeCreateModal --}} {{-- moduleTypeEditModal --}}
    @include('magento_module_type.partials.form_modals')
    {{-- moduleCategoryCreateModal --}} {{-- moduleCategoryEditModal --}}
    @include('magento_module_category.partials.form_modals')
    {{-- moduleCreateModal --}} {{-- moduleEditModal --}}
    @include('magento_module.partials.form_modals')
    {{-- apiDataAddModal --}}
    @include('magento_module.partials.api_form_modals')
    {{-- cronJobDataAddModal --}}
    @include('magento_module.partials.cron_form_modals')
    {{-- apiDataShowModal --}}
    @include('magento_module.partials.api_data_show_modals')
    {{-- cronJobDataShowModal --}}
    @include('magento_module.partials.cron_data_show_modals')
    {{-- JsRequireDataAddModal --}}
    @include('magento_module.partials.js_require_form_modals')
    {{-- JsRequireDataShowModal --}}
    @include('magento_module.partials.js_require_show_modals')
    {{-- isCustomizedDataAddModal --}}
    @include('magento_module.partials.is_customized_form_modals')
    {{-- isCustomizedDataShowModal --}}
    @include('magento_module.partials.is_customized_show_modals')
    {{-- magentoModuleHistoryShowModal --}}
    @include('magento_module.partials.show_history_modals')


@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    {{-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap.min.js"></script> --}}

    <script>
        $(document).on('click', '#searchReset', function(e) {
            //alert('success');
            $('#dateform').trigger("reset");
            e.preventDefault();
            oTable.draw();
        });

        $('#dateform').on('submit', function(e) {
            e.preventDefault();
            oTable.draw();

            return false;
        });

        $('#extraSearch').on('click', function(e) {
            e.preventDefault();
            oTable.draw();
        });

        // START Print Table Using datatable
        var oTable;
        $(document).ready(function() {
            oTable = $('#erp_table').DataTable({
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                sScrollX: true,
                order: [
                    [0, 'desc']
                ],
                targets: 'no-sort',
                bSort: false,

                oLanguage: {
                    sLengthMenu: "Show _MENU_",
                },
                createdRow: function(row, data, dataIndex) {
                    // Set the data-status attribute, and add a class
                    $(row).attr('role', 'row');
                    $(row).find("td").last().addClass('text-danger');
                },
                ajax: {
                    "url": "{{ route('magento_modules.index') }}",
                    data: function(d) {
                        d.module = $('input[name=module]').val();
                        d.module_type = $('select[name=module_type]').val();
                        d.is_customized = $('select[name=is_customized]').val();
                        d.module_category_id = $('select[name=module_category_id]').val();
                        d.task_status = $('select[name=task_status]').val();
                        d.store_website_id = $('select[name=store_website_id]').val();
                        // d.view_all = $('input[name=view_all]:checked').val(); // for Check box
                    },
                },
                columnDefs: [{
                    targets: [],
                    orderable: false,
                    searchable: false,
                    // className: 'mdl-data-table__cell--non-numeric'
                }],
                columns: [{
                        data: 'id',
                        name: 'magento_modules.id',
                        render: function(data, type, row, meta) {
                            var html = '<input type="hidden" name="mm_id" class="data_id" value="'+data+'">';
                            return html + data;
                        }
                    },
                    {
                        data: 'last_message',
                        name: 'magento_modules.last_message',
                        render: function(data, type, row, meta) {
                            
                            let message = `<input type="text" id="remark_${row['id']}" name="remark" class="form-control" placeholder="Remark" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-remark ml-2" data-id="${row['id']}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveRemarks(${row['id']})"><img src="/images/filled-sent.png"></button>`;
                                data = (data == null) ? '' : `<div class="flex items-center justify-left" title="${data}">${setStringLength(data, 15)}</div>`;
                            let retun_data = `${data} <div class="d-flex"> ${message} ${remark_send_button} ${remark_history_button} </div>`;
                            
                            return retun_data;
                        }
                    },
                    {
                        data: 'category_name',
                        name: 'magento_module_categories.category_name',
                        render: function(data, type, row, meta) {
                            var m_types = row['categories'];
                            var m_types =  m_types.replace(/&quot;/g, '"');
                            if(m_types && m_types != "" ){
                                var m_types = JSON.parse(m_types);
                                var m_types_html = '<select id="module_category_id" class="form-control edit_mm" required="required" name="module_category_id"><option selected="selected" value="">Select Module Category</option>';
                                m_types.forEach(function(m_type){
                                    if(m_type.category_name == data){
                                        m_types_html += `<option value="${m_type.id}" selected>${m_type.category_name}</option>`;
                                    }else{
                                        m_types_html += `<option value="${m_type.id}" >${m_type.category_name}</option>`;
                                    }
                                    
                                });
                                m_types_html += '</select>';
                                return m_types_html;
                            }else{
                                return `<div class="flex items-center justify-left">${data}</div>`;
                            }
                            
                        }
                        
                    },
                    {
                        data: 'module_description',
                        name: 'magento_modules.module_description',
                        render: function(data, type, row, meta) {
                            var status_array = ['Disabled', 'Enable'];
                            return `<div class="flex items-center justify-left" title="${data}">${setStringLength(data, 15)}</div>`;
                        }
                    },
                    {
                        data: 'website',
                        name: 'store_websites.website',
                        render: function(data, type, row, meta) {
                            var m_types = row['website_list'];
                            var m_types =  m_types.replace(/&quot;/g, '"');
                            if(m_types && m_types != "" ){
                                var m_types = JSON.parse(m_types);
                                var m_types_html = '<select id="module_category_id" class="form-control edit_mm" required="required" name="store_website_id"><option selected="selected" value="">Select Module Category</option>';
                                m_types.forEach(function(m_type){
                                    if(m_type.website == data){
                                        m_types_html += `<option value="${m_type.id}" selected> ${m_type.website}</option>`;
                                    }else{
                                        m_types_html += `<option value="${m_type.id}" >${m_type.website}</option>`;
                                    }
                                    
                                });
                                m_types_html += '</select>';
                                return m_types_html;
                            }else{
                                return `<div class="flex items-center justify-left" title="${data}">${setStringLength(data, 15)}</div>`;
                            }
                            
                        }
                    },
                    {
                        data: 'module',
                        name: 'magento_modules.module',
                    },
                    {
                        data: 'api',
                        name: 'magento_modules.api',
                        render: function(data, type, row, meta) {
                            var html = '<select id="api" class="form-control edit_mm" name="api"><option selected="selected" value="">Select API</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';
                            let add_button = `<button type="button" class="btn btn-xs add-api-data-modal" title="Add Api Details" data-id="${row['id']}"><i class="fa fa-plus"></i></button>`;
                            let show_button = `<button type="button" class="btn btn-xs show-api-modal" title="Show Api History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            let html_data = ``;
                            
                            if(data == 1){
                                html_data = `<div class="d-flex"> ${html}  ${add_button} ${show_button} </div>`;
                            }else{
                                html_data = `<div class="d-flex"> ${html}  ${show_button} </div>`;
                            }
                            return html_data;
                        }
                    },
                    {
                        data: 'cron_job',
                        name: 'magento_modules.cron_job',
                        render: function(data, type, row, meta) {
                            
                            var html = '<select id="cron_job" class="form-control edit_mm" name="cron_job"><option selected="selected" value="">Select Cron Job</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';

                            
                            let add_button = `<button type="button" class="btn btn-xs add-cron_job-modal" title="Add Cron Details" data-id="${row['id']}"><i class="fa fa-plus"></i></button>`;
                            let show_button = `<button type="button" class="btn btn-xs show-cron_job-modal" title="Show Cron History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            
                            if(data == 1){
                                html_data = `<div class="d-flex"> ${html}  ${add_button} ${show_button} </div>`;
                            }else{
                                html_data = `<div class="d-flex"> ${html}  ${show_button} </div>`;
                            }
                            return  html_data;
                        }
                    },
                    
                    {
                        data: 'current_version',
                        name: 'magento_modules.current_version',
                    },
                    {
                        data: 'magento_module_type',
                        name: 'magento_module_types.magento_module_type',
                        render: function(data, type, row, meta) {
                            var m_types = row['m_types'];
                            var m_types =  m_types.replace(/&quot;/g, '"');
                            if(m_types && m_types != "" ){
                                var m_types = JSON.parse(m_types);
                                var m_types_html = '<select id="module_type" class="form-control edit_mm" name="module_type"><option selected="selected" value="">Select Module Type</option>';
                                m_types.forEach(function(m_type){
                                    if(m_type.magento_module_type == data){
                                        m_types_html += `<option value="${m_type.id}" selected>${m_type.magento_module_type}</option>`;
                                    }else{
                                        m_types_html += `<option value="${m_type.id}" >${m_type.magento_module_type}</option>`;
                                    }
                                    
                                });
                                m_types_html += '</select>';
                                return m_types_html;
                            }else{
                                return `<div class="flex items-center justify-left">${data}</div>`;
                            }
                            
                        }
                    },
                    {
                        data: 'payment_status',
                        name: 'magento_modules.payment_status',
                        render: function(data, type, row, meta) {
                            
                            var html = '<select id="payment_status" class="form-control edit_mm" name="payment_status"><option selected="selected" value="">Select Payment Status</option>';
                                html += '<option value="Free" '+(data == 'Free' ? 'selected' : '')+'>Free</option><option value="Paid" '+(data == 'Paid' ? 'selected' : '')+'>Paid</option>';
                            html +='</select>';
                            return  html;
                        }
                    },
                    {
                        data: 'status',
                        name: 'magento_modules.status',
                        render: function(data, type, row, meta) {
                            var status_array = ['Disabled', 'Enable'];
                           
                            var html = '<select id="status" class="form-control edit_mm"  name="status"><option selected="selected" value="">Select Status</option>';
                                html += '<option value="Enable" '+(status_array[data] == 'Enable' ? 'selected' : '')+'>Enable</option><option value="Disabled" '+(status_array[data] == 'Disabled' ? 'selected' : '')+'>Disabled</option>';
                            html +='</select>';

                            return `<div class="flex items-center justify-left">${html}</div>`;
                        }
                    },
                    {
                        data: 'developer_id',
                        name: 'users.name',
                        render: function(data, type, row, meta) {
                            
                            var dev_list = row['developer_list'];
                            var dev_list =  dev_list.replace(/&quot;/g, '"');
                            if(dev_list && dev_list != "" ){
                                var dev_html = '<select id="developer_name" class="form-control edit_mm" name="developer_name"><option selected="selected" value="">Select developer name</option>';
                                var dev_list = JSON.parse(dev_list);
                                dev_list.forEach(function(dev){
                                    dev_html += `<option value="${dev.id}" `+(dev.id == data ? 'selected' :'') +`>${dev.name}</option>`;
                                });
                                dev_html +="</select>";
                            }
                            return `<div class="flex items-center justify-left">${dev_html}</div>`;
                        }
                    },
                    {
                        data: 'is_customized',
                        name: 'magento_modules.is_customized', 
                        render: function(data, type, row, meta) {
                            
                            var html = '<select id="is_customized" class="form-control edit_mm"  name="is_customized"><option selected="selected" value="">Customized</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';
                            
                            let add_button = `<button type="button" class="btn btn-xs add-is_customized-modal" title="Add 3rd party JS Details" data-id="${row['id']}"><i class="fa fa-plus"></i></button>`;
                            let show_button = `<button type="button" class="btn btn-xs show-is_customized-modal" title="Show 3rd party JS History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            
                            if(data == 1){
                                html_data = `<div class="d-flex"> ${html}  ${add_button} ${show_button} </div>`;
                            }else{
                                html_data = `<div class="d-flex"> ${html}  ${show_button} </div>`;
                            }
                            return html_data;
                        }
                    },
                    {
                        data: 'is_js_css',
                        name: 'magento_modules.is_js_css',
                        render: function(data, type, row, meta) {
                             
                            var html = '<select id="is_js_css" class="form-control edit_mm"  name="is_js_css"><option selected="selected" value="">Select Javascript/css Require</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';

                            return html;
                        }
                    },
                    {
                        data: 'is_third_party_js',
                        name: 'magento_modules.is_third_party_js',
                        render: function(data, type, row, meta) {

                            var html = '<select id="is_third_party_js" class="form-control edit_mm"  name="is_third_party_js"><option selected="selected" value="">Select Javascript/css Require</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';

                            let add_button = `<button type="button" class="btn btn-xs add-third_party_js-modal" title="Add Customized Details" data-id="${row['id']}"><i class="fa fa-plus"></i></button>`;
                            let show_button = `<button type="button" class="btn btn-xs show-third_party_js-modal" title="Show Customized History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            
                            
                            if(data == 1){
                                html_data = `<div class="d-flex"> ${html} ${add_button} ${show_button} </div>`;
                            }else{
                                html_data = `<div class="d-flex"> ${html} ${show_button} </div>`;
                            }
                            return html_data;
                        }
                    },
                    {
                        data: 'is_sql',
                        name: 'magento_modules.is_sql',
                        render: function(data, type, row, meta) {
                            var html = '<select id="is_sql" class="form-control edit_mm"  name="is_sql"><option selected="selected" value="">Select Sql Query Status</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';

                            return html;
                        }
                    },
                    {
                        data: 'is_third_party_plugin',
                        name: 'magento_modules.is_third_party_plugin',
                        render: function(data, type, row, meta) {
                            var html = '<select id="is_third_party_plugin" class="form-control edit_mm"  name="is_third_party_plugin"><option selected="selected" value="">Select Third Party Plugin</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';

                            return html;
                        }
                    },
                    {
                        data: 'site_impact',
                        name: 'magento_modules.site_impact',
                        render: function(data, type, row, meta) {
                            var html = '<select id="site_impact" class="form-control edit_mm"  name="site_impact"><option selected="selected" value="">Site Impact</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';

                            return html;
                        }
                    },
                    {
                        data: 'id',
                        name: 'magento_modules.id',
                        // visible:false,
                        render: function(data, type, row, meta) {
                            row["m_types"] = "";
                            row["developer_list"] = "";
                            row["categories"] = "";
                            row["website_list"] = "";
                            var show_data = actionShowButtonWithClass('show-details', row['id']);
                            var edit_data = actionEditButtonWithClass('edit-magento-module', JSON.stringify(row));
                            let history_button = `<button type="button" class="btn btn-xs show-magenato_module_history-modal" title="Show History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            var del_data = actionDeleteButton(row['id']);
                            return `<div class="flex justify-left items-center"> ${show_data} ${history_button} ${edit_data} ${del_data} </div>`;
                        }
                    },
                ],
            });
        });
        // END Print Table Using datatable

        // Delete Module 
        $(document).on('click', '.clsdelete', function() {
            var id = $(this).attr('data-id');
            var e = $(this).parent().parent();
            var url = `{{ url('/') }}/magento_modules/` + id;
            tableDeleteRow(url, oTable);
        });

        //Status Update 
        $(document).on('click', '.clsstatus', function() {
            var id = $(this).attr('data-id');
            var status = $(this).attr('data-status');
            var url = `{{ url('/') }}/magento_modules/status/` + id + `/` + status;
            tableChnageStatus(url, oTable);
        });

        // Load All Module Details
        $(document).on("click", ".show-details", function() {

            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ url('/') }}/magento_modules/` + id,
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    if (response.code == 200) {
                        $("#blank-modal").find(".modal-title").html(response.title);
                        $("#blank-modal").find(".modal-body").html(response.data);
                        $("#blank-modal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
        
        // Store Reark
        function saveRemarks(row_id) {
            console.log(row_id);
            var remark = $("#remark_" + row_id).val();
            // var send_to = $("#send_to_" + row_id).val();

            var val = $("#remark_" + row_id).val();

            $.ajax({
                url: `{{ route('magento_module_remark.store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remark: remark,
                    // send_to: send_to,
                    magento_module_id: row_id
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                if (response.status) {
                    $("#remark_" + row_id).val('');
                    $("#send_to_" + row_id).val('');
                    toastr["success"](response.message);
                    oTable.draw();
                } else {
                    toastr["error"](response.message);
                }
                $("#loading-image").hide();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                if (jqXHR.responseJSON.errors !== undefined) {
                    $.each(jqXHR.responseJSON.errors, function(key, value) {
                        // $('#validation-errors').append('<div class="alert alert-danger">' + value + '</div');
                        toastr["warning"](value);
                    });
                } else {
                    toastr["error"]("Oops,something went wrong");
                }
                $("#loading-image").hide();
            });
        }


        $(document).on("click", ".add-api-data-modal", function() {
            let magento_module_id = $(this).data('id');
            $("#apiDataAddModal").find('[name="magento_module_id"]').val(magento_module_id);
            $('#apiDataAddModal').modal('show');
        });

        $(document).on("click", ".add-cron_job-modal", function() {
            let magento_module_id = $(this).data('id');
            $("#cronJobDataAddModal").find('[name="magento_module_id"]').val(magento_module_id);
            $('#cronJobDataAddModal').modal('show');
        });

        $(document).on("click", ".add-third_party_js-modal", function() {
            let magento_module_id = $(this).data('id');
            $("#JsRequireDataAddModal").find('[name="magento_module_id"]').val(magento_module_id);
            $('#JsRequireDataAddModal').modal('show');
        });

        $(document).on("click", ".add-is_customized-modal", function() {
            let magento_module_id = $(this).data('id');
            $("#isCustomizedDataAddModal").find('[name="magento_module_id"]').val(magento_module_id);
            $('#isCustomizedDataAddModal').modal('show');
        });
        
        // Load Remark
        $(document).on('click', '.load-module-remark', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_remark.get_remarks', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.remark } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                        <td><i class='fa fa-copy copy_remark' data-remark_text='${v.remark}'></i></td>
                                    </tr>`;
                        });
                        $("#remark-area-list").find(".remark-action-list-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#remark-area-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        // Load Api Modal
        $(document).on('click', '.show-api-modal', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_api_histories.show', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${v.id } </td>
                                        <td> ${v.resources } </td>
                                        <td> ${v.frequency } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${getDateByFormat(v.created_at) } </td>
                                    </tr>`;
                        });
                        $("#apiDataShowModal").find(".api-details-data-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#apiDataShowModal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        // Load cron job Modal
        $(document).on('click', '.show-cron_job-modal', function() {
            var id = $(this).attr('data-id');
            
            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_cron_job_histories.show', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${v.id } </td>
                                        <td> ${v.cron_time } </td>
                                        <td> ${v.frequency } </td>
                                        <td> ${v.cpu_memory } </td>
                                        <td> ${v.comments } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${getDateByFormat(v.created_at) } </td>
                                    </tr>`;
                        });
                        $("#cronJobDataShowModal").find(".cron-job-details-data-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#cronJobDataShowModal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
        
        // Load Js Require Modal
        $(document).on('click', '.show-third_party_js-modal', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_js_require_histories.show', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${v.id } </td>
                                        <td> ${(v.files_include  == 1)? 'Yes': 'No'  } </td>
                                        <td> ${(v.native_functionality  == 1)? 'Yes': 'No'  } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${getDateByFormat(v.created_at) } </td>
                                    </tr>`;
                        });
                        $("#JsRequireDataShowModal").find(".js-require-details-data-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#JsRequireDataShowModal").modal("show");
                        
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        // Load Js Require Modal
        $(document).on('click', '.show-is_customized-modal', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_customized_histories.show', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${v.id } </td>
                                        <td> ${(v.magento_standards == 1)? 'Yes': 'No' } </td>
                                        <td> ${v.remark } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${getDateByFormat(v.created_at) } </td>
                                    </tr>`;
                        });
                        $("#isCustomizedDataShowModal").find(".is-customized-details-data-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#isCustomizedDataShowModal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        // Show History
        $(document).on('click', '.show-magenato_module_history-modal', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_histories.show', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> <span title="" > ${v.id } </span> </td>
                                        <td> <span title="${(v.module_category !== null) ? v.module_category.category_name : ' - ' }" > ${(v.module_category !== null) ? v.module_category.category_name : ' - ' } </span> </td>
                                        <td> <span title="${(v.store_website !== null) ? v.store_website.website : ' - ' }" > ${(v.store_website !== null) ? setStringLength(v.store_website.website) : ' - ' } </span> </td>
                                        <td> <span title="${ v.module }" > ${ v.module } </span> </td>
                                        <td> <span title="${ v.module_description }" > ${ setStringLength(v.module_description) } </span> </td>
                                        <td> <span title="${ v.current_version }" > ${ v.current_version } </span> </td>
                                        <td> <span title="${(v.module_type_data !== null) ? v.module_type_data.magento_module_type : ' - ' }" > ${(v.module_type_data !== null) ? v.module_type_data.magento_module_type : ' - ' } </span> </td>
                                        <td> <span title="${(v.task_status_data !== null) ? v.task_status_data.name : ' - ' }" > ${(v.task_status_data !== null) ? v.task_status_data.name : ' - ' } </span> </td>
                                        <td> <span title="${(v.is_sql == 1)? 'Yes': 'No' }" > ${(v.is_sql == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${(v.api == 1)? 'Yes': 'No' }" > ${(v.api == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${(v.cron_job == 1)? 'Yes': 'No' }" > ${(v.cron_job == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${(v.is_third_party_plugin == 1)? 'Yes': 'No' }" > ${(v.is_third_party_plugin == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${(v.is_third_party_js == 1)? 'Yes': 'No' }" > ${(v.is_third_party_js == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${(v.is_customized == 1)? 'Yes': 'No' }" > ${(v.is_customized == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${(v.is_js_css == 1)? 'Yes': 'No' }" > ${(v.is_js_css == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${v.payment_status }" > ${v.payment_status } </span> </td>
                                        <td> <span title="${(v.developer_name_data !== null) ? v.developer_name_data.name : ' - ' }" > ${(v.developer_name_data !== null) ? v.developer_name_data.name : ' - ' } </span> </td>
                                        <td> <span title="${(v.user !== null) ? v.user.name : ' - ' }" > ${(v.user !== null) ? v.user.name : ' - ' } </span> </td>
                                        <td> <span title="" > ${getDateByFormat(v.created_at) } </span> </td>
                                    </tr>`;
                        });
                        $("#magentoModuleHistoryShowModal").find(".js-magento-module-history-data-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#magentoModuleHistoryShowModal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
        $(document).on('click', '.set-remark', function() {
            var id = $(this).attr('data-mm_id');
            $.ajax({
                type: "POST",
                url: "{{route('task.create.get.remark')}}",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    task_id : task_id,
                    remark : remark,
                    type : "TASK",
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function (response) {
                if(response.code == 200) {
                    $("#loading-image").hide();
                    $("#preview-task-create-get-modal").modal("show");
                    $(".task-create-get-list-view").html(response.data);
                    $('.remark_pop').val("");
                    toastr['success'](response.message);
                }else{
                    $("#loading-image").hide();
                    $("#preview-task-create-get-modal").modal("show");
                    $(".task-create-get-list-view").html("");
                    toastr['error'](response.message);
                }
                
            }).fail(function (response) {
                $("#loading-image").hide();
                $("#preview-task-create-get-modal").modal("show");
                $(".task-create-get-list-view").html("");
                toastr['error'](response.message);
            });
        });
        $(document).on("click",".copy_remark",function(e) {
            var thiss = $(this);
            var remark_text = thiss.data('remark_text');
            copyToClipboard(remark_text);
            /* Alert the copied text */
            toastr['success']("Copied the text: " + remark_text);
            
        });
        function copyToClipboard(text) {
            var sampleTextarea = document.createElement("textarea");
            document.body.appendChild(sampleTextarea);
            sampleTextarea.value = text; //save main text in it
            sampleTextarea.select(); //select textarea contenrs
            document.execCommand("copy");
            document.body.removeChild(sampleTextarea);
        }
        $(document).on('change', '.edit_mm', function() {
            var  column = $(this).attr('name');
            var value = $(this).val();
            var data_id = $(this).parents('tr').find('.data_id').val();
            
            $.ajax({
                type: "POST",
                url: "{{route('magento_module.update.option')}}",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    columnName : column,
                    data : value,
                    id : data_id
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function (response) {
                if(response.code == 200) {
                    $("#loading-image").hide();
                     oTable.draw();
                    toastr['success'](response.message);
                }else{
                    $("#loading-image").hide();
                    oTable.draw();
                    toastr['error'](response.message);
                }
                
            }).fail(function (response) {
                $("#loading-image").hide();
                oTable.draw();
                console.log("failed");
                toastr['error'](response.message);
            });
        });
    </script>

@endsection
