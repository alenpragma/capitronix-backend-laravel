<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.updateStatusBtn').forEach(button => {
            button.addEventListener('click', function() {
                let userId = this.dataset.id;
                let userName = this.dataset.name;
                let userEmail = this.dataset.email;
                let userWallet = this.dataset.activeWallet;
                let userDepositWallet = this.dataset.depositWallet;
                let blockStatus = this.dataset.block;
                let walletStatus = this.dataset.wallet;

                document.getElementById('modal_user_id').value = userId;
                document.getElementById('modal_user_name').value = userName;
                document.getElementById('modal_user_email').value = userEmail;
                document.getElementById('modal_user_active_wallet').value = userWallet;
                document.getElementById('modal_user_deposit_wallet').value = userDepositWallet;
                document.getElementById('modal_block_status').value = blockStatus;
                document.getElementById('modal_wallet_status').value = walletStatus;
            });
        });
    });
</script>

@extends('admin.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">All Users</h4>
        </div>

        <div class="card-body table-responsive">
            <form method="GET" action="{{ route('users.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <select name="filter" class="form-control">
                            <option value="">-- Filter Users --</option>
                            <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Active Users</option>
                            <option value="inactive" {{ request('filter') == 'inactive' ? 'selected' : '' }}>Inactive Users</option>
                            <option value="blocked" {{ request('filter') == 'blocked' ? 'selected' : '' }}>Blocked Users</option>
                            <option value="unblocked" {{ request('filter') == 'unblocked' ? 'selected' : '' }}>Unblocked Users</option>
                        </select>

                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary" type="submit">Filter</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <table class="table table-striped table-hover table-head-bg-primary mt-3">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Country</th>
                    <th>Wallet Balance</th>
                    <th>Profit Wallet</th>
                    <th>Active Wallet</th>
                    <th>Referred By</th>
                    <th>Status</th>
                    <th>Block Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($users as $index => $user)
                    <tr>
                        <td>{{ $index + $users->firstItem() }}</td>
                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->country }}</td>
                        <td>${{ number_format($user->deposit_wallet ?? 0, 2) }}</td>
                        <td>${{ number_format($user->profit_wallet ?? 0, 2) }}</td>
                        <td>${{ number_format($user->active_wallet ?? 0, 2) }}</td>
                        <td>{{ $user->referredBy->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $user->is_block ? 'bg-danger' : 'bg-success' }}">
                                {{ $user->is_block ? 'Blocked' : 'Unblocked' }}
                            </span>
                        </td>
                        <td>
                            <button type="button"
                                    class="btn btn-sm btn-primary updateStatusBtn"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    data-active-wallet="{{ $user->active_wallet }}"
                                    data-deposit-wallet="{{ $user->deposit_wallet }}"
                                    data-block="{{ $user->is_block }}"
                                    data-wallet="{{ $user->profit_wallet }}"
                                    data-toggle="modal"
                                    data-target="#actionModal">
                                <i class="fas fa-edit"></i> Manage
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No users found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $users->links('admin.layouts.partials.__pagination') }}
            </div>
        </div>
        @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6',
                    timer: 3000,
                    timerProgressBar: true
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#d33',
                    timer: 3000,
                    timerProgressBar: true
                });
            </script>
        @endif

    </div>

    @include('admin.modal.userblockmodal')
@endsection
