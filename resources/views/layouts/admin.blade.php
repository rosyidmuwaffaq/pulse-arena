<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin — Pulse Arena')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('css/pulse.css') }}" rel="stylesheet">
</head>
<body>
    <header class="container topbar">
        <a href="{{ route('admin.events.index') }}" class="logo">PULSE<span>/ADMIN</span></a>
        <nav>
            <a href="{{ route('admin.events.index') }}">Kelola Event</a>
            <a href="{{ route('admin.events.create') }}">Tambah Event</a>
        </nav>
    </header>
    <main class="container pb-5">
        @yield('content')
    </main>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="{{ asset('js/pulse.js') }}"></script>
    @stack('scripts')
</body>
</html>
