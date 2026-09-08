@extends('layouts.app')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Quản lý đơn hàng</h1>
        <p class="text-muted mb-0">Theo dõi và cập nhật trạng thái đơn hàng của khách.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="fw-bold">#{{ $order->id }}</td>
                        <td>{{ $order->user->name ?? 'Không xác định' }}<br><small class="text-muted">{{ $order->user->email ?? '' }}</small></td>
                        <td class="text-danger fw-bold">{{ number_format($order->total, 0, ',', '.') }} đ</td>
                        <td>{{ $order->payment_method === 'COD' ? 'COD' : 'Chuyển khoản' }}</td>
                        <td>
                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm" aria-label="Trạng thái đơn hàng #{{ $order->id }}">
                                    <option value="processing" @selected($order->status === 'processing')>Đang xử lý</option>
                                    <option value="paid" @selected($order->status === 'paid')>Đã thanh toán</option>
                                    <option value="cancelled" @selected($order->status === 'cancelled')>Đã hủy</option>
                                </select>
                                <button class="btn btn-sm btn-primary" type="submit">Lưu</button>
                            </form>
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">Chưa có đơn hàng.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
