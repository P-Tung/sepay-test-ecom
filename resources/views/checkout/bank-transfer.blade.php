@extends('layouts.app')

@section('title', 'Thanh toán SePay Sandbox')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <span class="badge text-bg-warning mb-2">Sandbox SePay</span>
                    <h1 class="h2">Thanh toán đơn hàng #{{ $orderId }}</h1>
                    <p class="text-muted mb-0">Môi trường thử nghiệm, không trừ tiền thật.</p>
                </div>

                <dl class="row mb-4">
                    <dt class="col-6">Mã hóa đơn</dt>
                    <dd class="col-6">CHODCU-{{ $orderId }}</dd>
                    <dt class="col-6">Số tiền</dt>
                    <dd class="col-6 fw-bold text-danger">{{ number_format($totalAmount, 0, ',', '.') }} đ</dd>
                </dl>

                @if ($fields['merchant'] === '' || config('services.sepay.secret_key') === '')
                    <div class="alert alert-warning">Chưa cấu hình SEPAY_MERCHANT_ID và SEPAY_SECRET_KEY trong file .env.</div>
                @else
                    <form method="POST" action="{{ $checkoutUrl }}">
                        @foreach ($fields as $name => $value)
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endforeach
                        <button type="submit" class="btn btn-primary w-100">Thanh toán thử với SePay</button>
                    </form>
                @endif

                <a href="{{ route('orders.index') }}" class="btn btn-link w-100 mt-2">Xem lịch sử đơn hàng</a>
            </div>
        </div>
    </div>
</div>
@endsection
