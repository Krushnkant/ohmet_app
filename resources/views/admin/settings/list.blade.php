@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Settings</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        {{--<h4 class="card-title">
                            Settings List
                        </h4>--}}
                        <div class="col-lg-12">

                        <div class="table-responsive">
                            <table id="" class="table table-striped table-bordered customNewtable" style="width:100%">
                            <thead>
                                    <tr>
                                        <th><h4 class="text-white mt-0 mb-0">Setting</h4></th>
                                        <th colspan="2" class="text-right">
                                            <button id="editInvoiceBtn" class="btn btn-outline-white btn-sm" data-toggle="modal" data-target="#InvoiceModal">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                <tr>
                                        <th style="width: 50%">Company Name</th>
                                        <td><span id="company_name_val">{{ $Settings->company_name }}</span></td>
                                    </tr>
                                    <tr>
                                        <th style="width: 50%">Company Logo</th>
                                        <td>
                                            @if(isset($Settings->company_logo))
                                                <img src="{{ url('images/company/'.$Settings->company_logo) }}" width="150px" height="" alt="Company Logo" id="company_logo_val">
                                            @else
                                                <img src="{{ url('images/placeholder_image.png') }}" width="150px" height="" alt="Company Logo" id="company_logo_val">
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th style="width: 50%">Company Favicon</th>
                                        <td>
                                            @if(isset($Settings->company_favicon))
                                                <img src="{{ url('images/company/'.$Settings->company_favicon) }}" width="150px" height="" alt="Company Favicon" id="company_favicon_val">
                                            @else
                                                <img src="{{ url('images/placeholder_image.png') }}" width="150px" height="" alt="Company Favicon" id="company_favicon_val">
                                            @endif
                                        </td>
                                    </tr>

                                    
                                    

                                   
                                </tbody>
                            </table>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="InvoiceModal">
        <div class="modal-dialog modal-dialog-centered mw-100 w-50" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="InvoiceForm" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h5 class="modal-title">Update Settings</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    </div>
                    <div class="modal-body">
                        <div id="attr-cover-spin" class="cover-spin"></div>
                        <div class="form-group row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="col-form-label" for="Company Name">Company Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control input-flat" id="company_name" name="company_name" placeholder="">
                                <div id="company_name-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label class="col-form-label" for="Logo">Company Logo <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control-file" id="company_logo" name="company_logo" placeholder="">
                            <div id="company_logo-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                            <img src="{{ url('images/placeholder_image.png') }}" class="" id="company_logo_image_show" height="50px" width="50px" style="margin-top: 5px">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label class="col-form-label" for="favicon">Company Favicon <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control-file" id="company_favicon" name="company_favicon" placeholder="">
                            <div id="company_favicon-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                            <img src="{{ url('images/placeholder_image.png') }}" class="" id="company_favicon_image_show" height="50px" width="50px" style="margin-top: 5px">
                        </div>
                        </div>
                        
                       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveInvoiceBtn">Save <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('js')
