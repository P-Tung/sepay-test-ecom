<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Quản lý ứng dụng')</title>
@if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
@vite(['resources/css/app.css', 'resources/js/app.js'])
@endif
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<!-- Bắt đầu Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
<div class="container">
<a class="navbar-brand" href="{{ url('/') }}">Chợ Đồ Cũ</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav me-auto">
<li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}">Trang chủ</a></li>
@auth
<li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Sản phẩm</a></li>
<li class="nav-item"><a class="nav-link" href="{{ route('categories.index') }}">Danh mục</a></li>
<li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}">Giỏ hàng ({{ collect(session('cart', []))->sum('quantity') }})</a></li>
<li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}">Đơn hàng</a></li>
@if(auth()->user()->isAdmin())
<li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
@endif
@endauth
</ul>
<ul class="navbar-nav ms-auto">
@guest
<li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Đăng nhập</a></li>
<li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Đăng ký</a></li>
@else
<li class="nav-item"><span class="nav-link">Xin chào, {{ auth()->user()->name }}</span></li>
<li class="nav-item">
	<form method="POST" action="{{ route('logout') }}">
		@csrf
		<button class="nav-link border-0 bg-transparent" type="submit">Đăng xuất</button>
	</form>
</li>
@endguest
</ul>
</div>
</div>
</nav>
<!-- Kết thúc Navbar -->
<!-- Phần nội dung chính của trang web -->
<main class="container flex-grow-1">
<!-- Hiển thị thông báo thành công từ Controller (ví dụ: Category created successfully) -->
@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">
	<ul class="mb-0">
		@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif
<!-- Các file giao diện con (như index, create, edit...) sẽ được đổ nội dung vào đây -->
@yield('content')
</main>

<footer class="site-footer bg-dark text-light mt-5">
<div class="container py-5">
<div class="row g-4">
<div class="col-lg-4">
<a class="footer-brand text-white text-decoration-none" href="{{ route('welcome') }}">Chợ Đồ Cũ</a>
<p class="footer-description text-light">Mua sắm đơn giản hơn với những sản phẩm chất lượng và dịch vụ tận tâm.</p>
<div class="footer-contact">
<a class="text-light text-decoration-none" href="tel:19002026">Hotline: 1900 2026</a>
<a class="text-light text-decoration-none" href="mailto:support@ecommerce2026.vn">support@ecommerce2026.vn</a>
</div>
</div>
<div class="col-6 col-lg-2">
<h2 class="footer-heading h5">Khám phá</h2>
<ul class="footer-links">
<li><a class="text-light text-decoration-none" href="{{ route('welcome') }}">Trang chủ</a></li>
<li><a class="text-light text-decoration-none" href="{{ route('products.index') }}">Sản phẩm</a></li>
<li><a class="text-light text-decoration-none" href="{{ route('categories.index') }}">Danh mục</a></li>
<li><a class="text-light text-decoration-none" href="{{ route('cart.index') }}">Giỏ hàng</a></li>
</ul>
</div>
<div class="col-6 col-lg-2">
<h2 class="footer-heading h5">Hỗ trợ</h2>
<ul class="footer-links">
<li><a class="text-light text-decoration-none" href="{{ route('orders.index') }}">Đơn hàng của tôi</a></li>
<li><a class="text-light text-decoration-none" href="{{ route('login') }}">Tài khoản</a></li>
<li><a class="text-light text-decoration-none" href="{{ route('register') }}">Đăng ký</a></li>
<li><a class="text-light text-decoration-none" href="mailto:support@ecommerce2026.vn">Liên hệ</a></li>
</ul>
</div>
<div class="col-lg-4">
<h2 class="footer-heading h5">Thanh toán an toàn</h2>
<p class="footer-description text-light">Hỗ trợ thanh toán khi nhận hàng và chuyển khoản ngân hàng.</p>
<div class="payment-badges" aria-label="Phương thức thanh toán">
<span>COD</span>
<span>VNPAY</span>
<span>QR BANKING</span>
</div>
</div>
</div>
<div class="footer-bottom">
<span>&copy; {{ date('Y') }} Chợ Đồ Cũ. Bảo lưu mọi quyền.</span>
<span>Chính sách bảo mật · Điều khoản sử dụng</span>
</div>
</div>
</footer>
<!-- Script của Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
