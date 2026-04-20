@props([
    'variant' => 'sidebar',
    'title' => 'Vo Duy Binh',
    'subtitle' => 'Admin Workspace',
])

@php
    $photoRelativePath = 'images/logo/profile-logo.jpg';
    $photoExists = file_exists(public_path($photoRelativePath));
    $photoUrl = asset($photoRelativePath);
    $initials = collect(explode(' ', trim($title)))
        ->filter()
        ->take(2)
        ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

@if ($variant === 'sidebar')
    <div class="app-logo-shell">
        <div class="app-logo-avatar app-logo-avatar-sidebar">
            @if ($photoExists)
                <img src="{{ $photoUrl }}" alt="{{ $title }}" class="app-logo-image" />
            @else
                <span class="app-logo-fallback">{{ $initials }}</span>
            @endif
        </div>
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $title }}</p>
            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
        </div>
    </div>
@elseif ($variant === 'sidebar-compact')
    <div class="app-logo-avatar app-logo-avatar-compact">
        @if ($photoExists)
            <img src="{{ $photoUrl }}" alt="{{ $title }}" class="app-logo-image" />
        @else
            <span class="app-logo-fallback app-logo-fallback-compact">{{ $initials }}</span>
        @endif
    </div>
@elseif ($variant === 'mobile')
    <div class="app-logo-shell app-logo-shell-mobile">
        <div class="app-logo-avatar app-logo-avatar-mobile">
            @if ($photoExists)
                <img src="{{ $photoUrl }}" alt="{{ $title }}" class="app-logo-image" />
            @else
                <span class="app-logo-fallback app-logo-fallback-compact">{{ $initials }}</span>
            @endif
        </div>
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $title }}</p>
        </div>
    </div>
@elseif ($variant === 'auth')
    <div class="flex flex-col items-center gap-4">
        <div class="app-logo-avatar app-logo-avatar-auth">
            @if ($photoExists)
                <img src="{{ $photoUrl }}" alt="{{ $title }}" class="app-logo-image" />
            @else
                <span class="app-logo-fallback app-logo-fallback-auth">{{ $initials }}</span>
            @endif
        </div>
        <div class="space-y-1 text-center">
            <p class="text-base font-semibold text-white">{{ $title }}</p>
            <p class="text-sm text-gray-300">{{ $subtitle }}</p>
        </div>
    </div>
@endif
