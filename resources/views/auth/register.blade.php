@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-3">Đăng ký tài khoản</h2>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3"><label class="form-label" for="name">Họ tên</label><input class="form-control" id="name" name="name" value="{{ old('name') }}" required></div>
            <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" required></div>
            <div class="mb-3"><label class="form-label" for="password">Mật khẩu</label><input class="form-control" id="password" name="password" type="password" required></div>
            <div class="mb-3"><label class="form-label" for="password_confirmation">Xác nhận mật khẩu</label><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required></div>
            <button class="btn btn-primary">Đăng ký</button>
            <a class="btn btn-link" href="{{ route('login') }}">Đã có tài khoản?</a>
        </form>
    </div>
</div>
@endsection
