<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nightbot Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script>
        // Theme management
        const getTheme = () => localStorage.getItem('theme') || 'system';
        const setTheme = (theme) => {
            localStorage.setItem('theme', theme);
            applyTheme(theme);
        };
        const applyTheme = (theme) => {
            if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        };
        // Apply theme before page renders
        applyTheme(getTheme());
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen transition-colors">
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-8">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Nightbot Admin</h1>
                    <div class="flex space-x-1">
                        <a href="/admin/commands" class="px-3 py-2 rounded-md text-sm font-medium transition-colors <?= ($page ?? '') === 'commands' ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?>">Commandes</a>
                        <a href="/admin/quotes" class="px-3 py-2 rounded-md text-sm font-medium transition-colors <?= ($page ?? '') === 'quotes' ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?>">Quotes</a>
                        <a href="/admin/help" class="px-3 py-2 rounded-md text-sm font-medium transition-colors <?= ($page ?? '') === 'help' ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?>">Aide</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Theme Switcher -->
                    <div class="relative">
                        <button id="theme-button" class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg id="theme-icon-light" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <svg id="theme-icon-dark" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            <svg id="theme-icon-system" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                        <div id="theme-menu" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50">
                            <div class="py-1">
                                <button data-theme="light" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Clair</button>
                                <button data-theme="dark" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Sombre</button>
                                <button data-theme="system" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Système</button>
                            </div>
                        </div>
                    </div>
                    <?php if (\App\Auth::check()): ?>
                        <span class="text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars(\App\Auth::getUser()) ?></span>
                        <a href="/logout" class="px-3 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors">Déconnexion</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php require __DIR__ . '/' . $view . '.php'; ?>
    </main>

    <script>
        // Theme switcher logic
        const themeButton = document.getElementById('theme-button');
        const themeMenu = document.getElementById('theme-menu');
        const themeButtons = document.querySelectorAll('[data-theme]');
        const icons = {
            light: document.getElementById('theme-icon-light'),
            dark: document.getElementById('theme-icon-dark'),
            system: document.getElementById('theme-icon-system')
        };

        const updateIcon = (theme) => {
            Object.values(icons).forEach(icon => icon.classList.add('hidden'));
            icons[theme].classList.remove('hidden');
        };

        themeButton.addEventListener('click', () => {
            themeMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!themeButton.contains(e.target) && !themeMenu.contains(e.target)) {
                themeMenu.classList.add('hidden');
            }
        });

        themeButtons.forEach(button => {
            button.addEventListener('click', () => {
                const theme = button.dataset.theme;
                setTheme(theme);
                updateIcon(theme);
                themeMenu.classList.add('hidden');
            });
        });

        // Initialize icon
        updateIcon(getTheme());

        // Listen to system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (getTheme() === 'system') {
                applyTheme('system');
            }
        });
    </script>
</body>
</html>
