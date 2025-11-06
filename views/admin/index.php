<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Commandes Nightbot</h2>
        <a href="/admin/command/new" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-white font-medium rounded-lg transition-colors">
            Nouvelle commande
        </a>
    </div>

    <?php if (empty($commands)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
            <p class="text-gray-500 dark:text-gray-400">Aucune commande créée pour le moment.</p>
        </div>
    <?php else: ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Créée le</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($commands as $command): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="px-2 py-1 bg-gray-100 dark:bg-gray-900 text-purple-600 dark:text-purple-400 rounded text-sm font-mono">!<?= htmlspecialchars($command->name) ?></code>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100"><?= htmlspecialchars($command->description ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= date('d/m/Y H:i', strtotime($command->created_at)) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="/admin/command/<?= $command->id ?>" class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors">Éditer</a>
                                <form method="POST" action="/admin/command/<?= $command->id ?>/delete" class="inline">
                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded transition-colors" onclick="return confirm('Supprimer cette commande ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
