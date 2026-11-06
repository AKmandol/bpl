@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-bold text-success mb-1">Catalog</p>
            <h1 class="h2 fw-bold mb-1">Products</h1>
            <p class="text-secondary mb-0">Manage product details, prices, images, and categories.</p>
        </div>
        <button class="btn btn-success px-3 fw-semibold" type="button" data-bs-toggle="modal"
            data-bs-target="#createProductModal" @if (!$hasSubcategories) disabled @endif>
            <span class="me-1" aria-hidden="true">＋</span> Add product
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-warning" role="alert">{{ session('error') }}</div>
    @endif

    <section class="card border-0 shadow-sm rounded-4 overflow-hidden" aria-labelledby="product-list-heading">
        <div class="card-header bg-white border-0 px-3 px-lg-4 pt-4 pb-3">
            <h2 id="product-list-heading" class="h5 fw-bold mb-1">All products</h2>
            <p class="small text-secondary mb-0">{{ $products->total() }}
                {{ \Illuminate\Support\Str::plural('product', $products->total()) }}</p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 admin-table">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 ps-lg-4" scope="col">Product</th>
                        <th scope="col">Category</th>
                        <th scope="col">Price</th>
                        <th class="text-end pe-3 pe-lg-4" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="ps-3 ps-lg-4">
                                <div class="d-flex align-items-center gap-3">
                                    @if ($product->image_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}"
                                            alt="" class="rounded-3 object-fit-cover" width="52" height="52"
                                            loading="lazy">
                                    @else
                                        <span
                                            class="d-inline-grid rounded-3 bg-success-subtle text-success-emphasis fw-bold"
                                            style="width: 52px; height: 52px; place-items: center"
                                            aria-hidden="true">{{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}</span>
                                    @endif
                                    <span class="min-w-0">
                                        <span class="d-block fw-semibold text-body">{{ $product->name }}</span>
                                        <code class="small text-secondary">{{ $product->slug }}</code>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="d-block fw-semibold">{{ $product->category->name }}</span>
                                <span class="small text-secondary">{{ $product->subcategory->name }}</span>
                            </td>
                            <td>
                                <span
                                    class="d-block fw-semibold">৳{{ number_format((float) $product->new_price, 2) }}</span>
                                @if ($product->old_price !== null)
                                    <span
                                        class="small text-secondary text-decoration-line-through">৳{{ number_format((float) $product->old_price, 2) }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-3 pe-lg-4">
                                <div class="d-inline-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="modal"
                                        data-bs-target="#editProduct-{{ $product->id }}">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal"
                                        data-bs-target="#deleteProduct-{{ $product->id }}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-5 text-center">
                                <p class="fw-bold mb-1">No products yet</p>
                                <p class="text-secondary small mb-3">Add categories and subcategories, then create your
                                    first product.</p>
                                @if ($hasSubcategories)
                                    <button class="btn btn-success btn-sm fw-semibold" type="button" data-bs-toggle="modal"
                                        data-bs-target="#createProductModal">Add your first product</button>
                                @elseif ($categories->isNotEmpty())
                                    <a href="{{ route('admin.subcategories.index') }}"
                                        class="btn btn-outline-success btn-sm fw-semibold">Add a subcategory first</a>
                                @else
                                    <a href="{{ route('admin.categories.index') }}"
                                        class="btn btn-outline-success btn-sm fw-semibold">Manage categories first</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="card-footer bg-white border-0 px-3 px-lg-4 py-3">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </section>

    <div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel"
        aria-hidden="true" @if ($errors->any() && old('_product_modal') === 'create') data-open-on-load @endif>
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 shadow">
                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_product_modal" value="create">
                    <div class="modal-header border-0 px-4 px-lg-5 pt-4 pb-2">
                        <div>
                            <h2 class="modal-title fs-5 fw-bold" id="createProductModalLabel">Add a product</h2>
                            <p class="small text-secondary mb-0 mt-1">Fill in the product details and choose its category.
                            </p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 px-lg-5 py-3">
                        @if (!$hasSubcategories)
                            <div class="alert alert-warning">Add a category and subcategory before creating a product.</div>
                            @if ($categories->isEmpty())
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-success">Manage
                                    categories</a>
                            @else
                                <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-success">Manage
                                    subcategories</a>
                            @endif
                        @else
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="newProductCategory" class="form-label fw-semibold">Category</label>
                                    <select id="newProductCategory" name="category_id"
                                        class="form-select @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('category_id')) is-invalid @endif" required>
                                        <option value="">Choose a category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('_product_modal') === 'create' && (string) old('category_id') === (string) $category->id)>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('category_id'))
                                        <div class="invalid-feedback d-block">{{ $errors->first('category_id') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="newProductSubcategory" class="form-label fw-semibold">Subcategory</label>
                                    <select id="newProductSubcategory" name="subcategory_id"
                                        class="form-select @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('subcategory_id')) is-invalid @endif" required>
                                        <option value="">Choose a subcategory</option>
                                        @foreach ($categories as $category)
                                            @if ($category->subcategories->isNotEmpty())
                                                <optgroup label="{{ $category->name }}">
                                                    @foreach ($category->subcategories as $subcategory)
                                                        <option value="{{ $subcategory->id }}"
                                                            @selected(old('_product_modal') === 'create' && (string) old('subcategory_id') === (string) $subcategory->id)>{{ $subcategory->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        @endforeach
                                    </select>
                                    <div class="form-text">Options are grouped by their parent category.</div>
                                    @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('subcategory_id'))
                                        <div class="invalid-feedback d-block">Choose a subcategory under the selected
                                            category.</div>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <label for="newProductName" class="form-label fw-semibold">Product name</label>
                                    <input id="newProductName" name="name" type="text"
                                        value="{{ old('_product_modal') === 'create' ? old('name') : '' }}"
                                        class="form-control @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('name')) is-invalid @endif"
                                        maxlength="255" required>
                                    <div class="form-text">The product URL slug is generated from this name.</div>
                                    @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('name'))
                                        <div class="invalid-feedback d-block">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <label for="newProductDescription" class="form-label fw-semibold">Description</label>
                                    <textarea id="newProductDescription" name="description" rows="4" maxlength="5000"
                                        class="form-control @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('description')) is-invalid @endif" required>{{ old('_product_modal') === 'create' ? old('description') : '' }}</textarea>
                                    @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('description'))
                                        <div class="invalid-feedback d-block">{{ $errors->first('description') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <label for="newProductOldPrice" class="form-label fw-semibold">Old price <span
                                            class="text-secondary fw-normal">(optional)</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input id="newProductOldPrice" name="old_price" type="number" min="0"
                                            max="99999999.99" step="0.01"
                                            value="{{ old('_product_modal') === 'create' ? old('old_price') : '' }}"
                                            class="form-control @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('old_price')) is-invalid @endif">
                                    </div>
                                    @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('old_price'))
                                        <div class="invalid-feedback d-block">{{ $errors->first('old_price') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <label for="newProductNewPrice" class="form-label fw-semibold">New price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input id="newProductNewPrice" name="new_price" type="number" min="0"
                                            max="99999999.99" step="0.01"
                                            value="{{ old('_product_modal') === 'create' ? old('new_price') : '' }}"
                                            class="form-control @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('new_price')) is-invalid @endif"
                                            required>
                                    </div>
                                    @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('new_price'))
                                        <div class="invalid-feedback d-block">{{ $errors->first('new_price') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <label for="newProductImage" class="form-label fw-semibold">Product image <span
                                            class="text-secondary fw-normal">(optional)</span></label>
                                    <input id="newProductImage" name="image" type="file"
                                        accept=".jpg,.jpeg,.png,.webp"
                                        class="form-control @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('image')) is-invalid @endif">
                                    <div class="form-text">JPG, PNG, or WebP up to 2 MB.</div>
                                    @if ($errors->any() && old('_product_modal') === 'create' && $errors->has('image'))
                                        <div class="invalid-feedback d-block">{{ $errors->first('image') }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-0 px-4 px-lg-5 pb-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-semibold"
                            @if (!$hasSubcategories) disabled @endif>Save product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($products as $product)
        @php
            $isFailedEdit =
                $errors->any() && old('_product_modal') === 'edit' && (int) old('_product_id') === $product->id;
        @endphp

        <div class="modal fade" id="editProduct-{{ $product->id }}" tabindex="-1"
            aria-labelledby="editProductLabel-{{ $product->id }}" aria-hidden="true"
            @if ($isFailedEdit) data-open-on-load @endif>
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 rounded-4 shadow">
                    <form method="POST" action="{{ route('admin.products.update', $product) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_product_modal" value="edit">
                        <input type="hidden" name="_product_id" value="{{ $product->id }}">
                        <div class="modal-header border-0 px-4 px-lg-5 pt-4 pb-2">
                            <div>
                                <h2 class="modal-title fs-5 fw-bold" id="editProductLabel-{{ $product->id }}">Edit
                                    product</h2>
                                <p class="small text-secondary mb-0 mt-1">Update the product details shown in your shop.
                                </p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4 px-lg-5 py-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="productCategory-{{ $product->id }}"
                                        class="form-label fw-semibold">Category</label>
                                    <select id="productCategory-{{ $product->id }}" name="category_id"
                                        class="form-select @if ($isFailedEdit && $errors->has('category_id')) is-invalid @endif" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected(($isFailedEdit ? (string) old('category_id') : (string) $product->category_id) === (string) $category->id)>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($isFailedEdit && $errors->has('category_id'))
                                        <div class="invalid-feedback">{{ $errors->first('category_id') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="productSubcategory-{{ $product->id }}"
                                        class="form-label fw-semibold">Subcategory</label>
                                    <select id="productSubcategory-{{ $product->id }}" name="subcategory_id"
                                        class="form-select @if ($isFailedEdit && $errors->has('subcategory_id')) is-invalid @endif" required>
                                        @foreach ($categories as $category)
                                            @if ($category->subcategories->isNotEmpty())
                                                <optgroup label="{{ $category->name }}">
                                                    @foreach ($category->subcategories as $subcategory)
                                                        <option value="{{ $subcategory->id }}"
                                                            @selected(($isFailedEdit ? (string) old('subcategory_id') : (string) $product->subcategory_id) === (string) $subcategory->id)>{{ $subcategory->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        @endforeach
                                    </select>
                                    <div class="form-text">Options are grouped by their parent category.</div>
                                    @if ($isFailedEdit && $errors->has('subcategory_id'))
                                        <div class="invalid-feedback d-block">Choose a subcategory under the selected
                                            category.</div>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <label for="productName-{{ $product->id }}" class="form-label fw-semibold">Product
                                        name</label>
                                    <input id="productName-{{ $product->id }}" name="name" type="text"
                                        value="{{ $isFailedEdit ? old('name') : $product->name }}"
                                        class="form-control @if ($isFailedEdit && $errors->has('name')) is-invalid @endif"
                                        maxlength="255" required>
                                    <div class="form-text">The product URL slug is generated from this name.</div>
                                    @if ($isFailedEdit && $errors->has('name'))
                                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <label for="productDescription-{{ $product->id }}"
                                        class="form-label fw-semibold">Description</label>
                                    <textarea id="productDescription-{{ $product->id }}" name="description" rows="4" maxlength="5000"
                                        class="form-control @if ($isFailedEdit && $errors->has('description')) is-invalid @endif" required>{{ $isFailedEdit ? old('description') : $product->description }}</textarea>
                                    @if ($isFailedEdit && $errors->has('description'))
                                        <div class="invalid-feedback">{{ $errors->first('description') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <label for="productOldPrice-{{ $product->id }}" class="form-label fw-semibold">Old
                                        price <span class="text-secondary fw-normal">(optional)</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input id="productOldPrice-{{ $product->id }}" name="old_price" type="number"
                                            min="0" max="99999999.99" step="0.01"
                                            value="{{ $isFailedEdit ? old('old_price') : $product->old_price }}"
                                            class="form-control @if ($isFailedEdit && $errors->has('old_price')) is-invalid @endif">
                                    </div>
                                    @if ($isFailedEdit && $errors->has('old_price'))
                                        <div class="invalid-feedback d-block">{{ $errors->first('old_price') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <label for="productNewPrice-{{ $product->id }}" class="form-label fw-semibold">New
                                        price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input id="productNewPrice-{{ $product->id }}" name="new_price" type="number"
                                            min="0" max="99999999.99" step="0.01"
                                            value="{{ $isFailedEdit ? old('new_price') : $product->new_price }}"
                                            class="form-control @if ($isFailedEdit && $errors->has('new_price')) is-invalid @endif"
                                            required>
                                    </div>
                                    @if ($isFailedEdit && $errors->has('new_price'))
                                        <div class="invalid-feedback d-block">{{ $errors->first('new_price') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <label for="productImage-{{ $product->id }}" class="form-label fw-semibold">Replace
                                        image <span class="text-secondary fw-normal">(optional)</span></label>
                                    <input id="productImage-{{ $product->id }}" name="image" type="file"
                                        accept=".jpg,.jpeg,.png,.webp"
                                        class="form-control @if ($isFailedEdit && $errors->has('image')) is-invalid @endif">
                                    <div class="form-text">JPG, PNG, or WebP up to 2 MB.</div>
                                    @if ($product->image_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}"
                                            alt="Current image for {{ $product->name }}"
                                            class="mt-2 rounded-3 object-fit-cover" width="72" height="72">
                                    @endif
                                    @if ($isFailedEdit && $errors->has('image'))
                                        <div class="invalid-feedback d-block">{{ $errors->first('image') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 px-4 px-lg-5 pb-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success fw-semibold">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteProduct-{{ $product->id }}" tabindex="-1"
            aria-labelledby="deleteProductLabel-{{ $product->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 rounded-4 shadow">
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header border-0 px-4 pt-4 pb-2">
                            <h2 class="modal-title fs-5 fw-bold" id="deleteProductLabel-{{ $product->id }}">Delete
                                product?</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4 py-2">
                            <p class="mb-0 text-secondary">Delete <strong
                                    class="text-body">{{ $product->name }}</strong>? Its stored image will also be
                                removed.</p>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger fw-semibold">Delete product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
