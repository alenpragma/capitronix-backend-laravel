@extends('admin.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Code Management</h4>
{{--            <a href="{{ route('codes.create') }}" class="btn btn-primary btn-sm float-end">+ Add Code</a>--}}
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Owner</th>
                    <th>Status</th>
                    <th>User</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($codes as $code)
                    <tr>
                        <td>{{ $code->id }}</td>
                        <td>{{ strtoupper($code->code) }}</td>
                        <td>{{ $code->owner->name ?? '-' }}</td>
                        <td><span class="badge bg-info">{{ $code->status }}</span></td>
                        <td>{{ $code->user->name ?? '-' }}</td>
                        <td>{{ $code->created_at->diffForHumans() }}</td>
{{--                        <td>--}}
{{--                            <a href="{{ route('codes.edit', $code->id) }}" class="btn btn-warning btn-sm">Edit</a>--}}
{{--                            <form action="{{ route('codes.destroy', $code->id) }}" method="POST" style="display:inline-block;">--}}
{{--                                @csrf @method('DELETE')--}}
{{--                                <button type="submit" class="btn btn-danger btn-sm"--}}
{{--                                        onclick="return confirm('Are you sure?')">Delete</button>--}}
{{--                            </form>--}}
{{--                        </td>--}}
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $codes->links('admin.layouts.partials.__pagination') }}
        </div>
    </div>
@endsection
