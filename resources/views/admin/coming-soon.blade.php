@extends('layouts.shop')

@section('title', 'Admin area')

@section('content')
    <section class="mx-auto flex min-h-[65vh] max-w-3xl flex-col items-center justify-center px-5 py-16 text-center sm:px-8">
        <span class="flex size-16 items-center justify-center rounded-3xl bg-lime-100 text-2xl font-black text-emerald-950"
            aria-hidden="true">D</span>
        <p class="mt-6 text-xs font-bold uppercase tracking-[0.2em] text-emerald-800">Store management</p>
        <h1 class="mt-3 text-3xl font-black tracking-tight text-stone-900 sm:text-4xl">The admin workspace is up next</h1>
        <p class="mt-4 max-w-xl text-base leading-7 text-stone-600">This is the entry point for managing categories,
            subcategories, and products. The management screens will be added in the next step.</p>
        <a href="{{ route('shop.index') }}"
            class="mt-8 inline-flex rounded-full bg-emerald-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-emerald-800">Return
            to the shop</a>
    </section>
@endsection
