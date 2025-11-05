<div class="container">
    <h2><?= $quote === null ? 'Nouvelle quote' : 'Gérer la quote : ' . htmlspecialchars($quote->name) ?></h2>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= $quote === null ? '/admin/quote' : '/admin/quote/' . $quote->id ?>">
        <div class="form-group">
            <label for="name">Nom de la quote</label>
            <input
                type="text"
                id="name"
                name="name"
                required
                placeholder="myquote"
                value="<?= htmlspecialchars($quote->name ?? '') ?>"
                <?= $quote !== null ? 'readonly' : '' ?>
            >
            <small>Le nom utilisé dans l'API (ne peut pas être modifié après création)</small>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <input
                type="text"
                id="description"
                name="description"
                placeholder="Description de la quote"
                value="<?= htmlspecialchars($quote->description ?? '') ?>"
            >
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/admin/quotes" class="btn">Annuler</a>
        </div>
    </form>

    <?php if ($quote !== null): ?>
        <hr style="margin: 3rem 0; border: none; border-top: 1px solid #ddd;">

        <h3>Éléments de la quote</h3>

        <div class="quote-token-info">
            <strong>Token :</strong> <code><?= htmlspecialchars($quote->token) ?></code>
            <small>Utilisez ce token pour ajouter/supprimer des éléments via l'API</small>
        </div>

        <form method="POST" action="/admin/quote/<?= $quote->id ?>/item" class="add-item-form">
            <div class="form-group">
                <label for="value">Ajouter un nouvel élément</label>
                <div style="display: flex; gap: 1rem;">
                    <input
                        type="text"
                        id="value"
                        name="value"
                        placeholder="Contenu de l'élément"
                        style="flex: 1;"
                        required
                    >
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </div>
        </form>

        <?php if (empty($items)): ?>
            <p class="empty-state">Aucun élément dans cette quote.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Valeur</th>
                        <th>Créé le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= htmlspecialchars($item['value']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                            <td>
                                <form method="POST" action="/admin/quote/<?= $quote->id ?>/item/<?= $item['id'] ?>/delete" style="display: inline;">
                                    <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Supprimer cet élément ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.quote-token-info {
    background: #fff3cd;
    border: 1px solid #ffc107;
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1.5rem;
}

.quote-token-info code {
    background: white;
    padding: 0.2rem 0.5rem;
    border-radius: 3px;
    font-family: monospace;
}

.quote-token-info small {
    display: block;
    margin-top: 0.5rem;
    color: #856404;
}

.add-item-form {
    margin-bottom: 2rem;
}

hr {
    margin: 2rem 0;
}
</style>
