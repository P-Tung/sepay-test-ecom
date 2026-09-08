@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="p-4 bg-light border rounded">
    <h1>Admin Dashboard</h1>
    <p class="text-muted">Chào mừng {{ auth()->user()->name }} đến khu vực quản trị.</p>
    <div class="d-flex gap-2">
        <a class="btn btn-primary" href="{{ route('admin.products.index') }}">Quản lý sản phẩm</a>
        <a class="btn btn-secondary" href="{{ route('admin.categories.index') }}">Quản lý danh mục</a>
        <a class="btn btn-success" href="{{ route('admin.orders.index') }}">Quản lý đơn hàng</a>
        <a class="btn btn-info text-white" href="{{ route('admin.reports.index') }}">Xem báo cáo</a>
    </div>
</div>
@endsection
