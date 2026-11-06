@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-bold text-success mb-1">Catalog</p>
            <h1 class="h2 fw-bold mb-1">Categories</h1>
            <p class="text-secondary mb-0">Organize the products in your shop.</p>
        </div>
        <button class="btn btn-success px-3 fw-semibold" type="button" data-bs-toggle="modal"
            data-bs-target="#createCategoryModal">
            <span class="me-1" aria-hidden="true">＋</span> Add category
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

    <section class="card border-0 shadow-sm rounded-4 overflow-hidden" aria-labelledby="category-list-heading">
        <div class="card-header bg-white border-0 px-3 px-lg-4 pt-4 pb-3 d-flex justify-content-between align-items-center">
            <div>
                <h2 id="category-list-heading" class="h5 fw-bold mb-1">All categories</h2>
                <p class="small text-secondary mb-0">{{ $categories->total() }}
                    {{ \Illuminate\Support\Str::plural('category', $categories->total()) }}</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 admin-table">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 ps-lg-4" scope="col">Category</th>
                        <th scope="col">Slug</th>
                        <th scope="col">Subcategories</th>
                        <th scope="col">Products</th>
                        <th class="text-end pe-3 pe-lg-4" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="ps-3 ps-lg-4">
                                <span class="d-block fw-semibold text-body">{{ $category->name }}</span>
                                <span class="small text-secondary">Added
                                    {{ $category->created_at->format('M j, Y') }}</span>
                            </td>
                            <td><code class="text-secondary">{{ $category->slug }}</code></td>
                            <td><span class="badge rounded-pill text-bg-light">{{ $category->subcategories_count }}</span>
                            </td>
                            <td><span class="badge rounded-pill text-bg-light">{{ $category->products_count }}</span></td>
                            <td class="text-end pe-3 pe-lg-4">
                                <div class="d-inline-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="modal"
                                        data-bs-target="#editCategory-{{ $category->id }}">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal"
                                        data-bs-target="#deleteCategory-{{ $category->id }}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center">
                                <p class="fw-bold mb-1">No categories yet</p>
                                <p class="text-secondary small mb-3">Create a category to start organizing your catalog.</p>
                                <button class="btn btn-success btn-sm fw-semibold" type="button" data-bs-toggle="modal"
                                    data-bs-target="#createCategoryModal">Add your first category</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="card-footer bg-white border-0 px-3 px-lg-4 py-3">
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </section>

    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel"
        aria-hidden="true" @if ($errors->any() && old('_category_modal') === 'create') data-open-on-load @endif>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <input type="hidden" name="_category_modal" value="create">
                    <div class="modal-header border-0 px-4 pt-4 pb-2">
                        <div>
                            <h2 class="modal-title fs-5 fw-bold" id="createCategoryModalLabel">Add a category</h2>
                            <p class="small text-secondary mb-0 mt-1">Give this group a clear name shoppers can understand.
                            </p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <label for="newCategoryName" class="form-label fw-semibold">Category name</label>
                        <input id="newCategoryName" name="name" type="text"
                            value="{{ old('_category_modal') === 'create' ? old('name') : '' }}"
                            class="form-control @error('name') is-invalid @enderror" maxlength="255" required autofocus>
                        <div class="form-text">A shop URL slug will be generated from this name.</div>
                        @if ($errors->any() && old('_category_modal') === 'create')
                            <div class="invalid-feedback d-block">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-semibold">Save category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($categories as $category)
        @php
            $isFailedEdit =
                $errors->any() && old('_category_modal') === 'edit' && (int) old('_category_id') === $category->id;
        @endphp

        <div class="modal fade" id="editCategory-{{ $category->id }}" tabindex="-1"
            aria-labelledby="editCategoryLabel-{{ $category->id }}" aria-hidden="true"
            @if ($isFailedEdit) data-open-on-load @endif>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_category_modal" value="edit">
                        <input type="hidden" name="_category_id" value="{{ $category->id }}">
                        <div class="modal-header border-0 px-4 pt-4 pb-2">
                            <div>
                                <h2 class="modal-title fs-5 fw-bold" id="editCategoryLabel-{{ $category->id }}">Edit
                                    category</h2>
                                <p class="small text-secondary mb-0 mt-1">Update the name shown in your shop.</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4 py-3">
                            <label for="categoryName-{{ $category->id }}" class="form-label fw-semibold">Category
                                name</label>
                            <input id="categoryName-{{ $category->id }}" name="name" type="text"
                                value="{{ $isFailedEdit ? old('name') : $category->name }}"
                                class="form-control @if ($isFailedEdit && $errors->has('name')) is-invalid @endif" maxlength="255"
                                required>
                            <div class="form-text">A shop URL slug will be updated from this name.</div>
                            @if ($isFailedEdit && $errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success fw-semibold">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteCategory-{{ $category->id }}" tabindex="-1"
            aria-labelledby="deleteCategoryLabel-{{ $category->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 rounded-4 shadow">
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header border-0 px-4 pt-4 pb-2">
                            <h2 class="modal-title fs-5 fw-bold" id="deleteCategoryLabel-{{ $category->id }}">Delete
                                category?</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4 py-2">
                            <p class="mb-0 text-secondary">Delete <strong
                                    class="text-body">{{ $category->name }}</strong>? This cannot be undone.</p>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger fw-semibold">Delete category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
