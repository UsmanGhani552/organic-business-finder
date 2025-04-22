@extends('admin.layout.master')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group float-right">
                <a type="button" href="{{ route('admin.subscription.create') }}" class="btn btn-primary waves-effect waves-light text-white"><i class="fas fa-plus-square mr-2"></i>Create</a>
            </div>
            <h4 class="page-title">Categories</h4>
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
                            <th>Transaction Id</th>
                            <th>User Name</th>
                            <th>Status</th>
                            <th>Cancel Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>


                    <tbody>
                        @foreach ($subscriptions as $subscription)
                        <tr>
                            <td>{{ $subscription->transaction_id }}</td>
                            <td>{{ $subscription->user->name }}</td>
                            <td>{{ $subscription->status == 1 ? 'Active' : 'Expired' }}</td>
                            <td>{{ $subscription->auto_renew_status == 0 ? 'Cancelled' : 'Autorenewable' }}</td>
                            <td>
                                <a type="button" href="{{ route('admin.subscription.edit',$subscription->id) }}" class="btn btn-sm btn-success text-white">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a type="button" href="{{ route('admin.subscription.delete',$subscription->id) }}" class="btn btn-sm btn-danger text-white">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
@endsection
