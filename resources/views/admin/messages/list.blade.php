@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Message</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        

                        <div class="action-section">
                            <?php $page_id = \App\Models\ProjectPage::where('route_url',\Illuminate\Support\Facades\Route::currentRouteName())->pluck('id')->first(); ?>
                            @if(getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) )
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#messageModal" id="AddBtn_AttrSpec"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            @endif
                            
                        </div>

                 

                        <div class="tab-pane fade show active table-responsive" id="messages_tab">
                            <table id="messages_page_table" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="messageModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="messagesform" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formtitle">Add New Message</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="attr-cover-spin" class="cover-spin"></div>
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-form-label" for="title" id="label_title">Message <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="title" name="title" placeholder="">
                            <div id="title-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                       
                        
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="message_id" id="message_id">
                        
                        <button type="button" class="btn btn-outline-primary" id="save_newmessageBtn">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                        <button type="button" class="btn btn-primary" id="save_closemessageBtn">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeletemessageModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Message</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this Message?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="Removemessagesubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- message JS start -->
    <script type="text/javascript">
      
      
        function save_message(btn,btn_type){
            $(btn).prop('disabled',true);
            $(btn).find('.loadericonfa').show();
            var formData = $("#messagesform").serializeArray();

            $.ajax({
                type: 'POST',
                url: "{{ url('admin/addorupdatemessage') }}",
                data: formData,
                success: function (res) {
                    if(res.status == 'failed'){
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled',false);
                        if (res.errors.title) {
                            $('#title-error').show().text(res.errors.title);
                        } else {
                            $('#title-error').hide();
                        }

                    }

                    if(res.status == 200){
                        if(btn_type == 'save_close'){
                            $("#messageModal").modal('hide');
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled',false);
                            if(res.action == 'add'){
                                message_page_tabs();
                                toastr.success("Message Added",'Success',{timeOut: 5000});
                            }
                            if(res.action == 'update'){
                                message_page_tabs();
                                toastr.success("Message Updated",'Success',{timeOut: 5000});
                            }
                        }

                        if(btn_type == 'save_new'){
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled',false);
                            $("#messageModal").find('form').trigger('reset');
                            $('#message_id').val("");
                            $('#title-error').html("");
                            $("#messageModal").find("#save_newmessageBtn").removeAttr('data-action');
                            $("#messageModal").find("#save_closemessageBtn").removeAttr('data-action');
                            $("#messageModal").find("#save_newmessageBtn").removeAttr('data-id');
                            $("#messageModal").find("#save_closemessageBtn").removeAttr('data-id');
                            $("#title").focus();
                            if(res.action == 'add'){
                                message_page_tabs();
                                toastr.success("message Added",'Success',{timeOut: 5000});
                            }
                            if(res.action == 'update'){
                                message_page_tabs();
                                toastr.success("message Updated",'Success',{timeOut: 5000});
                            }
                        }
                    }

                    if(res.status == 400){
                        $("#messageModal").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled',false);
                       
                        toastr.error("Please try again",'Error',{timeOut: 5000});
                    }
                },
                error: function (data) {
                    $("#messageModal").modal('hide');
                    $(btn).find('.loadericonfa').hide();
                    $(btn).prop('disabled',false);
            
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            });
        }

        $('body').on('click', '#save_newmessageBtn', function () {
            save_message($(this),'save_new');
        });

        $('body').on('click', '#save_closemessageBtn', function () {
            save_message($(this),'save_close');
        });

        $('#messageModal').on('shown.bs.modal', function (e) {
            $("#title").focus();
        });

        $('#messageModal').on('hidden.bs.modal', function () {
            $(this).find('form').trigger('reset');
            $(this).find("#save_newmessageBtn").removeAttr('data-action');
            $(this).find("#save_closemessageBtn").removeAttr('data-action');
            $(this).find("#save_newmessageBtn").removeAttr('data-id');
            $(this).find("#save_closemessageBtn").removeAttr('data-id');
            $('#message_id').val("");
            $('#title-error').html("");
        });

        $('#DeletemessageModal').on('hidden.bs.modal', function () {
            $(this).find("#Removemessagesubmit").removeAttr('data-id');
        });

        $(document).ready(function() {
            message_page_tabs('',true);
        });

        function message_page_tabs(tab_type='',is_clearState=false) {
            if(is_clearState){
                $('#messages_page_table').DataTable().state.clear();
            }

            $('#messages_page_table').DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                'stateSave': function(){
                    if(is_clearState){
                        return false;
                    }
                    else{
                        return true;
                    }
                },
                "ajax":{
                    "url": "{{ url('admin/allmessagelist') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{ _token: '{{ csrf_token() }}' ,tab_type: tab_type},
                    // "dataSrc": ""
                },
                'columnDefs': [
                    { "width": "50px", "targets": 0 },
                    { "width": "145px", "targets": 1 },
                    { "width": "165px", "targets": 2 },
                    { "width": "120px", "targets": 3 },
                    { "width": "115px", "targets": 4 },
                ],
                "columns": [
                    {data: 'id', name: 'id', class: "text-center" , orderable: false ,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {data: 'title', name: 'title', class: "text-left"},
                    {data: 'estatus', name: 'estatus', orderable: false, searchable: false, class: "text-center"},
                    {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
                    {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
                ]
            });
        }


        $('body').on('click', '#editmessageBtn', function () {
            var edit_message_id = $(this).attr('data-id');

            $.get("{{ url('admin/messages') }}" +'/' + edit_message_id +'/edit', function (data) {
                $('#messageModal').find('.modal-title').html("Edit message");
                $('#messageModal').find('#save_newmessageBtn').attr("data-action","update");
                $('#messageModal').find('#save_closemessageBtn').attr("data-action","update");
                $('#messageModal').find('#save_newmessageBtn').attr("data-id",edit_message_id);
                $('#messageModal').find('#save_closemessageBtn').attr("data-id",edit_message_id);
                $('#message_id').val(data.id);
                $('#title').val(data.message_text);
                $('#expiry_date').val(data.expiry_date);
                
                
            });
        });

        $('body').on('click', '#deletemessageBtn', function (e) {
            // e.preventDefault();
            var delete_message_id = $(this).attr('data-id');
            $("#DeletemessageModal").find('#Removemessagesubmit').attr('data-id',delete_message_id);
            
           
            $('#DeletemessageModal').find('.modal-title').html("Remove Message");
            $('#DeletemessageModal').find('.modal-body').html("Are you sure you wish to remove this message?");
            
        });

        $('body').on('click', '#Removemessagesubmit', function (e) {
            $('#Removemessagesubmit').prop('disabled',true);
            $(this).find('.removeloadericonfa').show();
            e.preventDefault();
            var remove_message_id = $(this).attr('data-id');


            $.ajax({
                type: 'GET',
                url: "{{ url('admin/messages') }}" +'/' + remove_message_id +'/delete',
                success: function (res) {
                    if(res.status == 200){
                        $("#DeletemessageModal").modal('hide');
                        $('#Removemessagesubmit').prop('disabled',false);
                        $("#Removemessagesubmit").find('.removeloadericonfa').hide();
                        message_page_tabs();
                        toastr.success("message Deleted",'Success',{timeOut: 5000});
                    }

                    if(res.status == 400){
                        $("#DeletemessageModal").modal('hide');
                        $('#Removemessagesubmit').prop('disabled',false);
                        $("#Removemessagesubmit").find('.removeloadericonfa').hide();
                        message_page_tabs();
                        toastr.error("Please try again",'Error',{timeOut: 5000});
                    }
                },
                error: function (data) {
                    $("#DeletemessageModal").modal('hide');
                    $('#Removemessagesubmit').prop('disabled',false);
                    $("#Removemessagesubmit").find('.removeloadericonfa').hide();
                   
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            });
        });

        function chagemessagestatus(message_id) {
            $.ajax({
                type: 'GET',
                url: "{{ url('admin/chagemessagestatus') }}" +'/' + message_id,
                success: function (res) {
                    if(res.status == 200 && res.action=='deactive'){
                        $("#messagestatuscheck_"+message_id).val(2);
                        $("#messagestatuscheck_"+message_id).prop('checked',false);
                        message_page_tabs();
                        toastr.success("Message Deactivated",'Success',{timeOut: 5000});
                    }
                    if(res.status == 200 && res.action=='active'){
                        $("#messagestatuscheck_"+message_id).val(1);
                        $("#messagestatuscheck_"+message_id).prop('checked',true);
                        message_page_tabs();
                        toastr.success("Message activated",'Success',{timeOut: 5000});
                    }
                },
                error: function (data) {
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            });
        }

    </script>
    <!-- attribute JS end-->
@endsection

