<div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?= $command === null ? 'Nouvelle commande' : 'Éditer la commande' ?></h2>

    <?php if (isset($error)): ?>
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 rounded">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= $command === null ? '/admin/command' : '/admin/command/' . $command->id ?>" id="commandForm" class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nom de la commande</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        required
                        placeholder="random"
                        value="<?= htmlspecialchars($command->name ?? '') ?>"
                        class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                    >
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Le nom utilisé dans Nightbot (sans le !)</p>
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <input
                        type="text"
                        id="description"
                        name="description"
                        placeholder="Tire un nombre aléatoire"
                        value="<?= htmlspecialchars($command->description ?? '') ?>"
                        class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                    >
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4">
            <div>
                <label for="editor" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Code JavaScript</label>
                <div id="editor" class="border-2 border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden" style="height: 400px;"></div>
                <textarea id="code" name="code" style="display: none;"><?= htmlspecialchars($command->code ?? '') ?></textarea>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Variables disponibles : <code class="px-2 py-1 bg-gray-100 dark:bg-gray-900 text-purple-600 dark:text-purple-400 rounded text-xs font-mono">args</code> (array des arguments passés après la commande)
                </p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Exemple :</p>
                <pre class="text-xs text-gray-800 dark:text-gray-200 overflow-x-auto"><code>// args[0] = min, args[1] = max
const min = parseInt(args[0]) || 1;
const max = parseInt(args[1]) || 100;
const random = Math.floor(Math.random() * (max - min + 1)) + min;
return `Nombre aléatoire : ${random}`;</code></pre>
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-white font-medium rounded-lg transition-colors">
                Enregistrer
            </button>
            <a href="/admin" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition-colors">
                Annuler
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs/loader.js"></script>
<script>
    require.config({ paths: { vs: 'https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs' }});

    require(['vs/editor/editor.main'], function() {
        // Détecter le thème actuel
        const isDark = document.documentElement.classList.contains('dark');

        const editor = monaco.editor.create(document.getElementById('editor'), {
            value: document.getElementById('code').value,
            language: 'javascript',
            theme: isDark ? 'vs-dark' : 'vs',
            automaticLayout: true,
            minimap: { enabled: false },
            fontSize: 14,
            lineNumbers: 'on',
            roundedSelection: false,
            scrollBeyondLastLine: false,
        });

        // Sync editor content with hidden textarea before form submit
        document.getElementById('commandForm').addEventListener('submit', function() {
            document.getElementById('code').value = editor.getValue();
        });

        // Observer pour détecter les changements de thème
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    const isDark = document.documentElement.classList.contains('dark');
                    monaco.editor.setTheme(isDark ? 'vs-dark' : 'vs');
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    });
</script>
