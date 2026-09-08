@extends('layouts.app')

@section('title', 'Danh sách danh mục')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

    <h2>Danh sách danh mục (Admin)</h2>

    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        Thêm mới
    </a>

</div>

<table class="table table-bordered table-hover">

    <thead class="table-dark">

        <tr>
            <th style="width: 10%;">ID</th>
            <th>Tên danh mục</th>
            <th style="width: 20%; text-align: center;">Hành động</th>
        </tr>

    </thead>

    <tbody>

        @foreach($categories as $category)

        <tr>

            <td>{{ $category->id }}</td>

            <td>{{ $category->name }}</td>

            <td class="text-center">

                <a
                    href="{{ route('admin.categories.show', $category->id) }}"
                    class="btn btn-info btn-sm text-white"
                >
                    Xem
                </a>

                <a
                    href="{{ route('admin.categories.edit', $category->id) }}"
                    class="btn btn-warning btn-sm"
                >
                    Sửa
                </a>

                <form
                    action="{{ route('admin.categories.destroy', $category->id) }}"
                    method="POST"
                    style="display: inline;"
                >

                    @csrf

                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?')"
                    >
                        Xóa
                    </button>

                </form>

            </td>

        </tr>

        @endforeach

        @if($categories->isEmpty())

        <tr>

            <td colspan="3" class="text-center">
                Hiện chưa có danh mục nào.
            </td>

        </tr>

        @endif

    </tbody>

</table>

{{ $categories->links() }}

@endsection
