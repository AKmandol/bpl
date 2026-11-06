<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Browse useful everyday products at Dokan Market.">
    <title>@yield('title', 'Dokan Market') · Dokan Market</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-stone-50 text-stone-900 antialiased">
    <header class="border-b border-stone-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 py-4 sm:px-8">
            <a href="{{ route('shop.index') }}" class="flex items-center gap-3" aria-label="Dokan Market home">
                <span
                    class="flex size-11 items-center justify-center rounded-2xl bg-emerald-900 text-xl font-black text-lime-300">D</span>
                <span>
                    <span class="block text-lg font-black tracking-tight text-emerald-950">Dokan Market</span>
                    <span class="block text-xs font-medium tracking-wide text-stone-500">GOOD FINDS, EVERY DAY</span>
                </span>
            </a>

            <nav aria-label="Main navigation" class="flex items-center gap-3 sm:gap-6">
                <a href="{{ route('shop.index') }}"
                    class="hidden text-sm font-semibold text-stone-600 transition hover:text-emerald-900 sm:inline">Shop</a>
                <a href="{{ route('admin.home') }}"
                    class="rounded-full border border-emerald-900 px-4 py-2 text-sm font-bold text-emerald-950 transition hover:bg-emerald-900 hover:text-white sm:px-5">
                    Go to admin
                </a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-stone-200 bg-white">
        <div
            class="mx-auto flex max-w-7xl flex-col gap-2 px-5 py-7 text-sm text-stone-500 sm:flex-row sm:items-center sm:justify-between sm:px-8">
            <p class="font-semibold text-stone-700">Dokan Market</p>
            <p>Thoughtful picks for everyday life.</p>
        </div>
    </footer>
</body>

</html>
