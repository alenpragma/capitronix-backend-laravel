<div class="modal fade" id="walletModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.update.wallet') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Wallet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select name="wallet_type" class="form-control mb-2" required>
                        <option value="deposit_wallet">Deposit Wallet</option>
                        <option value="active_wallet">Active Wallet</option>
                        <option value="profit_wallet">Profit Wallet</option>
                    </select>
                    <input type="number" step="0.01" name="amount" class="form-control mb-2" placeholder="Amount" required>
                    <select name="action" class="form-control" required>
                        <option value="add">Add</option>
                        <option value="reduce">Reduce</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
