@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <h2 class="mb-3">Đăng nhập</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" required></div>
            <div class="mb-3"><label class="form-label" for="password">Mật khẩu</label><input class="form-control" id="password" name="password" type="password" required></div>
            <div class="form-check mb-3"><input class="form-check-input" id="remember" name="remember" type="checkbox"><label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label></div>
            <button class="btn btn-primary">Đăng nhập</button>
            <a class="btn btn-link" href="{{ route('register') }}">Tạo tài khoản</a>
        </form>
    </div>
</div>
@endsection
