@extends('admin.layout.master')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group float-right">
                    <a type="button" href="{{ route('admin.subscription.create') }}"
                        class="btn btn-primary waves-effect waves-light text-white"><i
                            class="fas fa-plus-square mr-2"></i>Create</a>
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


                    <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
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
                                <tr class="subscription-row" data-id="{{ $subscription->id }}"
                                    data-url="{{ route('api.subscription.status', $subscription->id) }}">
                                    <td>{{ $subscription->transaction_id }}</td>
                                    <td>{{ $subscription->user->name }}</td>
                                    <td class="status-cell text-info"><span class="spinner-border spinner-border-sm"></span>
                                        Getting status...</td>
                                    <td class="renewal-cell text-info"><span class="spinner-border spinner-border-sm"></span>
                                        Checking...</td>
                                    <td>
                                        <a type="button" href="{{ route('admin.subscription.edit', $subscription->id) }}"
                                            class="btn btn-sm btn-success text-white">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a type="button" href="{{ route('admin.subscription.delete', $subscription->id) }}"
                                            class="btn btn-sm btn-danger text-white">
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

            const fetchStatusSequentially = async (rows) => {
                for (let i = 0; i < rows.length; i++) {
                    let row = $(rows[i]);
                    let url = row.data('url');

                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function (response) {
                            const transaction = response?.data?.[0]?.lastTransactions?.[0] || {};

                            let statusText = 'Unknown';
                            let renewalText = 'Unknown';

                            // Apple-style response
                            if ('decodedRenewalInfo' in transaction) {
                                const status = transaction?.status;
                                const autoRenew = transaction?.decodedRenewalInfo?.autoRenewStatus;

                                statusText = status === 1 ? 'Active' : 'Expired';
                                renewalText = autoRenew === 0 ? 'Cancelled' : 'Autorenewable';

                                // Google-style response
                            } else if ('subscriptionInfo' in transaction) {
                                const status = transaction?.paymentState;
                                const autoRenew = transaction?.autoRenewing;

                                statusText = status === 1 ? 'Active' : 'Expired';  // `null` is treated as expired
                                renewalText = autoRenew === false ? 'Cancelled' : 'Autorenewable';
                            }

                            row.find('.status-cell').text(statusText);
                            row.find('.renewal-cell').text(renewalText);
                        },
                        error: function (error) {
                            row.find('.status-cell').text('Error');
                            row.find('.renewal-cell').text('Error');
                            console.error("Error fetching subscription status", error);
                        },
                    });
                    if ((i + 1) % 5 === 0) {
                        await new Promise(resolve => setTimeout(resolve, 1000)); // Wait for 1 second before the next request
                        console.log("Waiting for 1 second before next request...");
                    }
                }
            };

            const rowsOriginal = $('.subscription-row');
            fetchStatusSequentially(rowsOriginal); // call async function

            const table = $('#datatable').DataTable();
            table.on('draw', function () {
                console.log('Table redrawn, fetching status again...');
                const rowsOriginal = $('.subscription-row');
                fetchStatusSequentially(rowsOriginal);
            });
        });
    </script>
@endpush