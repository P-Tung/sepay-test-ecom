@extends('layouts.app')
@section('title', 'Thêm danh mục mới')
@section('content')
<div class="container">
<h2>Thêm danh mục mới</h2>
<form action="{{ route('admin.categories.store') }}" method="POST">
@csrf
<div class="mb-3">
<label for="name" class="form-label">Tên danh mục</label>
<input type="text" class="form-control" id="name" name="name" required>
</div>
<button type="submit" class="btn btn-success">Lưu danh mục</button>
<a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
</form>
</div>
@endsection
