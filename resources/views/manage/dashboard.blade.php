
@extends('layouts.manage')
@section('title')
{{ trans('main.home') }}
@endsection

@section('content')
<div class="page-wrapper">
   <!-- ============================================================== -->
   <!-- Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->
   <div class="page-breadcrumb">
       <div class="row">
           <div class="col-5 align-self-center">
               <h4 class="page-title">{{ trans('main.home') }}</h4>
               <div class="d-flex align-items-center">

               </div>
           </div>
           <div class="col-7 align-self-center">
               <div class="d-flex no-block justify-content-end align-items-center">
                   <nav aria-label="breadcrumb">
                       <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">{{ trans('main.home') }}</li>
                       </ol>
                   </nav>
               </div>
           </div>
       </div>
   </div>
   <!-- ============================================================== -->
   <!-- End Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->
   <!-- ============================================================== -->
   <!-- Container fluid  -->
   <!-- ============================================================== -->
   <div class="container-fluid">
       <!-- ============================================================== -->
       <!-- Info box -->
       <!-- ============================================================== -->
       @include('include.dashboard_staticthic.shop')

       {{-- <div class="card-group">
         <!-- Card -->
         <div class="card">
             <div class="card-body">
                 <div class="d-flex align-items-center">
                     <div class="m-r-10">
                         <span class="btn btn-circle btn-lg bg-danger">
                             <i class="ti-clipboard text-white"></i>
                         </span>
                     </div>
                     <div>
                         New projects
                     </div>
                     <div class="ml-auto">
                         <h2 class="m-b-0 font-light">23</h2>
                     </div>
                 </div>
             </div>
         </div>
         <!-- Card -->
         <!-- Card -->
         <div class="card">
             <div class="card-body">
                 <div class="d-flex align-items-center">
                     <div class="m-r-10">
                         <span class="btn btn-circle btn-lg btn-info">
                             <i class="ti-wallet text-white"></i>
                         </span>
                     </div>
                     <div>
                         Total Earnings

                     </div>
                     <div class="ml-auto">
                         <h2 class="m-b-0 font-light">113</h2>
                     </div>
                 </div>
             </div>
         </div>
         <!-- Card -->
         <!-- Card -->
         <div class="card">
             <div class="card-body">
                 <div class="d-flex align-items-center">
                     <div class="m-r-10">
                         <span class="btn btn-circle btn-lg bg-success">
                             <i class="ti-shopping-cart text-white"></i>
                         </span>
                     </div>
                     <div>
                         Total Sales

                     </div>
                     <div class="ml-auto">
                         <h2 class="m-b-0 font-light">43</h2>
                     </div>
                 </div>
             </div>
         </div>
         <!-- Card -->
         <!-- Card -->
         <div class="card">
             <div class="card-body">
                 <div class="d-flex align-items-center">
                     <div class="m-r-10">
                         <span class="btn btn-circle btn-lg bg-warning">
                             <i class="mdi mdi-currency-usd text-white"></i>
                         </span>
                     </div>
                     <div>
                         Profit

                     </div>
                     <div class="ml-auto">
                         <h2 class="m-b-0 font-light">63</h2>
                     </div>
                 </div>
             </div>
         </div>
         <!-- Card -->
         <!-- Column -->


     </div> --}}

       <!-- ============================================================== -->
       @include('include.dashboard_staticthic.last_shop')
   </div>
</div>


@endsection
