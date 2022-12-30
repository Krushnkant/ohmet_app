@extends('admin.layout')

@section('content')
{{--    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Home</a></li>
            </ol>
        </div>
    </div>--}}
    <!-- row -->
    <?php $page_id = \App\Models\ProjectPage::where('route_url',\Illuminate\Support\Facades\Route::currentRouteName())->pluck('id')->first(); ?>
    @if(getUSerRole()==1 || (getUSerRole()!=1 && is_read($page_id)) )
   
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="container-fluid mt-3">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6">
                                <div class="card gradient-1">
                                    <div class="card-body">
                                        <h3 class="card-title text-white">Today Orders</h3>
                                        <div class="d-inline-block">
                                            <h2 class="text-white"> 500 </h2>
                                            <p class="text-white mb-0">Yesterday Orders</p>
                                            <h4 class="text-white mb-0"> 55 </h4>
                                        </div>
                                        <span class="float-right display-5 opacity-5"><i class="fa fa-shopping-cart"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="card gradient-2">
                                    <div class="card-body">
                                        <h3 class="card-title text-white">Today Sales</h3>
                                        <div class="d-inline-block">
                                            <h2 class="text-white">$ 55 </h2>
                                            <p class="text-white mb-0">Yester Sales</p>
                                            <h4 class="text-white mb-0">$ 55 </h4>
                                        </div>
                                        <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="card gradient-3">
                                    <div class="card-body">
                                        <h3 class="card-title text-white">Today Inquiry</h3>
                                        <div class="d-inline-block">
                                            <h2 class="text-white"> 50000 </h2>
                                            <p class="text-white mb-0">Today Opinion</p>
                                            <h4 class="text-white mb-0"> 532532 </h4>
                                        </div>
                                        <span class="float-right display-5 opacity-5"><i class="fa fa-heart"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="card gradient-4">
                                    <div class="card-body">
                                        <h3 class="card-title text-white">New Users</h3>
                                        <div class="d-inline-block">
                                            <h2 class="text-white"> 555 </h2>
                                            <p class="text-white mb-0"> All Users</p>
                                            <h4 class="text-white mb-0"> 2222 </h4>
                                        </div>
                                        <span class="float-right display-5 opacity-5"><i class="fa fa-users"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>       
            </div>

    </div>

  
    
    @endif
    @endsection
    @section('js')
    <!-- user list JS start -->
    
@endsection
