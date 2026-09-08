@extends('layouts.app')

@section('title', 'Chi tiết sản phẩm')

@section('content')
<h2 class="mb-3">Chi tiết sản phẩm</h2>
<div class="card" style="max-width: 700px;">
    <div class="card-body">
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="mb-3" width="180" height="180" style="object-fit: cover;">
        @endif
        <h4>{{ $product->name }}</h4>
        <p><strong>Danh mục:</strong> {{ $product->category->name }}</p>
        <p><strong>Giá:</strong> {{ number_format($product->price, 0, ',', '.') }} đ</p>
        <p><strong>Số lượng:</strong> {{ $product->quantity }}</p>
        <p><strong>Mô tả:</strong> {{ $product->description ?: 'Không có mô tả.' }}</p>
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">Sửa</a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>
</div>
@endsection
