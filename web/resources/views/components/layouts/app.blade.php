<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{darkMode: localStorage.getItem('theme') || localStorage.setItem('theme', 'system')}"
      x-init="$watch('darkMode', val => localStorage.setItem('theme', val))"
      x-bind:class="{
        'dark': darkMode === 'dark' || (darkMode === 'system' && window.matchMedia('(prefers-color-scheme: dark)')
            .matches)
      }">
<head>
    <meta charset="utf-8">

    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles
    @vite(['resources/css/app.css'])
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-950 dark:text-white font-normal">

{{ $slot }}

@livewire('notifications')

@vite(['resources/js/app.js'])
@filamentScripts
</body>
</html>
