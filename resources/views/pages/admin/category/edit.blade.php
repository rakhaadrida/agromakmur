@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Kategori - {{ $category->name }}</h1>
        </div>
        @if($errors->any())
            <div class="alert alert-danger alert-input-section">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="card show">
                        <div class="card-body">
                            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group row">
                                    <label for="name" class="col-2 col-form-label text-bold text-right">Nama</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-6">
                                        <input type="text" class="form-control col-form-label-sm" name="name" id="name" value="{{ $category->name }}" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="reminderLimit" class="col-2 col-form-label text-bold text-right">Batas Pengingat Stok</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="number" min="0" class="form-control col-form-label-sm" name="reminder_limit" id="reminderLimit" value="{{ $category->reminder_limit }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja" required>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row justify-content-center">
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-success btn-block text-bold">Simpan</button>
                                    </div>
                                    <div class="col-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-block text-bold">Batal</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')

@endpush
