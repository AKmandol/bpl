@extends('layouts.shop')

@section('title', $activeSubcategory?->name ?? ($activeCategory?->name ?? 'Shop'))

@section('content')
    <section class="bg-emerald-950 text-white">
        <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-16">
            <p class="mb-3 text-xs font-bold uppercase tracking-[0.22em] text-lime-300">A little something for every day</p>
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <h1 class="text-4xl font-black tracking-tight sm:text-5xl">
                        {{ $activeSubcategory?->name ?? ($activeCategory?->name ?? 'Find your next favourite') }}
                    </h1>
                    <p class="mt-4 max-w-xl text-base leading-7 text-emerald-100 sm:text-lg">
                        @if ($activeSubcategory)
                            Explore our {{ strtolower($activeSubcategory->name) }} picks, chosen for everyday use.
                        @elseif ($activeCategory)
                            Explore {{ strtolower($activeCategory->name) }} picks from our shop.
                        @else
                            Useful things, considered prices, and a simple way to find what you need.
                        @endif
                    </p>
                </div>
                <a href="#products"
                    class="inline-flex w-fit items-center gap-2 rounded-full bg-lime-300 px-5 py-3 text-sm font-extrabold text-emerald-950 transition hover:bg-lime-200">
                    Browse products <span aria-hidden="true">↓</span>
                </a>
            </div>
        </div>
    </section>

    <div class="mx-auto grid max-w-7xl gap-8 px-5 py-9 sm:px-8 lg:grid-cols-[230px_minmax(0,1fr)] lg:gap-10 lg:py-12">
        <aside aria-label="Browse product categories"
            class="self-start rounded-3xl border border-stone-200 bg-white p-5 shadow-sm lg:sticky lg:top-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-base font-extrabold text-stone-900">Shop by category</h2>
                <span class="text-xs font-semibold text-stone-400">{{ $categories->count() }}</span>
            </div>

            <a href="{{ route('shop.index') }}" @class([
                'flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-bold transition',
                'bg-emerald-50 text-emerald-950' => !$activeCategory,
                'text-stone-600 hover:bg-stone-50 hover:text-emerald-950' => $activeCategory,
            ])>
                <span>All products</span>
            </a>

            <ul class="mt-3 space-y-2">
                @forelse ($categories as $category)
                    <li>
                        <a href="{{ route('shop.categories.show', ['category' => $category->slug]) }}"
                            @class([
                                'flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-bold transition',
                                'bg-emerald-50 text-emerald-950' => $activeCategory?->is($category),
                                'text-stone-700 hover:bg-stone-50 hover:text-emerald-950' => !$activeCategory?->is(
                                    $category),
                            ])>
                            <span>{{ $category->name }}</span>
                            <span class="text-xs font-semibold text-stone-400">{{ $category->products_count }}</span>
                        </a>

                        @if ($category->subcategories->isNotEmpty())
                            <ul class="ml-4 mt-1 space-y-1 border-l border-stone-200 pl-3">
                                @foreach ($category->subcategories as $subcategory)
                                    <li>
                                        <a href="{{ route('shop.subcategories.show', ['subcategory' => $subcategory->slug]) }}"
                                            @class([
                                                'flex items-center justify-between rounded-lg px-2.5 py-2 text-sm transition',
                                                'font-bold text-emerald-900' => $activeSubcategory?->is($subcategory),
                                                'text-stone-500 hover:text-emerald-900' => !$activeSubcategory?->is(
                                                    $subcategory),
                                            ])>
                                            <span>{{ $subcategory->name }}</span>
                                            <span class="text-xs text-stone-400">{{ $subcategory->products_count }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @empty
                    <li class="rounded-xl bg-stone-50 px-3 py-3 text-sm leading-6 text-stone-500">
                        Categories will appear here once they are added.
                    </li>
                @endforelse
            </ul>
        </aside>

        <section id="products" aria-labelledby="product-list-heading" class="min-w-0">
            <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-stone-500">
                        {{ $activeSubcategory?->name ?? ($activeCategory?->name ?? 'Our collection') }}</p>
                    <h2 id="product-list-heading"
                        class="mt-1 text-2xl font-black tracking-tight text-stone-900 sm:text-3xl">Products to explore</h2>
                </div>
                <p class="text-sm font-medium text-stone-500">
                    {{ $products->total() }} {{ \Illuminate\Support\Str::plural('item', $products->total()) }}
                </p>
            </div>

            @if ($products->isNotEmpty())
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($products as $product)
                        <article
                            class="group overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                            <a href="{{ route('shop.products.show', ['product' => $product->slug]) }}" class="block"
                                aria-label="View {{ $product->name }}">
                                <div
                                    class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-lime-100 via-amber-50 to-orange-100">
                                    @if ($product->image_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image_path) }}"
                                            alt="{{ $product->name }}" loading="lazy"
                                            class="size-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div
                                            class="flex size-full flex-col items-center justify-center gap-3 text-emerald-950/70">
                                            <span
                                                class="flex size-16 items-center justify-center rounded-3xl bg-white/70 text-3xl shadow-sm"
                                                aria-hidden="true">✳</span>
                                            <span
                                                class="text-xs font-bold uppercase tracking-[0.18em]">{{ $product->subcategory->name }}</span>
                                        </div>
                                    @endif
                                    <span
                                        class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1.5 text-xs font-bold text-emerald-950 shadow-sm">{{ $product->category->name }}</span>
                                </div>
                                <div class="p-5">
                                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-stone-400">
                                        {{ $product->subcategory->name }}</p>
                                    <h3
                                        class="mt-2 min-h-14 text-lg font-extrabold leading-6 text-stone-900 group-hover:text-emerald-800">
                                        {{ $product->name }}</h3>
                                    <p class="mt-2 line-clamp-2 min-h-10 text-sm leading-5 text-stone-500">
                                        {{ $product->description }}</p>
                                    <div class="mt-5 flex items-center justify-between gap-3">
                                        <div class="flex flex-wrap items-baseline gap-2">
                                            <span
                                                class="text-lg font-black text-emerald-950">৳{{ number_format((float) $product->new_price, 2) }}</span>
                                            @if ($product->old_price)
                                                <span
                                                    class="text-sm font-medium text-stone-400 line-through">৳{{ number_format((float) $product->old_price, 2) }}</span>
                                            @endif
                                        </div>
                                        <span
                                            class="flex size-9 items-center justify-center rounded-full bg-emerald-50 font-bold text-emerald-900 transition group-hover:bg-emerald-900 group-hover:text-white"
                                            aria-hidden="true">↗</span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>

                <div class="mt-9">
                    {{ $products->links() }}
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-stone-300 bg-white px-6 py-16 text-center">
                    <span
                        class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-lime-100 text-2xl text-emerald-900"
                        aria-hidden="true">✳</span>
                    <h3 class="mt-5 text-xl font-extrabold text-stone-900">No products here yet</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-500">Try another category, or come back
                        after the shop has been stocked.</p>
                    @if ($activeCategory)
                        <a href="{{ route('shop.index') }}"
                            class="mt-6 inline-flex rounded-full bg-emerald-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-800">Browse
                            all products</a>
                    @endif
                </div>
            @endif
        </section>
    </div>
@endsection
