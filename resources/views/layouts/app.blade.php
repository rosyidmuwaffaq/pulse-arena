<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pulse Arena')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('css/pulse.css') }}" rel="stylesheet">

    <script>
        // Baca tema yang tersimpan
        (function () {
            const savedTheme = localStorage.getItem('pulse-theme');

            if (savedTheme === 'light') {
                document.documentElement.classList.add('light-mode');
            }
        })();
    </script>
</head>

<body>

    <header class="container topbar">

        <a href="{{ route('events.index') }}" class="logo">
            PULSE<span>/ARENA</span>
        </a>

        <nav>
            <a href="{{ route('events.index') }}">Event</a>
            <a href="{{ route('tickets.index') }}">Tiket Saya</a>

            <button
                type="button"
                id="themeToggle"
                class="theme-toggle"
                aria-label="Gunakan mode terang"
                title="Gunakan mode terang"
            >
                ☀️
            </button>
        </nav>

    </header>

    <main class="container pb-5">
        @yield('content')
    </main>

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="{{ asset('js/pulse.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const themeToggle = document.getElementById('themeToggle');

            if (!themeToggle) {
                return;
            }

            function updateThemeButton() {
                const isLight =
                    document.documentElement.classList.contains('light-mode');

                if (isLight) {
                    themeToggle.textContent = '🌙';
                    themeToggle.setAttribute(
                        'aria-label',
                        'Gunakan mode malam'
                    );
                    themeToggle.setAttribute(
                        'title',
                        'Gunakan mode malam'
                    );
                } else {
                    themeToggle.textContent = '☀️';
                    themeToggle.setAttribute(
                        'aria-label',
                        'Gunakan mode terang'
                    );
                    themeToggle.setAttribute(
                        'title',
                        'Gunakan mode terang'
                    );
                }
            }

            // Sesuaikan ikon dengan tema
            updateThemeButton();

            themeToggle.addEventListener('click', function () {

                document.documentElement.classList.toggle('light-mode');

                const isLight =
                    document.documentElement.classList.contains('light-mode');

                localStorage.setItem(
                    'pulse-theme',
                    isLight ? 'light' : 'dark'
                );

                updateThemeButton();
            });

        });
    </script>

    @stack('scripts')

</body>
</html>