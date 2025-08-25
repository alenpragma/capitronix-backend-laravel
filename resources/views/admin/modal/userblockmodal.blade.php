<div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('users.update')}}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="actionModalLabel">Manage User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="modal_user_id" name="user_id">
                    <input type="hidden" id="modal_block_status" name="is_block">
                    <input type="hidden" id="modal_wallet_status" name="wallet">

                    <div class="mb-3">
                        <label for="modal_user_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="modal_user_name" name="name" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="modal_user_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="modal_user_email" name="email" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="modal_user_active_wallet" class="form-label">Active Wallet</label>
                        <input type="text" class="form-control" id="modal_user_active_wallet" name="active_wallet" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="modal_user_deposit_wallet" class="form-label">Deposit Wallet</label>
                        <input type="text" class="form-control" id="modal_user_deposit_wallet" name="deposit_wallet" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Block Status</label>
                        <select class="form-select" name="block_action" id="modal_block_action">
                            <option value="0">Unblock</option>
                            <option value="1">Block</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
