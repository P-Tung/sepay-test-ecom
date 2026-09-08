@extends('layouts.app')

@section('title', 'Quản lý sản phẩm')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Danh sách sản phẩm</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Thêm sản phẩm</a>
</div>

<form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 mb-4">
    <div class="col-md-4">
        <label class="form-label" for="search">Tìm kiếm</label>
        <input class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Tên hoặc mô tả">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="category_id">Danh mục</label>
        <select class="form-select" id="category_id" name="category_id">
            <option value="">Tất cả danh mục</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="sort">Sắp xếp</label>
        <select class="form-select" id="sort" name="sort">
            <option value="newest" @selected(request('sort', 'newest') === 'newest')>Mới nhất</option>
            <option value="oldest" @selected(request('sort') === 'oldest')>Cũ nhất</option>
            <option value="name_asc" @selected(request('sort') === 'name_asc')>Tên A-Z</option>
            <option value="name_desc" @selected(request('sort') === 'name_desc')>Tên Z-A</option>
            <option value="price_asc" @selected(request('sort') === 'price_asc')>Giá tăng dần</option>
            <option value="price_desc" @selected(request('sort') === 'price_desc')>Giá giảm dần</option>
        </select>
    </div>
    <div class="col-md-2 d-flex align-items-end gap-2">
        <button class="btn btn-dark" type="submit">Áp dụng</button>
        <a class="btn btn-outline-secondary" href="{{ route('admin.products.index') }}">Xóa</a>
    </div>
</form>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
    <thead class="table-dark">
        <tr><th>Ảnh</th><th>Tên sản phẩm</th><th>Danh mục</th><th>Giá</th><th>Kho</th><th>Hành động</th></tr>
    </thead>
    <tbody>
    @forelse($products as $product)
        <tr>
            <td>
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="70" height="70" style="object-fit: cover;">
                @else
                    Chưa có ảnh
                @endif
            </td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->category->name }}</td>
            <td>{{ number_format($product->price, 0, ',', '.') }} đ</td>
            <td>{{ $product->quantity }}</td>
            <td class="text-nowrap">
                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-info btn-sm text-white">Xem</a>
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-sm">Sửa</a>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">Xóa</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center">Chưa có sản phẩm phù hợp.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $products->links() }}
@endsection
