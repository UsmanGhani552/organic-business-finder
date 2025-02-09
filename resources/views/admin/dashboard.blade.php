@extends('admin.layout.master')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group float-right">
                <ol class="breadcrumb hide-phone p-0 m-0">
                    <li class="breadcrumb-item"><a href="#">Zoogler</a></li>
                    <li class="breadcrumb-item"><a href="#">Tables</a></li>
                    <li class="breadcrumb-item active">Basic Tables</li>
                </ol>
            </div>
            <h4 class="page-title">Basic Tables</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="card">                                
            <div class="card-body">
                <h5 class="header-title pb-3 mt-0">Payments</h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="align-self-center">
                                <th>Project Name</th>
                                <th>Client Name</th>
                                <th>Payment Type</th>
                                <th>Paid Date</th>
                                <th>Amount</th>
                                <th>Transaction</th>                                                                                    
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Product Devlopment</td>
                                <td>
                                    <img src="assets/images/users/avatar-1.jpg" alt="" class="thumb-sm rounded-circle mr-2">
                                    Kevin Heal
                                </td>
                                <td>Paypal</td>
                                <td>5/8/2018</td>
                                <td>$15,000</td>
                                <td><span class="badge badge-boxed  badge-soft-warning">panding</span></td>                                                                        
                            </tr>
                            <tr>
                                <td>New Office Building</td>
                                <td>
                                    <img src="assets/images/users/avatar-2.jpg" alt="" class="thumb-sm rounded-circle mr-2">
                                    Frank M. Lyons
                                </td>
                                <td>Paypal</td>
                                <td>15/7/2018</td> 
                                <td>$35,000</td> 
                                <td><span class="badge badge-boxed  badge-soft-primary">Success</span></td>
                            </tr>
                            
                            <tr>
                                <td>Market Research</td>
                                <td>
                                    <img src="assets/images/users/avatar-3.jpg" alt="" class="thumb-sm rounded-circle mr-2">
                                    Angelo Butler
                                </td>
                                <td>Pioneer</td>
                                <td>30/9/2018</td>                                                                        
                                <td>$45,000</td>
                                <td><span class="badge badge-boxed  badge-soft-warning">Panding</span></td>
                            </tr>
                            
                            <tr>
                                <td>Website &amp; Blog</td>
                                <td>
                                    <img src="assets/images/users/avatar-4.jpg" alt="" class="thumb-sm rounded-circle mr-2">
                                    Phillip Morse
                                </td>
                                <td>Paypal</td>
                                <td>2/6/2018</td>
                                <td>$70,000</td>
                                <td><span class="badge badge-boxed  badge-soft-warning">Success</span></td>
                            </tr>
                            <tr>
                                <td>Product Devlopment</td>
                                <td>
                                    <img src="assets/images/users/avatar-5.jpg" alt="" class="thumb-sm rounded-circle mr-2">
                                    Kevin Heal
                                </td>
                                <td>Paypal</td>
                                <td>5/8/2018</td>
                                <td>$15,000</td>
                                <td><span class="badge badge-boxed  badge-soft-primary">panding</span></td>                                                                        
                            </tr>
                            <tr>
                                <td>New Office Building</td>
                                <td>
                                    <img src="assets/images/users/avatar-6.jpg" alt="" class="thumb-sm rounded-circle mr-2">
                                    Frank M. Lyons
                                </td>
                                <td>Paypal</td>
                                <td>15/7/2018</td> 
                                <td>$35,000</td> 
                                <td><span class="badge badge-boxed  badge-soft-primary">Success</span></td>
                            </tr>                                                                        
                        </tbody>
                    </table>
                </div><!--end table-responsive-->
                <div class="pt-3 border-top text-right">
                    <a href="#" class="text-primary">View all <i class="mdi mdi-arrow-right"></i></a>
                </div> 
            </div>
        </div>                                                                   
    </div> 
</div>
@endsection

