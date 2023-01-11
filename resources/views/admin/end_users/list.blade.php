@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Customer List</a></li>
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
                            <div class="d-flex">
                            <?php $page_id = \App\Models\ProjectPage::where('route_url','admin.enduser.list')->pluck('id')->first(); ?>
                            @if(getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) )
                                <button type="button" class="btn btn-primary" id="AddEndUserBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            @endif
                            </div>
                        </div>

                        @if(isset($action) && $action=='list')
                        <div class="custom-tab-1">
                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item end_user_page_tabs" data-tab="all_end_user_tab"><a class="nav-link active show" data-toggle="tab" href="">All</a>
                                </li>
                        
                                <li class="nav-item end_user_page_tabs" data-tab="active_end_user_tab"><a class="nav-link" data-toggle="tab" href="">Active</a>
                                </li>
                                <li class="nav-item end_user_page_tabs" data-tab="deactive_end_user_tab"><a class="nav-link" data-toggle="tab" href="">Deactive</a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-pane fade show active table-responsive" id="all_end_user_tab">
                            <table id="all_end_users" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Profile</th>
                                    <th>Contact Info</th>
                                    <th>Other Info</th>
                                    <th>User</th>
                                    <th>User Status</th>
                                    <th>Registration Date</th>
                                    <th>Other</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Profile</th>
                                    <th>Contact Info</th>
                                    <th>Other Info</th>
                                    <th>User</th>
                                    <th>User Status</th>
                                    <th>Registration Date</th>
                                    <th>Other</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        @endif

                        @if(isset($action) && $action=='create')
                            @include('admin.end_users.create')
                        @endif

                        @if(isset($action) && $action=='edit')
                            @include('admin.end_users.create')
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteEndUserModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Customer</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this customer?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemoveEndUserSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="{{ asset('js/UserImgJs.js') }}" type="text/javascript"></script>
<!-- end_user list JS start -->
<script type="text/javascript">
 $('#language_id').select2({
    width: '100%',
    multiple: true,
    placeholder: "Select Language",
    allowClear: true,
    autoclose: false,
    closeOnSelect: false,
});

$(document).ready(function() {
    end_user_page_tabs('',true);
});

function get_end_users_page_tabType(){
    var tab_type;
    $('.end_user_page_tabs').each(function() {
        var thi = $(this);
        if($(thi).find('a').hasClass('show')){
            tab_type = $(thi).attr('data-tab');
        }
    });
    return tab_type;
}

