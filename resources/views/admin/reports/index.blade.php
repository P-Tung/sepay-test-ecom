@extends('layouts.app')

@section('title', 'Báo cáo thống kê')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Báo cáo thống kê</h1>
        <p class="text-muted mb-0">Doanh thu được tính từ các đơn hàng đã thanh toán.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card bg-primary text-white border-0 shadow-sm"><div class="card-body"><h2 class="h6">Tổng số đơn hàng</h2><div class="fs-3 fw-bold">{{ $totalOrders }}</div></div></div></div>
    <div class="col-md-4"><div class="card bg-success text-white border-0 shadow-sm"><div class="card-body"><h2 class="h6">Tổng khách hàng</h2><div class="fs-3 fw-bold">{{ $totalCustomers }}</div></div></div></div>
    <div class="col-md-4"><div class="card bg-dark text-white border-0 shadow-sm"><div class="card-body"><h2 class="h6">Doanh thu đã thanh toán</h2><div class="fs-3 fw-bold">{{ number_format($paidRevenue, 0, ',', '.') }} đ</div></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-header bg-dark text-white">Doanh thu theo danh mục</div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Danh mục</th><th class="text-end">Doanh thu</th></tr></thead><tbody>
        @forelse($categoryRevenue as $revenue)<tr><td>{{ $revenue->category_name }}</td><td class="text-end fw-bold">{{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td></tr>@empty<tr><td colspan="2" class="text-center text-muted">Chưa có dữ liệu.</td></tr>@endforelse
        </tbody></table></div></div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-header bg-secondary text-white">Doanh thu theo ngày</div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Ngày</th><th class="text-end">Doanh thu</th></tr></thead><tbody>
        @forelse($revenueByDate as $revenue)<tr><td>{{ $revenue->date }}</td><td class="text-end fw-bold">{{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td></tr>@empty<tr><td colspan="2" class="text-center text-muted">Chưa có dữ liệu.</td></tr>@endforelse
        </tbody></table></div></div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm"><div class="card-header">Doanh thu theo tháng</div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Thời gian</th><th class="text-end">Doanh thu</th></tr></thead><tbody>
        @forelse($revenueByMonth as $revenue)<tr><td>Tháng {{ $revenue->month }}/{{ $revenue->year }}</td><td class="text-end fw-bold">{{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td></tr>@empty<tr><td colspan="2" class="text-center text-muted">Chưa có dữ liệu.</td></tr>@endforelse
        </tbody></table></div></div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm"><div class="card-header">Doanh thu theo năm</div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Năm</th><th class="text-end">Doanh thu</th></tr></thead><tbody>
        @forelse($revenueByYear as $revenue)<tr><td>{{ $revenue->year }}</td><td class="text-end fw-bold">{{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td></tr>@empty<tr><td colspan="2" class="text-center text-muted">Chưa có dữ liệu.</td></tr>@endforelse
        </tbody></table></div></div>
    </div>
</div>
@endsection
