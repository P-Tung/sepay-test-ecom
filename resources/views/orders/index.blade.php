@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Đơn hàng của tôi</h1>
    <a class="btn btn-outline-secondary" href="{{ route('welcome') }}">Tiếp tục mua sắm</a>
</div>

@forelse($orders as $order)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <strong>Đơn hàng #{{ $order->id }}</strong>
            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="card-body">
            <p class="mb-2"><strong>Thanh toán:</strong> {{ $order->payment_method === 'COD' ? 'COD' : 'Trực tuyến' }} · <strong>Trạng thái:</strong> {{ $order->status }}</p>
            <ul class="mb-3">
                @foreach($order->items as $item)
                    <li>{{ $item->product?->name ?? 'Sản phẩm đã xóa' }} x {{ $item->quantity }} - {{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</li>
                @endforeach
            </ul>
            <strong class="text-danger">Tổng tiền: {{ number_format($order->total, 0, ',', '.') }} đ</strong>
        </div>
    </div>
@empty
    <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
@endforelse

{{ $orders->links() }}
@endsection
