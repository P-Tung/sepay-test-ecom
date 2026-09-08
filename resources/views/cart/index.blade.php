@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Giỏ hàng</h1>
    <a href="{{ route('welcome') }}" class="btn btn-outline-secondary">Tiếp tục mua sắm</a>
</div>

@if(empty($cart))
    <div class="alert alert-info">Giỏ hàng của bạn đang trống.</div>
@else
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Ảnh</th>
                    <th>Sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Đơn giá</th>
                    <th style="width: 180px;">Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $id => $item)
                    <tr>
                        <td>
                            @if(!empty($item['image']))
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" width="70" height="70" style="object-fit: cover;">
                            @else
                                Không có ảnh
                            @endif
                        </td>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['category'] }}</td>
                        <td>{{ number_format($item['price'], 0, ',', '.') }} đ</td>
                        <td>
                            <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                @method('PATCH')
                                <input class="form-control" type="number" name="quantity" min="1" value="{{ $item['quantity'] }}" required>
                                <button class="btn btn-sm btn-primary" type="submit">Cập nhật</button>
                            </form>
                        </td>
                        <td class="fw-bold">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} đ</td>
                        <td>
                            <form action="{{ route('cart.destroy', $id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Tổng tiền:</th>
                    <th colspan="2" class="text-danger fs-5">{{ number_format($total, 0, ',', '.') }} đ</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card mt-4">
        <div class="card-body">
            <h4 class="card-title">Tiến hành đặt hàng</h4>
            <form action="{{ route('orders.store') }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-4">
                    <label class="form-label" for="payment_method">Phương thức thanh toán</label>
                    <select class="form-select" id="payment_method" name="payment_method" required>
                        <option value="COD">Thanh toán khi nhận hàng (COD)</option>
                        <option value="online">Thanh toán qua VietQR Techcombank</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-success" type="submit">Đặt hàng</button>
                </div>
            </form>
            <p class="text-muted small mb-0">Với thanh toán chuyển khoản, bạn sẽ được chuyển đến trang QR VietQR của Techcombank sau khi tạo đơn.</p>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
</script>
@endpush
