@extends('layouts.app')

@section('title', 'Sửa sản phẩm')

@section('content')
<h2 class="mb-3">Sửa sản phẩm</h2>
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @method('PUT')
    @include('products._form', ['submitLabel' => 'Cập nhật sản phẩm'])
</form>
@endsection
