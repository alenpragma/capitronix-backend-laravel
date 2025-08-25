@extends('admin.layouts.app')
@section('content')
<div class="p-5">
    <h4>Create New Plan</h4>
    @if(session()->has('error'))
        <div class="alert alert-success">
            {{ session('error') }}
        </div>
    @endif
    <form method="POST" action="{{ route('add-plan') }}">
        @csrf
        @include('admin.pages.plan.form')
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
