<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Admin') · Dokan Market</title>
        @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    </head>
    <body>
        <header class="navbar navbar-expand bg-white border-bottom sticky-top">
            <div class="container-fluid px-3 px-lg-4">
                <a href="{{ route('admin.categories.index') }}" class="navbar-brand d-flex align-items-center gap-3 mb-0">
                    <span class="admin-brand-mark">D</span>
                    <span class="lh-sm">
                        <span class="d-block fw-bold text-success-emphasis">Dokan Market</span>
                        <span class="d-block small text-secondary">STORE MANAGEMENT</span>
                    </span>
                </a>
                <a href="{{ route('shop.index') }}" class="btn btn-outline-success btn-sm fw-semibold">Back to shop <span aria-hidden="true">↗</span></a>
            </div>
        </header>

        <div class="container-fluid">
            <div class="row">
                <aside class="col-lg-2 col-md-3 border-end bg-white admin-sidebar p-3 p-lg-4">
                    <p class="small fw-bold text-uppercase text-secondary mb-2">Manage</p>
                    <nav class="nav nav-pills flex-column gap-1" aria-label="Admin navigation">
                        <a class="nav-link active" href="{{ route('admin.categories.index') }}" aria-current="page">Categories</a>
                        <span class="nav-link disabled" aria-disabled="true">Subcategories <small class="float-end">Soon</small></span>
                        <span class="nav-link disabled" aria-disabled="true">Products <small class="float-end">Soon</small></span>
                    </nav>
                </aside>

                <main class="col-lg-10 col-md-9 px-3 px-lg-5 py-4 py-lg-5">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
