@extends('admin.layouts.app')

@section('content')
<div class="container w-50 bg-warning p-3 rounded shadow mt-4">
    <h2 class="mb-3">Transfer Settings</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.transfer.settings.update') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-bold">Minimum Transfer Amount ($)</label>
            <input type="number" step="0.01" name="min_transfer"
                value="{{ old('min_transfer', $setting->min_transfer ?? '') }}"
                class="form-control" placeholder="Enter minimum transfer" required>
            @error('min_transfer')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Maximum Transfer Amount ($)</label>
            <input type="number" step="0.01" name="max_transfer"
                value="{{ old('max_transfer', $setting->max_transfer ?? '') }}"
                class="form-control" placeholder="Enter maximum transfer" required>
            @error('max_transfer')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- <div class="mb-3">
            <label class="form-label fw-bold">Transfer Charge (%)</label>
            <input type="number" step="0.01" name="charge"
                value="{{ old('charge', $setting->charge ?? '') }}"
                class="form-control" placeholder="Enter transfer charge" required>
            @error('charge')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div> --}}

        <div class="mb-3">
            <label class="form-label fw-bold">Status</label>
            <select name="status" class="form-control" required>
                <option value="1" {{ isset($setting) && $setting->status == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ isset($setting) && $setting->status == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Settings</button>
    </form>
</div>
@endsection
