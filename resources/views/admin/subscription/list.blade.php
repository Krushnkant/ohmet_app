@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Subscription Price</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{--<h4 class="card-title">User List</h4>--}}

                        <div class="action-section row">
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <?php $page_id = \App\Models\ProjectPage::where('route_url',\Illuminate\Support\Facades\Route::currentRouteName())->pluck('id')->first(); ?>
                                @if(getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) )
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#subscriptionModel" id="AddsubscriptionBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                @endif
                                
                            </div>
                            
                        </div>

                        <div class="tab-pane fade show active table-responsive" id="all_user_tab">
                            <table id="all_subscription" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Price</th>
                                    <th>Title</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Other</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Title</th>
                                    <th>title</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Other</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="subscriptionModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="subscriptionform" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formtitle">Add Subscription Price</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="attr-cover-spin" class="cover-spin"></div>
                        {{ csrf_field() }}
                        <div class="form-group ">
                            <label class="col-form-label" for="price"> Price <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control input-flat" id="price" name="price" min="0" onkeypress="return isNumber(event)" placeholder="">
                            <div id="price-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        <div class="form-group ">
                            <label class="col-form-label" for="title">Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="title" name="title" placeholder="">
                            <div id="title-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        <div class="form-group ">
                            <label class="col-form-label" for="key">Key <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="key" name="key" placeholder="">
                            <div id="key-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        <div class="form-group ">
                            <label class="col-form-label" for="days"> Days <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control input-flat" id="days" name="days" min="0" onkeypress="return isNumber(event)" placeholder="">
                            <div id="days-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="subscription_id" id="subscription_id">
                        <button type="button" class="btn btn-outline-primary" id="save_newsubscriptionBtn">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                        <button type="button" class="btn btn-primary" id="save_closesubscriptionBtn">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeletesubscriptionModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Subscription Price</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this Subscription Price?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemovesubscriptionSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- user list JS start -->
