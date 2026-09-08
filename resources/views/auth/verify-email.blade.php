@extends('layouts.app')

@section('title', 'Xác thực email')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card text-center">
            <div class="card-body p-4">
                <h2 class="mb-3">Xác thực địa chỉ email</h2>
                <p class="text-muted">Vui lòng kiểm tra hộp thư của bạn và bấm vào liên kết xác thực để tiếp tục.</p>
                @if(session('status') === 'verification-link-sent')
                    <div class="alert alert-success">Một liên kết xác thực mới đã được gửi đến email của bạn.</div>
                @endif
                <form method="POST" action="{{ route('verification.send') }}" class="mb-2">
                    @csrf
                    <button class="btn btn-primary" type="submit">Gửi lại email xác thực</button>
                </form>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-secondary" type="submit">Đăng xuất</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