<!-- settings JS start -->
<script type="text/javascript">
    $('#InvoiceModal').on('shown.bs.modal', function (e) {
        $("#prefix_invoice_no").focus();
    });

    $('#InvoiceModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $('#prefix_invoice_no-error').html("");
        $('#invoice_no-error').html("");
        $('#company_name-error').html("");
        $('#company_logo-error').html("");
        $('#company_favicon-error').html("");
        
        var default_image = "{{ url('images/placeholder_image.png') }}";
        $('#company_logo_image_show').attr('src', default_image);
    });

    $('body').on('click', '#editInvoiceBtn', function () {
        $.get("{{ url('admin/settings/edit') }}", function (data) {
           
            $('#prefix_invoice_no').val(data.prefix_invoice_no);
            $('#invoice_no').val(data.invoice_no);
            $('#company_name').val(data.company_name);
            
            if(data.company_logo==null){
                var default_image = "{{ url('images/placeholder_image.png') }}";
                $('#company_logo_image_show').attr('src', default_image);
            }
            else{
                var company_logo = "{{ url('images/company') }}" +"/" + data.company_logo;
                $('#company_logo_image_show').attr('src', company_logo);
            }

            if(data.company_favicon==null){
                var default_image = "{{ url('images/placeholder_image.png') }}";
                $('#company_favicon_image_show').attr('src', default_image);
            }
            else{
                var company_favicon = "{{ url('images/company') }}" +"/" + data.company_favicon;
                $('#company_favicon_image_show').attr('src', company_favicon);
            }
        })
    });

    $('body').on('click', '#saveInvoiceBtn', function () {
        $('#saveInvoiceBtn').prop('disabled',true);
        $('#saveInvoiceBtn').find('.loadericonfa').show();
        var formData = new FormData($("#InvoiceForm")[0]);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/updateInvoiceSetting') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if(res.status == 'failed'){
                    $('#saveInvoiceBtn').prop('disabled',false);
                    $('#saveInvoiceBtn').find('.loadericonfa').hide();
                    if (res.errors.prefix_invoice_no) {
                        $('#prefix_invoice_no-error').show().text(res.errors.prefix_invoice_no);
                    } else {
                        $('#prefix_invoice_no-error').hide();
                    }

                    if (res.errors.invoice_no) {
                        $('#invoice_no-error').show().text(res.errors.invoice_no);
                    } else {
                        $('#invoice_no-error').hide();
                    }

                    if (res.errors.company_name) {
                        $('#company_name-error').show().text(res.errors.company_name);
                    } else {
                        $('#company_name-error').hide();
                    }

                    if (res.errors.company_logo) {
                        $('#company_logo-error').show().text(res.errors.company_logo);
                    } else {
                        $('#company_logo-error').hide();
                    }

                    
                }

                if(res.status == 200){
                    $("#InvoiceModal").modal('hide');
                    $('#saveInvoiceBtn').prop('disabled',false);
                    $('#saveInvoiceBtn').find('.loadericonfa').hide();

                    $("#company_name_val").html(res.Settings.company_name);
                    var logo = "{{ url('images/company') }}" + "/" + res.Settings.company_logo;
                    if(res.Settings.company_logo!="" && res.Settings.company_logo!=null) {
                        $('#company_logo_val').attr('src', logo);
                    }

                    var favicon = "{{ url('images/company') }}" + "/" + res.Settings.company_favicon;
                    if(res.Settings.company_favicon!="" && res.Settings.company_favicon!=null) {
                        $('#company_favicon_val').attr('src', favicon);
                    }
                   
                   
                    toastr.success("Settings Updated",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#InvoiceModal").modal('hide');
                    $('#saveInvoiceBtn').prop('disabled',false);
                    $('#saveInvoiceBtn').find('.loadericonfa').hide();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#InvoiceModal").modal('hide');
                $('#saveInvoiceBtn').prop('disabled',false);
                $('#saveInvoiceBtn').find('.loadericonfa').hide();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });

    $('#company_logo').change(function(){
        $('#company_logo-error').hide();
        var file = this.files[0];
        var fileType = file["type"];
        var validImageTypes = ["image/jpeg", "image/png", "image/jpg"];
        if ($.inArray(fileType, validImageTypes) < 0) {
            $('#company_logo-error').show().text("Please provide a Valid Extension Logo(e.g: .jpg .png)");
            var default_image = "{{ url('public/images/placeholder_image.png') }}";
            $('#company_logo_image_show').attr('src', default_image);
        }
        else {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#company_logo_image_show').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    $('#company_favicon').change(function(){
        $('#company_favicon-error').hide();
        var file = this.files[0];
        var fileType = file["type"];
        var validImageTypes = ["image/jpeg", "image/png", "image/jpg"];
        if ($.inArray(fileType, validImageTypes) < 0) {
            $('#company_favicon-error').show().text("Please provide a Valid Extension Logo(e.g: .jpg .png)");
            var default_image = "{{ url('public/images/placeholder_image.png') }}";
            $('#company_favicon_image_show').attr('src', default_image);
        }
        else {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#company_favicon_image_show').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>
<!-- settings JS end -->
@endsection