<script type="text/javascript">
    $(document).ready(function() {
        subscription_page_tabs('',true);
    });

    

    function save_subscription(btn,btn_type){
        $(btn).prop('disabled',true);
        $(btn).find('.loadericonfa').show();

        var action  = $(btn).attr('data-action');

        var formData = new FormData($("#subscriptionform")[0]);

        formData.append('action',action);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/addorupdatesubscription') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if(res.status == 'failed'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    
                    if (res.errors.price) {
                        $('#price-error').show().text(res.errors.price);
                    } else {
                        $('#price-error').hide();
                    }

                    if (res.errors.title) {
                        $('#title-error').show().text(res.errors.title);
                    } else {
                        $('#title-error').hide();
                    }

                    if (res.errors.key) {
                        $('#key-error').show().text(res.errors.key);
                    } else {
                        $('#key-error').hide();
                    }

                    if (res.errors.days) {
                        $('#days-error').show().text(res.errors.days);
                    } else {
                        $('#days-error').hide();
                    }

                    
    
                }

                if(res.status == 200){
                    if(btn_type == 'save_close'){
                        $("#subscriptionModel").modal('hide');
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        if(res.action == 'add'){
                            subscription_page_tabs();
                            toastr.success("Subscription Price Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            subscription_page_tabs();
                            toastr.success("Subscription Price Updated",'Success',{timeOut: 5000});
                        }
                    }

                    if(btn_type == 'save_new'){
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        $("#subscriptionModel").find('form').trigger('reset');
                        $("#subscriptionModel").find("#save_newsubscriptionBtn").removeAttr('data-action');
                        $("#subscriptionModel").find("#save_closesubscriptionBtn").removeAttr('data-action');
                        $("#subscriptionModel").find("#save_newsubscriptionBtn").removeAttr('data-id');
                        $("#subscriptionModel").find("#save_closesubscriptionBtn").removeAttr('data-id');
                        $('#subscription_id').val("");
                        $('#price-error').html("");
                        $('#title-error').html("");
                      
                    
                        $("#title").focus();
                        if(res.action == 'add'){
                            subscription_page_tabs();
                            toastr.success("Subscription Price Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            subscription_page_tabs();
                            toastr.success("Subscription Price Updated",'Success',{timeOut: 5000});
                        }
                    }
                }

                if(res.status == 400){
                    $("#subscriptionModel").modal('hide');
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    subscription_page_tabs();
                    if(res.message == ""){
                      toastr.error("Please try again",'Error',{timeOut: 5000});
                    }else{
                        toastr.error(res.message,'Error',{timeOut: 5000});  
                    }
                }
            },
            error: function (data) {
                $("#subscriptionModel").modal('hide');
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();
                subscription_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#save_newsubscriptionBtn', function () {
        save_subscription($(this),'save_new');
    });

    $('body').on('click', '#save_closesubscriptionBtn', function () {
        save_subscription($(this),'save_close');
    });

    $('#subscriptionModel').on('shown.bs.modal', function (e) {
        $("#price").focus();
    });

   

    $('#subscriptionModel').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $(this).find("#save_newsubscriptionBtn").removeAttr('data-action');
        $(this).find("#save_closesubscriptionBtn").removeAttr('data-action');
        $(this).find("#save_newsubscriptionBtn").removeAttr('data-id');
        $(this).find("#save_closesubscriptionBtn").removeAttr('data-id');
        $('#subscription_id').val("");
        $('#price-error').html("");
        $('#title-error').html("");
        
    });

    $('#DeletesubscriptionModel').on('hidden.bs.modal', function () {
        $(this).find("#RemovesubscriptionSubmit").removeAttr('data-id');
    });

    function subscription_page_tabs(tab_type='',is_clearState=false) {
        if(is_clearState){
            $('#all_subscription').DataTable().state.clear();
        }

        $('#all_subscription').DataTable({
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
                "url": "{{ url('admin/allsubscriptionslist') }}",
                "dataType": "json",
                "type": "POST",
                "data":{ _token: '{{ csrf_token() }}' ,tab_type: tab_type},
                // "dataSrc": ""
            },
            'columnDefs': [
                { "width": "50px", "targets": 0 },
                { "width": "145px", "targets": 1 },
                { "width": "165px", "targets": 2 },
                { "width": "75px", "targets": 3 },
                { "width": "120px", "targets": 4 },
                { "width": "115px", "targets": 5 },
            ],
            "columns": [
                {data: 'id', name: 'id', class: "text-center", orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'price', name: 'price', class: "text-center multirow", orderable: false},
                {data: 'title', name: 'title', class: "text-left multirow", orderable: false},
                {data: 'days', name: 'days', class: "text-left multirow", orderable: true},
                {data: 'estatus', name: 'estatus', orderable: false, searchable: false, class: "text-center"},
                {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
                {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
            ]
        });
    }


    function changesubscriptionStatus(subscription_id) {
        //var tab_type = get_users_page_tabType();
       
        $.ajax({
            type: 'GET',
            url: "{{ url('admin/changesubscriptionstatus') }}" +'/' + subscription_id,
            success: function (res) {
                if(res.status == 200 && res.action=='deactive'){
                    $("#subscriptionstatuscheck_"+subscription_id).val(2);
                    $("#subscriptionstatuscheck_"+subscription_id).prop('checked',false);
                    subscription_page_tabs();
                    toastr.success("Subscription Price Deactivated",'Success',{timeOut: 5000});
                }
                if(res.status == 200 && res.action=='active'){
                    $("#subscriptionstatuscheck_"+subscription_id).val(1);
                    $("#subscriptionstatuscheck_"+subscription_id).prop('checked',true);
                    subscription_page_tabs();
                    toastr.success("Subscription Price activated",'Success',{timeOut: 5000});
                }
            },
            error: function (data) {
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#AddsubscriptionBtn', function (e) {
        $("#subscriptionModel").find('.modal-title').html("Add Subscription Price");
    });

    $('body').on('click', '#editsubscriptionBtn', function () {
        var subscription_id = $(this).attr('data-id');
        $.get("{{ url('admin/subscription') }}" +'/' + subscription_id +'/edit', function (data) {
            $('#subscriptionModel').find('.modal-title').html("Edit Subscription Price");
            $('#subscriptionModel').find('#save_closesubscriptionBtn').attr("data-action","update");
            $('#subscriptionModel').find('#save_newsubscriptionBtn').attr("data-action","update");
            $('#subscriptionModel').find('#save_closesubscriptionBtn').attr("data-id",subscription_id);
            $('#subscriptionModel').find('#save_newsubscriptionBtn').attr("data-id",subscription_id);
            $('#subscription_id').val(data.id);
            
            $('#price').val(data.price);
            $('#title').val(data.title);
            $('#key').val(data.key);
            $('#days').val(data.days);
            
        })
    });

    $('body').on('click', '#deletesubscriptionBtn', function (e) {
        var delete_subscription_id = $(this).attr('data-id');
        $("#DeletesubscriptionModel").find('#RemovesubscriptionSubmit').attr('data-id',delete_subscription_id);
    });

    $('body').on('click', '#RemovesubscriptionSubmit', function (e) {
        $('#RemovesubscriptionSubmit').prop('disabled',true);
        $(this).find('.removeloadericonfa').show();
        e.preventDefault();
        var remove_subscription_id = $(this).attr('data-id');
        //var tab_type = get_users_page_tabType();

        $.ajax({
            type: 'GET',
            url: "{{ url('admin/subscription') }}" +'/' + remove_subscription_id +'/delete',
            success: function (res) {
                if(res.status == 200){
                    $("#DeletesubscriptionModel").modal('hide');
                    $('#RemovesubscriptionSubmit').prop('disabled',false);
                    $("#RemovesubscriptionSubmit").find('.removeloadericonfa').hide();
                    subscription_page_tabs();
                    toastr.success("Subscription Price Deleted",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#DeletesubscriptionModel").modal('hide');
                    $('#RemovesubscriptionSubmit').prop('disabled',false);
                    $("#RemovesubscriptionSubmit").find('.removeloadericonfa').hide();
                    subscription_page_tabs();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#DeletesubscriptionModel").modal('hide');
                $('#RemovesubscriptionSubmit').prop('disabled',false);
                $("#RemovesubscriptionSubmit").find('.removeloadericonfa').hide();
                subscription_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });

    
    $('body').on('change', '#type', function () {
        if($(this).val() == 1){
            $("#Amount_label").html("Percentage (%) <span class='text-danger'>*</span>");
        }
        else if($(this).val() == 2){
            $("#Amount_label").html("Amount <span class='text-danger'>*</span>");
        }
    });

    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
<!-- user list JS end -->
@endsection

