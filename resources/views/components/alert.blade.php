@php
    $styles = [
        'success' => 'border-green-200 bg-green-50 text-green-800 dark:border-green-500/30 dark:bg-green-500/10 dark:text-green-200',
        'danger' => 'border-red-200 bg-red-50 text-red-800 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-200',
        'error' => 'border-red-200 bg-red-50 text-red-800 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-200',
        'warning' => 'border-yellow-200 bg-yellow-50 text-yellow-800 dark:border-yellow-500/30 dark:bg-yellow-500/10 dark:text-yellow-200',
        'info' => 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200',
    ];

    $alertClass = $styles[$type] ?? $styles['info'];
@endphp

@if ($hasMessages())
    <div {{ $attributes->class(['rounded-xl border px-4 py-3 text-sm shadow-theme-xs', $alertClass]) }}>
        @if ($message !== '')
            <p class="font-medium">{{ $message }}</p>
        @endif

        @if ($messages !== [])
            <ul class="{{ $message !== '' ? 'mt-2 list-disc space-y-1 pl-5' : 'list-disc space-y-1 pl-5' }}">
                @foreach ($messages as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        @endif

        @if (trim((string) $slot) !== '')
            <div class="{{ $message !== '' || $messages !== [] ? 'mt-2' : '' }}">
                {{ $slot }}
            </div>
        @endif
    </div>
@endif
