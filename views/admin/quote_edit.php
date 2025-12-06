<div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?= $quote === null ? 'Nouvelle quote' : 'Gérer la quote : ' . htmlspecialchars($quote->name) ?></h2>

    <?php if (isset($error)): ?>
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 rounded">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= $quote === null ? '/admin/quote' : '/admin/quote/' . $quote->id ?>" class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4">
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nom de la quote</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    required
                    placeholder="myquote"
                    value="<?= htmlspecialchars($quote->name ?? '') ?>"
                    <?= $quote !== null ? 'readonly' : '' ?>
                    class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all <?= $quote !== null ? 'opacity-50 cursor-not-allowed' : '' ?>"
                >
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Le nom utilisé dans l'API (ne peut pas être modifié après création)</p>
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <input
                    type="text"
                    id="description"
                    name="description"
                    placeholder="Description de la quote"
                    value="<?= htmlspecialchars($quote->description ?? '') ?>"
                    class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                >
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-white font-medium rounded-lg transition-colors">
                Enregistrer
            </button>
            <a href="/admin/quotes" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition-colors">
                Annuler
            </a>
        </div>
    </form>

    <?php if ($quote !== null): ?>
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Éléments de la quote</h3>
                <?php if (!empty($items)): ?>
                    <form method="POST" action="/admin/quote/<?= $quote->id ?>/items/clear" class="inline">
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors" onclick="return confirm('Supprimer TOUS les éléments de cette quote ?')">
                            Supprimer tout
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <form method="POST" action="/admin/quote/<?= $quote->id ?>/item" class="mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <label for="value" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ajouter un nouvel élément</label>
                    <div class="flex gap-4">
                        <input
                            type="text"
                            id="value"
                            name="value"
                            placeholder="Contenu de l'élément"
                            required
                            class="flex-1 px-4 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                        >
                        <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-white font-medium rounded-lg transition-colors">
                            Ajouter
                        </button>
                    </div>
                </div>
            </form>

            <?php if (empty($items)): ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Aucun élément dans cette quote.</p>
                </div>
            <?php else: ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valeur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Créé le</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($items as $item): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" id="item-row-<?= $item['id'] ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= $item['id'] ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="item-display-<?= $item['id'] ?> text-gray-900 dark:text-gray-100"><?= htmlspecialchars($item['value']) ?></div>
                                        <form method="POST" action="/admin/quote/<?= $quote->id ?>/item/<?= $item['id'] ?>/update" class="item-edit-<?= $item['id'] ?> hidden">
                                            <input
                                                type="text"
                                                name="value"
                                                value="<?= htmlspecialchars($item['value']) ?>"
                                                class="w-full px-3 py-1 rounded border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                            >
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex gap-2 justify-end">
                                            <button type="button" onclick="toggleEdit(<?= $item['id'] ?>)" class="item-edit-btn-<?= $item['id'] ?> inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors">
                                                Modifier
                                            </button>
                                            <button type="button" onclick="saveEdit(<?= $item['id'] ?>)" class="item-save-btn-<?= $item['id'] ?> hidden inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded transition-colors">
                                                Enregistrer
                                            </button>
                                            <button type="button" onclick="cancelEdit(<?= $item['id'] ?>, '<?= htmlspecialchars($item['value'], ENT_QUOTES) ?>')" class="item-cancel-btn-<?= $item['id'] ?> hidden inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white rounded transition-colors">
                                                Annuler
                                            </button>
                                            <form method="POST" action="/admin/quote/<?= $quote->id ?>/item/<?= $item['id'] ?>/delete" class="inline">
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded transition-colors" onclick="return confirm('Supprimer cet élément ?')">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <script>
                function toggleEdit(itemId) {
                    document.querySelector('.item-display-' + itemId).classList.add('hidden');
                    document.querySelector('.item-edit-' + itemId).classList.remove('hidden');
                    document.querySelector('.item-edit-btn-' + itemId).classList.add('hidden');
                    document.querySelector('.item-save-btn-' + itemId).classList.remove('hidden');
                    document.querySelector('.item-cancel-btn-' + itemId).classList.remove('hidden');
                }

                function cancelEdit(itemId, originalValue) {
                    document.querySelector('.item-display-' + itemId).classList.remove('hidden');
                    document.querySelector('.item-edit-' + itemId).classList.add('hidden');
                    document.querySelector('.item-edit-btn-' + itemId).classList.remove('hidden');
                    document.querySelector('.item-save-btn-' + itemId).classList.add('hidden');
                    document.querySelector('.item-cancel-btn-' + itemId).classList.add('hidden');
                    document.querySelector('.item-edit-' + itemId + ' input').value = originalValue;
                }

                function saveEdit(itemId) {
                    document.querySelector('.item-edit-' + itemId).submit();
                }
                </script>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
