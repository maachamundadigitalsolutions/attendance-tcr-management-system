<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hello World + Counter</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @livewireStyles
</head>
<body>
    <div style="text-align:center; margin-top:50px;">
        <h1>Hello World</h1>

        {{-- Livewire Counter Component --}}
        @livewire('counter')
        <h1>Hello World</h1>
        <!-- <a href="{{ route('login') }}">Go to Login</a> -->
        <a href="{{ route('login') }}" wire:navigate>Go to Login</a>

    </div>

    @livewireScripts
</body>
</html>
