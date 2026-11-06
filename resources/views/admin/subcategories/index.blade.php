@extends('layouts.admin')

@section('title', 'Subcategories')

@section('content')
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-bold text-success mb-1">Catalog</p>
            <h1 class="h2 fw-bold mb-1">Subcategories</h1>
            <p class="text-secondary mb-0">Place each subcategory under a parent category.</p>
        </div>
        <button class="btn btn-success px-3 fw-semibold" type="button" data-bs-toggle="modal"
            data-bs-target="#createSubcategoryModal" @if ($categories->isEmpty()) disabled @endif>
            <span class="me-1" aria-hidden="true">＋</span> Add subcategory
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

    <section class="card border-0 shadow-sm rounded-4 overflow-hidden" aria-labelledby="subcategory-list-heading">
        <div class="card-header bg-white border-0 px-3 px-lg-4 pt-4 pb-3">
            <h2 id="subcategory-list-heading" class="h5 fw-bold mb-1">All subcategories</h2>
            <p class="small text-secondary mb-0">{{ $subcategories->total() }}
                {{ \Illuminate\Support\Str::plural('subcategory', $subcategories->total()) }}</p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 admin-table">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 ps-lg-4" scope="col">Subcategory</th>
                        <th scope="col">Parent category</th>
                        <th scope="col">Slug</th>
                        <th scope="col">Products</th>
                        <th class="text-end pe-3 pe-lg-4" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subcategories as $subcategory)
                        <tr>
                            <td class="ps-3 ps-lg-4">
                                <span class="d-block fw-semibold text-body">{{ $subcategory->name }}</span>
                                <span class="small text-secondary">Added
                                    {{ $subcategory->created_at->format('M j, Y') }}</span>
                            </td>
                            <td>{{ $subcategory->category->name }}</td>
                            <td><code class="text-secondary">{{ $subcategory->slug }}</code></td>
                            <td><span class="badge rounded-pill text-bg-light">{{ $subcategory->products_count }}</span>
                            </td>
                            <td class="text-end pe-3 pe-lg-4">
                                <div class="d-inline-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="modal"
                                        data-bs-target="#editSubcategory-{{ $subcategory->id }}">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal"
                                        data-bs-target="#deleteSubcategory-{{ $subcategory->id }}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center">
                                <p class="fw-bold mb-1">No subcategories yet</p>
                                <p class="text-secondary small mb-3">Create a subcategory after adding at least one
                                    category.</p>
                                @if ($categories->isNotEmpty())
                                    <button class="btn btn-success btn-sm fw-semibold" type="button" data-bs-toggle="modal"
                                        data-bs-target="#createSubcategoryModal">Add your first subcategory</button>
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

        @if ($subcategories->hasPages())
            <div class="card-footer bg-white border-0 px-3 px-lg-4 py-3">
                {{ $subcategories->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </section>

    <div class="modal fade" id="createSubcategoryModal" tabindex="-1" aria-labelledby="createSubcategoryModalLabel"
        aria-hidden="true" @if ($errors->any() && old('_subcategory_modal') === 'create') data-open-on-load @endif>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form method="POST" action="{{ route('admin.subcategories.store') }}">
                    @csrf
                    <input type="hidden" name="_subcategory_modal" value="create">
                    <div class="modal-header border-0 px-4 pt-4 pb-2">
                        <div>
                            <h2 class="modal-title fs-5 fw-bold" id="createSubcategoryModalLabel">Add a subcategory</h2>
                            <p class="small text-secondary mb-0 mt-1">Choose the category this subcategory belongs to.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <label for="newSubcategoryCategory" class="form-label fw-semibold">Parent category</label>
                        <select id="newSubcategoryCategory" name="category_id"
                            class="form-select @if ($errors->any() && old('_subcategory_modal') === 'create' && $errors->has('category_id')) is-invalid @endif" required>
                            <option value="">Choose a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('_subcategory_modal') === 'create' && (string) old('category_id') === (string) $category->id)>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->any() && old('_subcategory_modal') === 'create' && $errors->has('category_id'))
                            <div class="invalid-feedback d-block">{{ $errors->first('category_id') }}</div>
                        @endif

                        <label for="newSubcategoryName" class="form-label fw-semibold mt-3">Subcategory name</label>
                        <input id="newSubcategoryName" name="name" type="text"
                            value="{{ old('_subcategory_modal') === 'create' ? old('name') : '' }}"
                            class="form-control @if ($errors->any() && old('_subcategory_modal') === 'create' && $errors->has('name')) is-invalid @endif" maxlength="255"
                            required>
                        <div class="form-text">A shop URL slug will be generated from this name.</div>
                        @if ($errors->any() && old('_subcategory_modal') === 'create' && $errors->has('name'))
                            <div class="invalid-feedback d-block">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-semibold">Save subcategory</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($subcategories as $subcategory)
        @php
            $isFailedEdit =
                $errors->any() &&
                old('_subcategory_modal') === 'edit' &&
                (int) old('_subcategory_id') === $subcategory->id;
        @endphp

        <div class="modal fade" id="editSubcategory-{{ $subcategory->id }}" tabindex="-1"
            aria-labelledby="editSubcategoryLabel-{{ $subcategory->id }}" aria-hidden="true"
            @if ($isFailedEdit) data-open-on-load @endif>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <form method="POST" action="{{ route('admin.subcategories.update', $subcategory) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_subcategory_modal" value="edit">
                        <input type="hidden" name="_subcategory_id" value="{{ $subcategory->id }}">
                        <div class="modal-header border-0 px-4 pt-4 pb-2">
                            <div>
                                <h2 class="modal-title fs-5 fw-bold" id="editSubcategoryLabel-{{ $subcategory->id }}">
                                    Edit subcategory</h2>
                                <p class="small text-secondary mb-0 mt-1">Update its name or parent category.</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4 py-3">
                            <label for="subcategoryCategory-{{ $subcategory->id }}" class="form-label fw-semibold">Parent
                                category</label>
                            @if ($subcategory->products_count > 0)
                                <input type="hidden" name="category_id" value="{{ $subcategory->category_id }}">
                            @endif
                            <select id="subcategoryCategory-{{ $subcategory->id }}" name="category_id"
                                class="form-select @if ($isFailedEdit && $errors->has('category_id')) is-invalid @endif"
                                @if ($subcategory->products_count > 0) disabled @endif required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(($isFailedEdit ? (string) old('category_id') : (string) $subcategory->category_id) === (string) $category->id)>
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                            @if ($subcategory->products_count > 0)
                                <div class="form-text">The parent category cannot change while this subcategory has
                                    products.</div>
                            @endif
                            @if ($isFailedEdit && $errors->has('category_id'))
                                <div class="invalid-feedback d-block">{{ $errors->first('category_id') }}</div>
                            @endif

                            <label for="subcategoryName-{{ $subcategory->id }}"
                                class="form-label fw-semibold mt-3">Subcategory name</label>
                            <input id="subcategoryName-{{ $subcategory->id }}" name="name" type="text"
                                value="{{ $isFailedEdit ? old('name') : $subcategory->name }}"
                                class="form-control @if ($isFailedEdit && $errors->has('name')) is-invalid @endif" maxlength="255"
                                required>
                            <div class="form-text">A shop URL slug will be generated from this name.</div>
                            @if ($isFailedEdit && $errors->has('name'))
                                <div class="invalid-feedback d-block">{{ $errors->first('name') }}</div>
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

        <div class="modal fade" id="deleteSubcategory-{{ $subcategory->id }}" tabindex="-1"
            aria-labelledby="deleteSubcategoryLabel-{{ $subcategory->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 rounded-4 shadow">
                    <form method="POST" action="{{ route('admin.subcategories.destroy', $subcategory) }}">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header border-0 px-4 pt-4 pb-2">
                            <h2 class="modal-title fs-5 fw-bold" id="deleteSubcategoryLabel-{{ $subcategory->id }}">
                                Delete subcategory?</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4 py-2">
                            <p class="mb-0 text-secondary">Delete <strong
                                    class="text-body">{{ $subcategory->name }}</strong>? This cannot be undone.</p>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger fw-semibold">Delete subcategory</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
