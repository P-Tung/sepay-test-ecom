@extends('layouts.app')

@section('title', 'Danh mục')

@section('content')
<h2 class="mb-3">Danh mục sản phẩm</h2>
<div class="list-group mb-3">
    @forelse($categories as $category)
        <div class="list-group-item">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <a class="fw-bold text-decoration-none" href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
                <span class="badge bg-secondary">{{ $category->products->count() }} sản phẩm</span>
            </div>
            <div class="d-flex gap-3 flex-wrap">
                @forelse($category->products->take(4) as $product)
                    <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="90" height="70" class="rounded border" style="object-fit: cover;">
                        @endif
                        <div class="small mt-1">{{ $product->name }}</div>
                    </a>
                @empty
                    <span class="text-muted">Chưa có sản phẩm</span>
                @endforelse
            </div>
        </div>
    @empty
        <div class="alert alert-info">Chưa có danh mục.</div>
    @endforelse
</div>
{{ $categories->links() }}
@endsection
