@extends('layouts.app')

@section('title', 'Sản phẩm')

@section('content')
<h2 class="mb-4">Sản phẩm mới nhất</h2>
<div class="row g-4">
    @forelse($products as $product)
        <div class="col-md-4">
            <div class="card h-100">
                @if($product->image)<img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 220px; object-fit: cover;">@endif
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="text-muted">{{ $product->category->name }}</p>
                    <p class="fw-bold">{{ number_format($product->price, 0, ',', '.') }} đ</p>
                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary mt-auto">Xem chi tiết</a>
                </div>
            </div>
        </div>
    @empty
        <p>Chưa có sản phẩm.</p>
    @endforelse
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
