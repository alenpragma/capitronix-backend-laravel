@extends('admin.layouts.app')
@section('content')
<div class="p-5">
    <h4>Edit Plan</h4>

    @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.all-plan.update', $plan->id) }}">
        @csrf
        @method('PUT')
        @include('admin.pages.plan.form', ['plan' => $plan])
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection
