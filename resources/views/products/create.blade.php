@extends('layouts.app')

@section('title', 'Thêm sản phẩm')

@section('content')
<h2 class="mb-3">Thêm sản phẩm</h2>
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @include('products._form', ['submitLabel' => 'Lưu sản phẩm'])
</form>
@endsection
