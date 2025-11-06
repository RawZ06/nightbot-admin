<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Quotes</h2>
        <a href="/admin/quote/new" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-white font-medium rounded-lg transition-colors">
            Nouvelle quote
        </a>
    </div>

    <?php if (empty($quotes)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
            <p class="text-gray-500 dark:text-gray-400">Aucune quote créée pour le moment.</p>
        </div>
    <?php else: ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Éléments</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Token</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($quotes as $quote): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="px-2 py-1 bg-gray-100 dark:bg-gray-900 text-purple-600 dark:text-purple-400 rounded text-sm font-mono"><?= htmlspecialchars($quote->name) ?></code>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100"><?= htmlspecialchars($quote->description ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= $quote->getItemCount() ?> élément(s)</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="px-2 py-1 bg-gray-100 dark:bg-gray-900 text-xs text-gray-600 dark:text-gray-300 rounded font-mono"><?= htmlspecialchars($quote->token) ?></code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="/admin/quote/<?= $quote->id ?>" class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors">Gérer</a>
                                <form method="POST" action="/admin/quote/<?= $quote->id ?>/delete" class="inline">
                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded transition-colors" onclick="return confirm('Supprimer cette quote ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Utilisation de l'API Quotes</h3>
        <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">Pour utiliser une quote depuis Nightbot ou une commande :</p>
        <pre class="bg-white dark:bg-gray-800 p-4 rounded-lg overflow-x-auto text-xs text-gray-800 dark:text-gray-200"><code>// Récupérer un élément aléatoire
GET /api/quote/{name}/random

// Récupérer tous les éléments
GET /api/quote/{name}/all

// Ajouter un élément (nécessite le token)
POST /api/quote/{name}/add
Body: { "value": "...", "token": "..." }

// Supprimer un élément (nécessite le token)
POST /api/quote/{name}/remove
Body: { "item_id": 123, "token": "..." }</code></pre>
    </div>
</div>
