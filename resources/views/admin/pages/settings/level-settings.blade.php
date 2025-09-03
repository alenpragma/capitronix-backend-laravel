@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Level Commissions</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex">
        <div class="col-md-8">
        <form action="{{ route('admin.level_commissions.update') }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table table-striped table-hover table-head-bg-primary mt-3">
                    <thead>
                        <tr>
                            <th hidden>Level</th>
                            <th>Level Name</th>
                            <th>Min Invest ($)</th>
                            <th>Direct Referral</th>
                            <th>Commission (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($levels as $index => $level)
                        <tr>
                            <td hidden>
                                {{ $level->level }}
                                <input type="hidden" name="level_id[]" value="{{ $level->id }}">
                            </td>
                            <td>
                                <input type="text" name="level_name[]" value="{{ $level->level_name }}" class="form-control" required>
                            </td>
                            <td>
                                <input type="number" name="min_invest[]" value="{{ $level->min_invest }}" class="form-control" step="0.01" required>
                            </td>
                            <td>
                                <input type="number" name="direct_referral[]" value="{{ $level->direct_referral }}" class="form-control" required>
                            </td>
                            <td>
                                <input type="number" name="commission[]" value="{{ $level->commission }}" class="form-control" step="0.01" max="100" required>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 mb-4">
                <button type="submit" class="btn btn-success">Update Levels</button>
            </div>
        </form>
        </div>
    </div>

</div>
@endsection