function save_user(btn,btn_type){
    $(btn).prop('disabled',true);
    $(btn).find('.loadericonfa').show();

    var action  = $(btn).attr('data-action');

    var formData = new FormData($("#enduserform")[0]);

    formData.append('action',action);

    var tab_type = get_end_users_page_tabType();

    $.ajax({
        type: 'POST',
        url: "{{ url('admin/addorupdateEnduser') }}",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if(res.status == 'failed'){
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();
                
                if (res.errors.age) {
                    $('#age-error').show().text(res.errors.age);
                } else {
                    $('#age-error').hide();
                }

                if (res.errors.first_name) {
                    $('#first_name-error').show().text(res.errors.first_name);
                } else {
                    $('#first_name-error').hide();
                }

                if (res.errors.last_name) {
                    $('#last_name-error').show().text(res.errors.last_name);
                } else {
                    $('#last_name-error').hide();
                }

                if (res.errors.mobile_no) {
                    $('#mobileno-error').show().text(res.errors.mobile_no);
                } else {
                    $('#mobileno-error').hide();
                }

                if (res.errors.dob) {
                    $('#dob-error').show().text(res.errors.dob);
                } else {
                    $('#dob-error').hide();
                }

                if (res.errors.email) {
                    $('#email-error').show().text(res.errors.email);
                } else {
                    $('#email-error').hide();
                }

                if (res.errors.location) {
                    $('#location-error').show().text(res.errors.location);
                } else {
                    $('#location-error').hide();
                }

                if (res.errors.rate_per_minite) {
                    $('#rate_per_minite-error').show().text(res.errors.rate_per_minite);
                } else {
                    $('#rate_per_minite-error').hide();
                }

                if (res.errors.userImg) {
                    $('#imagethumb-error').show().text(res.errors.userImg);
                } else {
                    $('#imagethumb-error').hide();
                }

                if (res.errors.userVideo) {
                    $('#videothumb-error').show().text(res.errors.userVideo);
                } else {
                    $('#videothumb-error').hide();
                }

                if (res.errors.userShotVideo) {
                    $('#shortvideothumb-error').show().text(res.errors.userShotVideo);
                } else {
                    $('#shortvideothumb-error').hide();
                }
            }

            if(res.status == 200){
                if(btn_type == 'save_close'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    location.href="{{ route('admin.end_users.list')}}";
                    if(res.action == 'add'){
                        toastr.success("Customer Added",'Success',{timeOut: 5000});
                    }
                    if(res.action == 'update'){
                        toastr.success("Customer Updated",'Success',{timeOut: 5000});
                    }
                    
                }

                if(btn_type == 'save_new'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    location.href="{{ route('admin.enduser.add')}}";
                    if(res.action == 'add'){
                        toastr.success("Customer Added",'Success',{timeOut: 5000});
                    }
                    if(res.action == 'update'){
                        toastr.success("Customer Updated",'Success',{timeOut: 5000});
                    }
                    
                }
            }

            if(res.status == 400){
                $("#EndUserModal").modal('hide');
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();
                end_user_page_tabs(tab_type);
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        },
        error: function (data) {
            $("#EndUserModal").modal('hide');
            $(btn).prop('disabled',false);
            $(btn).find('.loadericonfa').hide();
            end_user_page_tabs(tab_type);
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
}

$('body').on('click', '#save_newEndUserBtn', function () {
    save_user($(this),'save_new');
});

$('body').on('click', '#save_closeEndUserBtn', function () {
    save_user($(this),'save_close');
});







$('#DeleteEndUserModal').on('hidden.bs.modal', function () {
    $(this).find("#RemoveEndUserSubmit").removeAttr('data-id');
});

function end_user_page_tabs(tab_type='',is_clearState=false) {
    if(is_clearState){
        $('#all_end_users').DataTable().state.clear();
    }

    $('#all_end_users').DataTable({
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
            "url": "{{ url('admin/allEnduserlist') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}' ,tab_type: tab_type},
            // "dataSrc": ""
        },
        'columnDefs': [
            { "width": "50px", "targets": 0 },
            { "width": "145px", "targets": 1 },
            { "width": "145px", "targets": 2 },
            { "width": "200px", "targets": 3 },
            { "width": "50px", "targets": 4 },
            { "width": "120px", "targets": 5 },
            { "width": "120px", "targets": 6 },
            { "width": "180px", "targets": 7 },
        ],
        "columns": [
            {data: 'id', name: 'id', class: "text-center", orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'profile_pic', name: 'profile_pic', class: "text-center multirow"},
            {data: 'contact_info', name: 'contact_info', class: "text-left multirow", orderable: false},
            {data: 'login_info', name: 'login_info', class: "text-left multirow", orderable: false},
            {data: 'user', name: 'user', class: "text-left multirow", orderable: false},
            {data: 'estatus', name: 'estatus', orderable: false, searchable: false, class: "text-center"},
            {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
            {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
        ]
    });
}

$(".end_user_page_tabs").click(function() {
    var tab_type = $(this).attr('data-tab');
    end_user_page_tabs(tab_type,true);
});

function changeEndUserStatus(user_id) {
    var tab_type = get_end_users_page_tabType();
     
    $.ajax({
        type: 'GET',
        url: "{{ url('admin/changeEnduserstatus') }}" +'/' + user_id,
        success: function (res) {
            if(res.status == 200 && res.action=='deactive'){
                $("#EndUserstatuscheck_"+user_id).val(2);
                $("#EndUserstatuscheck_"+user_id).prop('checked',false);
                end_user_page_tabs(tab_type);
                toastr.success("Customer Deactivated",'Success',{timeOut: 5000});
            }
            if(res.status == 200 && res.action=='active'){
                $("#EndUserstatuscheck_"+user_id).val(1);
                $("#EndUserstatuscheck_"+user_id).prop('checked',true);
                end_user_page_tabs(tab_type);
                toastr.success("Customer activated",'Success',{timeOut: 5000});
            }
        },
        error: function (data) {
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
}



$('body').on('click', '#AddEndUserBtn', function () {
    location.href = "{{ route('admin.enduser.add') }}";
});

// $('body').on('click', '#editEndUserBtn', function () {
//     var user_id = $(this).attr('data-id');
 
//     //$("#parent_user_id option[value='"+ user_id +"']").remove();
    
//     $.get("{{ url('admin/end_users') }}" +'/' + user_id +'/edit', function (data) {
//         //console.log(data);
        
//         $('#EndUserModal').find('.modal-title').html("Edit Customer");
//         $('#EndUserModal').find('#save_closeEndUserBtn').attr("data-action","update");
//         $('#EndUserModal').find('#save_newEndUserBtn').attr("data-action","update");
//         $('#EndUserModal').find('#save_closeEndUserBtn').attr("data-id",user_id);
//         $('#EndUserModal').find('#save_newEndUserBtn').attr("data-id",user_id);
//         $('#user_id').val(data.id);
//         if(data.profile_pic==null){
//             var default_image = "{{ url('public/images/default_avatar.jpg') }}";
//             $('#profilepic_image_show').attr('src', default_image);
//         }
//         else{
//             var profile_pic = "{{ url('public/images/profile_pic') }}" +"/" + data.profile_pic;
//             $('#profilepic_image_show').attr('src', profile_pic);
//         }

//         if(data.adhar_front==null){
//             var default_image = "{{ url('public/images/default_avatar.jpg') }}";
//             $('#adhar_front_show').attr('src', default_image);
//         }
//         else{
//             var adhar_front = "{{ url('public/images/adhar_front') }}" +"/" + data.adhar_front;
//             $('#adhar_front_show').attr('src', adhar_front);
//         }

//         if(data.adhar_back==null){
//             var default_image = "{{ url('public/images/default_avatar.jpg') }}";
//             $('#profilepic_image_show').attr('src', default_image);
//         }
//         else{
//             var adhar_back = "{{ url('public/images/adhar_back') }}" +"/" + data.adhar_back;
//             $('#adhar_back_show').attr('src', adhar_back);
//         }
      
//         $('#first_name').val(data.first_name);
//         $('#last_name').val(data.last_name);
//         $('#mobile_no').val(data.mobile_no);
//         $('#dob').val(data.dob);
//         $('#email').val(data.email);
//         $('#password').val(data.decrypted_password);
//         $('#adhar_card_no').val(data.adhar_card_no);
//         $("input[name=gender][value=" + data.gender + "]").prop('checked', true);
//         //$('option:selected', "#parent_user_id").remove();    
//         //alert(data.parent_user_id);
//         $("#parent_user_id option:selected").each(function () {
//                $(this).removeAttr('selected'); 
//         });
//         $("#parent_user_id option[value="+ data.parent_user_id + "]").attr("selected",true).trigger('change');;
//         //$("#parent_user_id").select2('val', data.parent_user_id);
//         $('#parent_user_id').select2({
//             width: '100%',
//             placeholder: "Select Referral Users",
//             allowClear: true
//         }).trigger('change');
      
//         if(data.is_premium == 1){
//             $("input[name=is_premium]").attr('checked', true);
//             $("input[name=is_premium]").val(1);
//             $("#is_premium_div").hide();
//             $("#email_div").show();
//             $("#password_div").show();
//             $("#adhar_div").show();
//             $("#adhar_photo_div").show();
//         }
//         else{
//             $("input[name=is_premium]").attr('checked', false);
//             $("input[name=is_premium]").val(0);
//             $("#is_premium_div").show();
//             $("#email_div").hide();
//             $("#password_div").hide();
//             $("#adhar_div").hide();
//             $("#adhar_photo_div").hide();
//         }
//     })
// });

$('body').on('click', '#deleteEndUserBtn', function (e) {
    // e.preventDefault();
    var delete_user_id = $(this).attr('data-id');
    $("#DeleteEndUserModal").find('#RemoveEndUserSubmit').attr('data-id',delete_user_id);
});

$('body').on('click', '#RemoveEndUserSubmit', function (e) {
    $('#RemoveEndUserSubmit').prop('disabled',true);
    $(this).find('.removeloadericonfa').show();
    e.preventDefault();
    var remove_user_id = $(this).attr('data-id');

    var tab_type = get_end_users_page_tabType();

    $.ajax({
        type: 'GET',
        url: "{{ url('admin/end_users') }}" +'/' + remove_user_id +'/delete',
        success: function (res) {
            if(res.status == 200){
                $("#DeleteEndUserModal").modal('hide');
                $('#RemoveEndUserSubmit').prop('disabled',false);
                $("#RemoveEndUserSubmit").find('.removeloadericonfa').hide();
                end_user_page_tabs(tab_type);
                toastr.success("Customer Deleted",'Success',{timeOut: 5000});
            }

            if(res.status == 400){
                $("#DeleteEndUserModal").modal('hide');
                $('#RemoveEndUserSubmit').prop('disabled',false);
                $("#RemoveEndUserSubmit").find('.removeloadericonfa').hide();
                end_user_page_tabs(tab_type);
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        },
        error: function (data) {
            $("#DeleteEndUserModal").modal('hide');
            $('#RemoveEndUserSubmit').prop('disabled',false);
            $("#RemoveEndUserSubmit").find('.removeloadericonfa').hide();
            end_user_page_tabs(tab_type);
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
});

$(document).on('change', '#is_premium', function(e) {
    var action = $('#EndUserModal').find('#save_closeEndUserBtn').attr("data-action");

    if ($(this).is(':checked')) {
        $(this).val(1);
        $(this).attr('checked', true);
        $("#email_div").show();
        $("#password_div").show();
        $("#adhar_div").show();
        $("#adhar_photo_div").show();
    }
    else {
        $(this).val(0);
        $(this).attr('checked', false);
        $("#email_div").hide();
        $("#password_div").hide();
        $("#adhar_div").hide();
        $("#adhar_photo_div").hide();
    }
});

$('body').on('click', '.is_premium_user', function (e) {
        var user_id = $(this).attr('data-id');
        var url = "{{ url('admin/end_users_level') }}" + "/" + user_id;
        window.open(url,"_blank");
});

$('body').on('click', '.viewUserMonthlyBtn', function () {
    var id = $(this).attr('data-id');
    var url = "{{ url('admin/viewClieldUser') }}" + "/" + id;
    window.open(url,"_blank");
});

$('body').on('click', '#editEndUserBtn', function () {
    var user_id = $(this).attr('data-id');
    var url = "{{ url('admin/end_users') }}" + "/" + user_id + "/edit";
    window.open(url,"_blank");
});

function removeuploadedimg(divId ,inputId, imgName){
    if(confirm("Are you sure you want to remove this file?")){
        $("#"+divId).remove();
        $("#"+inputId).removeAttr('value');
        var filerKit = $("#userIconFiles").prop("jFiler");
        filerKit.reset();
    }
}



</script>

<!-- end_user list JS end -->
@endsection

