@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Sản phẩm mới nhất</h1>
    @guest
        <a class="btn btn-primary" href="{{ route('login') }}">Đăng nhập để xem sản phẩm</a>
    @endguest
</div>
<div class="row g-4">
    @forelse($products as $product)
        <div class="col-md-4">
            <div class="card h-100">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 220px; object-fit: cover;">
                @endif
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="text-muted">{{ $product->category->name }}</p>
                    <p class="fw-bold">{{ number_format($product->price, 0, ',', '.') }} đ</p>
                    @auth
                        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary mt-auto">Xem chi tiết</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary mt-auto">Đăng nhập để xem</a>
                    @endauth
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-info">Chưa có sản phẩm.</div></div>
    @endforelse
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
