@extends('layouts.shop')

@section('title', $product->name)

@section('content')
    <div class="mx-auto grid max-w-7xl gap-8 px-5 py-8 sm:px-8 sm:py-12 lg:grid-cols-[230px_minmax(0,1fr)] lg:gap-10">
        <aside aria-label="Browse product categories"
            class="self-start rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-base font-extrabold text-stone-900">Shop by category</h2>
            <a href="{{ route('shop.index') }}"
                class="block rounded-xl px-3 py-2.5 text-sm font-bold text-stone-600 transition hover:bg-stone-50 hover:text-emerald-950">All
                products</a>
            <ul class="mt-2 space-y-1">
                @foreach ($categories as $category)
                    <li>
                        <a href="{{ route('shop.categories.show', ['category' => $category->slug]) }}"
                            class="block rounded-xl px-3 py-2.5 text-sm font-bold text-stone-700 transition hover:bg-stone-50 hover:text-emerald-950">{{ $category->name }}</a>
                        <ul class="ml-4 border-l border-stone-200 pl-3">
                            @foreach ($category->subcategories as $subcategory)
                                <li><a href="{{ route('shop.subcategories.show', ['subcategory' => $subcategory->slug]) }}"
                                        class="block rounded-lg px-2.5 py-2 text-sm text-stone-500 transition hover:text-emerald-900">{{ $subcategory->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </aside>

        <article class="min-w-0">
            <a href="{{ route('shop.index') }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-emerald-900 transition hover:text-emerald-700"><span
                    aria-hidden="true">←</span> Back to shop</a>
            <div class="mt-6 grid overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm md:grid-cols-2">
                <div class="min-h-72 bg-gradient-to-br from-lime-100 via-amber-50 to-orange-100 md:min-h-[500px]">
                    @if ($product->image_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image_path) }}"
                            alt="{{ $product->name }}" class="size-full min-h-72 object-cover md:min-h-[500px]">
                    @else
                        <div
                            class="flex size-full min-h-72 flex-col items-center justify-center gap-4 text-emerald-950/70 md:min-h-[500px]">
                            <span
                                class="flex size-24 items-center justify-center rounded-[2rem] bg-white/70 text-5xl shadow-sm"
                                aria-hidden="true">✳</span>
                            <span
                                class="text-sm font-bold uppercase tracking-[0.18em]">{{ $product->subcategory->name }}</span>
                        </div>
                    @endif
                </div>
                <div class="flex flex-col justify-center p-6 sm:p-10">
                    <div class="flex flex-wrap gap-2 text-xs font-bold uppercase tracking-[0.12em]">
                        <a href="{{ route('shop.categories.show', ['category' => $product->category->slug]) }}"
                            class="rounded-full bg-emerald-50 px-3 py-1.5 text-emerald-900">{{ $product->category->name }}</a>
                        <a href="{{ route('shop.subcategories.show', ['subcategory' => $product->subcategory->slug]) }}"
                            class="rounded-full bg-stone-100 px-3 py-1.5 text-stone-600">{{ $product->subcategory->name }}</a>
                    </div>
                    <h1 class="mt-5 text-3xl font-black tracking-tight text-stone-900 sm:text-4xl">{{ $product->name }}
                    </h1>
                    <div class="mt-5 flex flex-wrap items-baseline gap-3">
                        <span
                            class="text-2xl font-black text-emerald-950">৳{{ number_format((float) $product->new_price, 2) }}</span>
                        @if ($product->old_price)
                            <span
                                class="text-base font-semibold text-stone-400 line-through">৳{{ number_format((float) $product->old_price, 2) }}</span>
                        @endif
                    </div>
                    <div class="mt-7 border-t border-stone-200 pt-6">
                        <h2 class="text-sm font-extrabold uppercase tracking-[0.12em] text-stone-700">About this product
                        </h2>
                        <p class="mt-3 whitespace-pre-line text-base leading-7 text-stone-600">{{ $product->description }}
                        </p>
                    </div>
                </div>
            </div>
        </article>
    </div>
@endsection
