<div class="container">
    <div class="header">
        <h2>Quotes</h2>
        <a href="/admin/quote/new" class="btn btn-primary">Nouvelle quote</a>
    </div>

    <?php if (empty($quotes)): ?>
        <p class="empty-state">Aucune quote créée pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Éléments</th>
                    <th>Token</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotes as $quote): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($quote->name) ?></code></td>
                        <td><?= htmlspecialchars($quote->description ?? '-') ?></td>
                        <td><?= $quote->getItemCount() ?> élément(s)</td>
                        <td><code class="token"><?= htmlspecialchars($quote->token) ?></code></td>
                        <td>
                            <a href="/admin/quote/<?= $quote->id ?>" class="btn btn-small">Gérer</a>
                            <form method="POST" action="/admin/quote/<?= $quote->id ?>/delete" style="display: inline;">
                                <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Supprimer cette quote ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="info-box" style="margin-top: 2rem;">
        <h3>Utilisation de l'API Quotes</h3>
        <p>Pour utiliser une quote depuis Nightbot ou une commande :</p>
        <pre><code>// Récupérer un élément aléatoire
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

<style>
.token {
    font-size: 0.85rem;
    background: #f0f0f0;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
    font-family: monospace;
}

.info-box {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.info-box h3 {
    margin-top: 0;
    color: #2c3e50;
}

.info-box pre {
    background: white;
    padding: 1rem;
    border-radius: 4px;
    overflow-x: auto;
}
</style>
