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
                            <tr class="subscription-row" data-id="{{ $subscription->id }}" data-url="{{ route('api.subscription.status', $subscription->id) }}">
                                <td>{{ $subscription->transaction_id }}</td>
                                <td>{{ $subscription->user->name }}</td>
                                <td class="status-cell text-info"><span class="spinner-border spinner-border-sm"></span> Getting status...</td>
                                <td class="renewal-cell text-info"><span class="spinner-border spinner-border-sm"></span> Checking...</td>
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
@push('scripts')
<script>
$(document).ready(function () {
    const rows = $('.subscription-row');

    const fetchStatusSequentially = async () => {
        for (let i = 0; i < rows.length; i++) {
            const row = $(rows[i]);
            const url = row.data('url');

            try {
                const response = await $.ajax({
                    url: url,
                    method: 'GET'
                });

                const transaction = response?.data?.[0]?.lastTransactions?.[0];
                const status = transaction?.status === 1 ? 'Active' : 'Expired';
                const autoRenew = transaction?.decodedRenewalInfo?.autoRenewStatus === 0 ? 'Cancelled' : 'Autorenewable';

                row.find('.status-cell').text(status);
                row.find('.renewal-cell').text(autoRenew);
            } catch (error) {
                row.find('.status-cell').text('Error');
                row.find('.renewal-cell').text('Error');
                console.error("Error fetching subscription status", error);
            }
            if(i%10 === 0 && i !== 0) {
                setTimeout(() => {}, 1000); // Optional delay between requests
        }
    };

    fetchStatusSequentially(); // call async function
});
</script>
@endpush