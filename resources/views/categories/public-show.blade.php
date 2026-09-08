@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
	<h2>Sản phẩm: {{ $category->name }}</h2>
	<a href="{{ route('categories.index') }}" class="btn btn-secondary">Quay lại danh mục</a>
</div>

<div class="row g-4">
	@forelse($category->products as $product)
		<div class="col-md-4">
			<div class="card h-100">
				@if($product->image)
					<img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="card-img-top" style="height: 220px; object-fit: cover;">
				@endif
				<div class="card-body d-flex flex-column">
					<h5 class="card-title">{{ $product->name }}</h5>
					<p class="fw-bold">{{ number_format($product->price, 0, ',', '.') }} đ</p>
					<a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary mt-auto">Xem chi tiết</a>
				</div>
			</div>
		</div>
	@empty
		<div class="col-12"><div class="alert alert-info">Danh mục này chưa có sản phẩm.</div></div>
	@endforelse
</div>
@endsection
