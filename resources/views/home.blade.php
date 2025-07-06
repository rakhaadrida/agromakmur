@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between ml-4 mt-4">
        <h1 class="text-bold text-black-50">Hello, {{ $user->name }}</h1>
    </div>
</div>
@endsection
