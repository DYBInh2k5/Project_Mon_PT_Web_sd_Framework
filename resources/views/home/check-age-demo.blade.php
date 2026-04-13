<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CheckAge Demo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="subtle-gradient min-h-screen">
    <main class="mx-auto flex min-h-screen max-w-4xl items-center px-4 py-10">
        <section class="w-full space-y-6 rounded-[32px] border border-white/70 bg-white/90 p-8 shadow-theme-xl backdrop-blur-sm dark:border-white/10 dark:bg-gray-900/90">
            <div class="space-y-3">
                <span class="toolbar-chip">Middleware demo</span>
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">Trang test middleware `CheckAge`</h1>
                    <p class="mt-2 text-sm leading-7 text-gray-600 dark:text-gray-300">
                        Day la trang minh hoa middleware co ban theo yeu cau cua co. Neu tuoi nho hon <strong>200</strong> thi vao duoc trang nay,
                        neu lon hon hoac bang <strong>200</strong> thi se bi chuyen sang <code>/check_fail</code>.
                    </p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <a href="{{ url('/check_age/18') }}" class="action-button-primary">
                    Test /check_age/18
                </a>
                <a href="{{ url('/check_age/200') }}" class="action-button-danger">
                    Test /check_age/200
                </a>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl bg-brand-50 p-5 dark:bg-brand-500/10">
                    <p class="text-sm font-semibold text-brand-700 dark:text-brand-300">URL hien tai</p>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ request()->path() }}</p>
                </div>
                <div class="rounded-2xl bg-gray-100 p-5 dark:bg-gray-800">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Tuoi dang test</p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ request()->route('age') ?? 'Khong co tham so tuoi' }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ url('/check_fail') }}" class="action-button">Mo /check_fail</a>
                <a href="{{ url('/') }}" class="action-button">Ve trang chu</a>
            </div>
        </section>
    </main>
</body>
</html>
