@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Purchase Coin</a></li>
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

                        <div class="tab-pane fade show active table-responsive" id="all_user_tab">
                            <table id="all_pricerange" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Amount</th>
                                    <th>Coin</th>
                                    <th>Payment Type</th>
                                    <th>Transaction Id</th>
                                    <th>Date</th>
                                 
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Amount</th>
                                    <th>Coin</th>
                                    <th>Payment Type</th>
                                    <th>Transaction Id</th>
                                    <th>Date</th>
                                   
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>




@endsection

@section('js')
<!-- user list JS start -->
<script type="text/javascript">
    $(document).ready(function() {
        pricerange_page_tabs('',true);
    });


    function pricerange_page_tabs(tab_type='',is_clearState=false) {
        if(is_clearState){
            $('#all_pricerange').DataTable().state.clear();
        }

        $('#all_pricerange').DataTable({
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
                "url": "{{ url('admin/allpurchasecoinslist') }}",
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
                {data: 'amount', name: 'amount', class: "text-center multirow", orderable: false},
                {data: 'coin', name: 'coin', class: "text-center multirow", orderable: false},
                {data: 'payment_type', name: 'payment_type', class: "text-center multirow", orderable: false},
                {data: 'transaction_id', name: 'transaction_id', class: "text-left multirow", orderable: false},
                {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
            ]
        });
    }


</script>
<!-- user list JS end -->
@endsection

