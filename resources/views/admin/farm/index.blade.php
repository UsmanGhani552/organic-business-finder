@extends('admin.layout.master')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            {{-- <div class="btn-group float-right">
                <a type="button" href="{{ route('admin.farm.create') }}" class="btn btn-primary waves-effect waves-light text-white"><i class="fas fa-plus-square mr-2"></i>Create</a>
        </div> --}}
        <h4 class="page-title">Farms</h4>
    </div>
</div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <strong>{{ $message }}</strong>
            </div>
            @endif
            <div class="card-body">


                <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created By</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>


                    <tbody>
                        @foreach ($farms as $farm)
                        <tr>
                            <td>{{ $farm->name }}</td>
                            <td>{{ $farm->email }}</td>
                            <td>{{ $farm->users->name }}</td>
                            <td><img src="{{ asset('images/farm/'.$farm->image) }}" alt="" class="thumb-sm mr-1"></td>
                            <td>
                                <a type="button" href="{{ route('admin.farm.edit',$farm->id) }}" class="btn btn-sm btn-warning text-white btn-view" data-id={{ $farm->id }} data-toggle="modal" data-target="#myModal">
                                    <i class="fas fa-eye"></i>
                                </a>
                                {{-- <a type="button" href="{{ route('admin.farm.edit',$farm->id) }}" class="btn btn-sm btn-success text-white">
                                <i class="fas fa-edit"></i>
                                </a> --}}
                                <a type="button" href="{{ route('admin.farm.delete',$farm->id) }}" class="btn btn-sm btn-danger text-white">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="col-sm-6 col-md-3 mt-4">
                    <!-- sample modal content -->
                    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModalLabel">Farm Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <div class="modal-body">
                                    <!-- Farm Info Section -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Name:</strong> <span id="farmName"></span></p>
                                            <p><strong>Email:</strong> <span id="farmEmail"></span></p>
                                            <p><strong>Location:</strong> <span id="farmLocation"></span></p>
                                            <p><strong>Phone:</strong> <span id="farmPhone"></span></p>
                                            <p><strong>Website:</strong> <a href="#" id="farmWebsite" target="_blank"></a></p>
                                            <p><strong>Timings:</strong> <span id="farmTimings"></span></p>
                                            <p><strong>Delivery Options:</strong> <span id="farmDelieveryOption"></span></p>
                                        </div>
                                        <div class="col-md-6 text-center">
                                            <p><strong>Image:</strong></p>
                                            <img id="farmImage" class="img-fluid rounded" style="max-height: 200px;" alt="Farm Image">
                                        </div>
                                    </div>
                    
                                    <hr>
                    
                                    <!-- Categories Section -->
                                    <h5>Categories</h5>
                                    <ul id="categories" class="list-unstyled"></ul>
                    
                                    <hr>
                    
                                    <!-- Payments Section -->
                                    <h5>Payment Methods</h5>
                                    <ul id="payments" class="list-unstyled"></ul>
                    
                                    <hr>
                    
                                    <!-- Days Section -->
                                    <h5>Operational Days</h5>
                                    <ul id="days" class="list-unstyled"></ul>
                    
                                    <hr>
                    
                                    <!-- Products Section -->
                                    <h5>Product Details</h5>
                                    <div id="productDetails" class="row"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div> 
</div> 
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('.btn-view').click(function() {
            var farmId = $(this).data('id'); 

            // AJAX request
            $.ajax({
    url: '/admin/farm/' + farmId,
    type: 'GET',
    success: function (response) {
        // Populate modal fields
        $('#farmName').text(response.farm.name);
        $('#farmEmail').text(response.farm.email);
        $('#farmImage').attr("src", response.farm.image);
        $('#farmLocation').text(response.farm.location);
        $('#farmPhone').text(response.farm.phone);
        $('#farmWebsite').text(response.farm.website).attr('href', response.farm.website);
        $('#farmDescription').text(response.farm.description);
        $('#farmTimings').text(response.farm.timings);
        $('#farmDelieveryOption').text(response.farm.delivery_option);

        // Clear previous data
        $('#categories').empty();
        $('#payments').empty();
        $('#days').empty();
        $('#productDetails').empty();

        // Populate Categories
        $(response.farm.categories).each(function (index, category) {
            $('#categories').append(`<li>${category.name}</li>`);
        });

        // Populate Payment Methods
        $(response.farm.payments).each(function (index, payment) {
            $('#payments').append(`<li>${payment.name}</li>`);
        });

        // Populate Days
        $(response.farm.days).each(function (index, day) {
            $('#days').append(`<li>${day.name}</li>`);
        });

        // Populate Products
        $(response.farm.products).each(function (index, product) {
            const baseUrl = window.location.origin;
            const imageUrl = `${baseUrl}/images/product/${product.image}`;
            $('#productDetails').append(`
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <img src="${imageUrl}" class="card-img-top" alt="${product.name}">
                        <div class="card-body">
                            <h6 class="card-title">${product.name}</h6>
                            <p class="card-text"><strong>Price:</strong> ${product.price}</p>
                        </div>
                    </div>
                </div>
            `);
        });

        // Show the modal
        $('#myModal').modal('show');
    },
    error: function (xhr) {
        console.log(xhr.responseText);
    }
});

        });
    });

</script>
@endpush
