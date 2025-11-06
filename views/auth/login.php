<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nightbot Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script>
        // Theme management
        const getTheme = () => localStorage.getItem('theme') || 'system';
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
<body class="bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen flex items-center justify-center transition-all duration-300">
    <div class="w-full max-w-md px-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 transition-colors">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Nightbot Admin</h1>
                <p class="text-gray-600 dark:text-gray-400">Connexion</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 rounded">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/login" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Nom d'utilisateur
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        required
                        autofocus
                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-all"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Mot de passe
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-all"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-purple-600 hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors shadow-lg hover:shadow-xl"
                >
                    Se connecter
                </button>
            </form>
        </div>
    </div>
</body>
</html>
