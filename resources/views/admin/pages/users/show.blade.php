@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>User Details - {{ $user->name }}</h2>

    <div class="card p-4 mb-4">
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID:</strong> {{ $user->id }}</p>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Mobile:</strong> {{ $user->mobile }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
                <p><strong>Block Status:</strong>
                    <span class="badge {{ $user->is_block ? 'bg-danger' : 'bg-success' }}">
                        {{ $user->is_block ? 'Blocked' : 'Unblocked' }}
                    </span>
                </p>
            </div>

            <div class="col-md-6">
                <h5>Wallets</h5>
                <p><strong>Deposit:</strong> {{ number_format($user->deposit_wallet,2) }}</p>
                <p><strong>Active:</strong> {{ number_format($user->active_wallet,2) }}</p>
                <p><strong>Profit:</strong> {{ number_format($user->profit_wallet,2) }}</p>

                <!-- Wallet Modal Trigger -->
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#walletModal">
                    Manage Wallets
                </button>
            </div>
        </div>
        <button class="btn btn-primary btn-sm mt-2 mb-2" data-bs-toggle="modal" data-bs-target="#editUserModal">Edit User</button>
        <form action="{{ route('admin.users.toggleBlock', $user->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-warning btn-sm w-100">{{ $user->is_block ? 'Unblock' : 'Block' }}</button>
        </form>
    </div>

    <div class="card p-4">
        <h5>Team Stats</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Level</th>
                    <th>Total Users</th>
                    <th>Total Investment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teamData as $level => $data)
                <tr>
                    <td>{{ $level }}</td>
                    <td>{{ $data['total'] }}</td>
                    <td>{{ number_format($data['totalInvestment'],2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('admin.pages.users.partials.wallet-modal')
@include('admin.pages.users.partials.edit-user-modal')

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');

    forms.forEach(function(form) {
        let isSubmitting = false; // form submission guard

        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                // যদি ইতিমধ্যে সাবমিট হয়ে থাকে, submission block
                e.preventDefault();
                return false;
            }
            isSubmitting = true; // এবার form সাবমিট শুরু

            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            }
        });
    });
});
</script>