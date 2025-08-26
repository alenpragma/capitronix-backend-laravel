<div class="form-group">
    <label>Name</label>
    <input type="text" name="name" class="form-control"
           value="{{ old('name', $plan->name ?? '') }}" required>
</div>

<div class="form-group">
    <label>Price</label>
    <input type="number" name="price" class="form-control"
           value="{{ old('price', $plan->price ?? '') }}" required>
</div>

<div class="form-group">
    <label>Interest Rate (%)</label>
    <input type="number" name="interest_rate" step="0.01" class="form-control"
           value="{{ old('interest_rate', $plan->interest_rate ?? '') }}" required>
</div>

<div class="form-group">
    <label>Duration (days)</label>
    <input type="number" name="duration" class="form-control"
           value="{{ old('duration', $plan->duration ?? '') }}" required>
</div>

<div class="form-group">
    <label>Return Type</label>
    <select name="return_type" class="form-control" required>
        <option value="daily" {{ old('return_type', $plan->return_type ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
        <option value="weekly" {{ old('return_type', $plan->return_type ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
        <option value="monthly" {{ old('return_type', $plan->return_type ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
        <option value="yearly" {{ old('return_type', $plan->return_type ?? '') == 'yearly' ? 'selected' : '' }}>Yearly</option>
    </select>
</div>

<div class="form-group">
    <label>Stock</label>
    <input type="number" name="stock" class="form-control"
           value="{{ old('stock', $plan->stock ?? 0) }}" required>
</div>

<div class="form-group">
    <label>Status</label>
    <select name="active" class="form-control" required>
        <option value="1" {{ old('active', $plan->active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
        <option value="0" {{ old('active', $plan->active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
    </select>
</div>
