@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="card" style="max-width: 720px;">
    @if($product->image)<img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="max-height: 360px; object-fit: contain;">@endif
    <div class="card-body">
        <h2>{{ $product->name }}</h2>
        <p class="text-muted">{{ $product->category->name }}</p>
        <p>{{ $product->description ?: 'Không có mô tả.' }}</p>
        <p class="fw-bold">{{ number_format($product->price, 0, ',', '.') }} đ</p>
        <p>Còn lại: {{ $product->quantity }}</p>
        @if($product->quantity > 0)
            <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-2">
                @csrf
                <button class="btn btn-primary" type="submit">Thêm vào giỏ hàng</button>
            </form>
        @else
            <p class="text-danger">Sản phẩm đã hết hàng.</p>
        @endif
        <a href="{{ route('welcome') }}" class="btn btn-secondary">Quay lại</a>
    </div>
</div>
@endsection
