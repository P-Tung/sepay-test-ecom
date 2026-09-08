@csrf
<div class="mb-3">
    <label class="form-label" for="name">Tên sản phẩm</label>
    <input class="form-control" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label" for="category_id">Danh mục</label>
    <select class="form-select" id="category_id" name="category_id" required>
        <option value="">Chọn danh mục</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="price">Giá</label>
        <input class="form-control" id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $product->price ?? '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="quantity">Số lượng</label>
        <input class="form-control" id="quantity" name="quantity" type="number" min="0" value="{{ old('quantity', $product->quantity ?? 0) }}" required>
    </div>
</div>
<div class="mb-3">
    <label class="form-label" for="description">Mô tả</label>
    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $product->description ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label" for="image">Ảnh sản phẩm</label>
    <input class="form-control" id="image" name="image" type="file" accept="image/*">
    @if(!empty($product?->image))
        <img class="mt-2" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="100" height="100" style="object-fit: cover;">
    @endif
</div>
<button class="btn btn-success" type="submit">{{ $submitLabel }}</button>
<a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Quay lại</a>
