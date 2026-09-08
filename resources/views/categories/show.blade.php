@extends('layouts.app')
@section('title', 'Chi tiết danh mục')
@section('content')
<div class="container">
<h2>Chi tiết danh mục</h2>
<div class="card mb-3">
<div class="card-body">
<h5 class="card-title">ID: {{ $category->id }}</h5>
<p class="card-text"><strong>Tên danh mục:</strong> {{ $category->name }}</p>
</div>
</div>
<a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
<a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning text-white">Sửa</a>
</div>
@endsection